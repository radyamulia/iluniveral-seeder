<?php

namespace App\Http\Controllers;

use App\Exports\DosenExport;
use App\Exports\JenjangExport;
use App\Exports\MahasiswaExport;
use App\Exports\MataKuliahExport;
use App\Exports\ProdiExport;
use App\Models\AktivitasMengajarDosen;
use App\Models\Dosen;
use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\MahasiswaLulusDO;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\RekapIPEPAMahasiswaDanLulusan;
use App\Models\RekapJumlahMahasiswa;
use App\Models\RiwayatFungsionalDosen;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    private $token;
    private $prodi;
    private $jenjang;
    private $mahasiswa;
    private $dosen;
    private $matakuliah;
    private $rekap_jumlah_mahasiswa;
    private $mahasiswa_lulus_do;
    private $rekap_ipepa_mahasiswa_dan_lulusan;
    private $aktivitas_mengajar_dosen;
    private $riwayat_fungsional_dosen;

    public function __construct()
    {
        $this->token = new Token();
        $this->prodi = new Prodi();
        $this->jenjang = new Jenjang();
        $this->mahasiswa = new Mahasiswa();
        $this->dosen = new Dosen();
        $this->matakuliah = new MataKuliah();
        $this->mahasiswa_lulus_do = new MahasiswaLulusDO();
        $this->rekap_jumlah_mahasiswa = new RekapJumlahMahasiswa();
        $this->rekap_ipepa_mahasiswa_dan_lulusan = new RekapIPEPAMahasiswaDanLulusan();
        $this->aktivitas_mengajar_dosen = new AktivitasMengajarDosen();
        $this->riwayat_fungsional_dosen = new RiwayatFungsionalDosen();
    }

    // ---------------- Mahasiswa -------------- 
    public function getAllMahasiswaFromDB(Request $request)
    {
        $search = $request->input('search');

        // Query with optional search filtering
        $list_mahasiswa = Mahasiswa::when($search, function ($query, $search) {
            $query->where('nama_mahasiswa', 'like', '%' . $search . '%')
                ->orWhere('nim', 'like', '%' . $search . '%')
                ->orWhereHas('prodi', function ($query) use ($search) {
                    $query->where('nama_program_studi', 'like', '%' . $search . '%')
                        ->orWhere('kode_program_studi', 'like', '%' . $search . '%')
                        ->orWhere('id_periode', 'like', '%' . $search . '%')
                        ->orWhereHas('jenjang_pendidikan', function ($query) use ($search) {
                            $query->where('nama_jenjang_didik', 'like', '%' . $search . '%');
                        });
                });
        })->paginate(25);

        return view('admin.mahasiswa', compact('list_mahasiswa'));
    }

    public function getAllMahasiswa()
    {
        // Attempt to get the data from the cache
        $list_mahasiswa = Cache::remember('mahasiswa_all_data', now()->addMinutes(5), function () {
            try {
                $data = $this->mahasiswa->getAllMhs($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch mata kuliah: ' . $e->getMessage());
                return null;
            }
        });

        // Manually paginate the data
        $currentPage = (int) request('page', 1); // Explicitly cast to integer
        $perPage = 50;
        $offset = ($currentPage - 1) * $perPage;

        // Slice the data for the current page
        $currentPageMahasiswa = array_slice($list_mahasiswa, $offset, $perPage);

        // Create a LengthAwarePaginator instance
        $paginatedData = new LengthAwarePaginator(
            $currentPageMahasiswa,
            count($list_mahasiswa), // Total number of records
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedData;
    }

    public function syncMahasiswa()
    {
        $cloud_data = $this->mahasiswa->getAllMhs($this->token->getToken());
        foreach ($cloud_data as $data) {
            $usableDate = date("Y-m-d", strtotime($data['tanggal_lahir']));
            // Use updateOrCreate to either update an existing record or create a new one
            Mahasiswa::updateOrCreate(
                ['id_mahasiswa' => $data['id_mahasiswa']],
                [
                    'id_registrasi_mahasiswa' => $data['id_registrasi_mahasiswa'],
                    'id_mahasiswa' => $data['id_mahasiswa'],
                    'id_sms' => $data['id_sms'],
                    'nim' => $data['nim'],
                    'nama_mahasiswa' => $data['nama_mahasiswa'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tanggal_lahir' => $usableDate,
                    'ipk' => $data['ipk'],
                    'nama_agama' => $data['nama_agama'],
                    'nama_status_mahasiswa' => $data['nama_status_mahasiswa'],
                    'id_periode' => $data['id_periode'],
                    'nama_periode_masuk' => $data['nama_periode_masuk'],
                    'id_prodi' => $data['id_prodi'],
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        return response()->json(['message' => 'Data synchronized successfully.'], 200);
    }

    public function exportCurrentMahasiswaToExcel(Request $request)
    {
        // Extract search filters and pagination from the request
        $search = $request->get('search', null);
        $page = $request->get('page', 1);

        // Rebuild the paginated query
        $list_mahasiswa = Mahasiswa::when($search, function ($query, $search) {
            $query->where('nama_mahasiswa', 'like', '%' . $search . '%')
                ->orWhere('nim', 'like', '%' . $search . '%')
                ->orWhereHas('prodi', function ($query) use ($search) {
                    $query->where('nama_program_studi', 'like', '%' . $search . '%')
                        ->orWhere('kode_program_studi', 'like', '%' . $search . '%')
                        ->orWhereHas('jenjang_pendidikan', function ($query) use ($search) {
                            $query->where('nama_jenjang_didik', 'like', '%' . $search . '%');
                        });
                });
        })->paginate(50, ['*'], 'page', $page);

        // Get the items for the current page
        $currentPageData = $list_mahasiswa->items();

        // Export the current page's data
        return Excel::download(new MahasiswaExport(collect($currentPageData)), 'mahasiswa_export.xlsx');
    }


    // ---------- Program Studi ----------------
    public function getAllProdiFromDB(Request $request)
    {
        $search = $request->input('search');

        // Query with optional search filtering
        $list_prodi = Prodi::when($search, function ($query, $search) {
            return $query->where('nama_program_studi', 'like', '%' . $search . '%');
        })->paginate(20);

        return view('admin.prodi', compact('list_prodi'));
    }

    public function getAllProdi()
    {
        $list_prodi = Cache::remember('prodi_all_data', now()->addMinutes(5), function () {
            try {
                $data = $this->prodi->getAllProdi($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch mata kuliah: ' . $e->getMessage());
                return null;
            }
        });
        return $list_prodi;
    }

    public function syncProdi(Request $request)
    {
        $cloud_data = $request->cloud_data;
        foreach ($cloud_data as $data) {
            // Use updateOrCreate to either update an existing record or create a new one
            Prodi::updateOrCreate(
                ['id_prodi' => $data['id_prodi']],
                [
                    'id_prodi' => $data['id_prodi'],
                    'kode_program_studi' => $data['kode_program_studi'],
                    'nama_program_studi' => $data['nama_program_studi'],
                    'status' => $data['status'],
                    'id_jenjang_didik' => $data['id_jenjang_pendidikan'],
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        return response()->json(['message' => 'Data synchronized successfully.'], 200);
    }

    public function exportCurrentProdiToExcel(Request $request)
    {
        // Extract search filters and pagination from the request
        $search = $request->get('search', null);
        $page = $request->get('page', 1);

        // Rebuild the paginated query
        $list_prodi = Prodi::when($search, function ($query, $search) {
            $query->where('nama_program_studi', 'like', '%' . $search . '%');
        })->paginate(20, ['*'], 'page', $page);

        // Get the items for the current page
        $currentPageData = $list_prodi->items();

        // Export the current page's data
        return Excel::download(new ProdiExport(collect($currentPageData)), 'program_studi_export.xlsx');
    }


    // ------- Jenjang Pendidikan -------------
    public function getAllJenjangFromDB(Request $request)
    {
        $search = $request->input('search');

        // Query with optional search filtering
        $list_jenjang = Jenjang::when($search, function ($query, $search) {
            return $query->where('nama_jenjang_didik', 'like', '%' . $search . '%');
        })->paginate(20);

        // $list_jenjang = Jenjang::paginate(15);
        return view('admin.jenjang', compact('list_jenjang'));
    }

    public function getAllJenjang()
    {
        $list_jenjang = Cache::remember('jenjang_all_data', now()->addMinutes(5), function () {
            try {
                $data = $this->jenjang->getAllJenjang($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch mata kuliah: ' . $e->getMessage());
                return null;
            }
        });
        return $list_jenjang;
    }

    public function syncJenjang(Request $request)
    {
        $cloud_data = $request->cloud_data;
        foreach ($cloud_data as $data) {
            // Use updateOrCreate to either update an existing record or create a new one
            Jenjang::updateOrCreate(
                ['id_jenjang_didik' => $data['id_jenjang_didik']], // Match the record based on a unique identifier (e.g., 'id')
                [
                    'nama_jenjang_didik' => $data['nama_jenjang_didik'],
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        return response()->json(['message' => 'Data synchronized successfully.'], 200);
    }

    public function exportCurrentJenjangToExcel(Request $request)
    {
        // Extract search filters and pagination from the request
        $search = $request->get('search', null);
        $page = $request->get('page', 1);

        // Rebuild the paginated query
        $list_jenjang = Jenjang::when($search, function ($query, $search) {
            $query->where('nama_jenjang_didik', 'like', '%' . $search . '%')
                ->orWhere('id_jenjang_didik', 'like', '%' . $search . '%');
        })->paginate(20, ['*'], 'page', $page);

        // Get the items for the current page
        $currentPageData = $list_jenjang->items();

        // Export the current page's data
        return Excel::download(new JenjangExport(collect($currentPageData)), 'jenjang_pendidikan_export.xlsx');
    }


    // ------- Dosen ----------
    public function getAllDosenFromDB(Request $request)
    {
        $search = $request->input('search');

        // Query with optional search filtering
        $list_dosen = Dosen::when($search, function ($query, $search) {
            return $query->where('nama_dosen', 'like', '%' . $search . '%')
                ->orWhere('nidn', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->orWhere('nama_status_aktif', 'like', '%' . $search . '%');
        })->paginate(20);

        return view('admin.dosen', compact('list_dosen'));
    }

    public function getAllDosen()
    {
        // Attempt to get the data from the cache
        $list_dosen = Cache::remember('dosen_all_data', now()->addMinutes(5), function () {
            try {
                $data = $this->dosen->getAllDosen($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch mata kuliah: ' . $e->getMessage());
                return null;
            }
        });

        // Manually paginate the data
        $currentPage = (int) request('page', 1); // Explicitly cast to integer
        $perPage = 50;
        $offset = ($currentPage - 1) * $perPage;

        // Slice the data for the current page
        $currentPageDosen = array_slice($list_dosen, $offset, $perPage);

        // Create a LengthAwarePaginator instance
        $paginatedData = new LengthAwarePaginator(
            $currentPageDosen,
            count($list_dosen), // Total number of records
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedData;
    }

    public function syncDosen(Request $request)
    {
        $cloud_data = $this->dosen->getAllDosen($this->token->getToken());
        foreach ($cloud_data as $data) {
            $usableDate = date("Y-m-d", strtotime($data['tanggal_lahir']));
            // Use updateOrCreate to either update an existing record or create a new one
            Dosen::updateOrCreate(
                ['id_dosen' => $data['id_dosen']], // Match the record based on a unique identifier (e.g., 'id')
                [
                    'nama_dosen' => $data['nama_dosen'],
                    'nidn' => $data['nidn'],
                    'nip' => $data['nip'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'nama_agama' => $data['nama_agama'],
                    'tanggal_lahir' => $usableDate,
                    'nama_status_aktif' => $data['nama_status_aktif'],
                    // 'id_dosen' => $data['id_dosen'],
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        return response()->json(['message' => 'Data synchronized successfully.'], 200);
    }

    public function exportCurrentDosenToExcel(Request $request)
    {
        // Extract search filters and pagination from the request
        $search = $request->get('search', null);
        $page = $request->get('page', 1);

        // Rebuild the paginated query
        $list_dosen = Dosen::when($search, function ($query, $search) {
            $query->where('nama_dosen', 'like', '%' . $search . '%')
                ->orWhere('id_jenjang_didik', 'like', '%' . $search . '%');
        })->paginate(20, ['*'], 'page', $page);

        // Get the items for the current page
        $currentPageData = $list_dosen->items();

        // Export the current page's data
        return Excel::download(new DosenExport(collect($currentPageData)), 'dosen_export.xlsx');
    }


    // ------- Mata Kuliah ----------
    public function getAllMataKuliahFromDB(Request $request)
    {
        $search = $request->input('search');

        $list_matakuliah = MataKuliah::when($search, function ($query, $search) {
            return $query->where('nama_mata_kuliah', 'like', '%' . $search . '%')
                ->orWhere('kode_mata_kuliah', 'like', '%' . $search . '%')
                ->orWhere('kel_mk', 'like', '%' . $search . '%')
                ->orWhere('jns_mk', 'like', '%' . $search . '%')
                ->orWhereHas('prodi', function ($query) use ($search) {
                    $query->where('nama_program_studi', 'like', '%' . $search . '%');
                })
                ->orWhereHas('jenjang_pendidikan', function ($query) use ($search) {
                    $query->where('nama_jenjang_didik', 'like', '%' . $search . '%');
                });
        })->paginate(20);

        return view('admin.matakuliah', compact('list_matakuliah'));
    }

    public function getAllMataKuliah()
    {
        // Attempt to get the data from the cache
        $list_matakuliah = Cache::remember('matakuliah_all_data', now()->addMinutes(5), function () {
            try {
                $data = $this->matakuliah->getAllMataKuliah($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch mata kuliah: ' . $e->getMessage());
                return null;
            }
        });

        // Manually paginate the data
        $currentPage = (int) request('page', 1); // Explicitly cast to integer
        $perPage = 50;
        $offset = ($currentPage - 1) * $perPage;

        // Slice the data for the current page
        $currentPageMataKuliah = array_slice($list_matakuliah, $offset, $perPage);

        // Create a LengthAwarePaginator instance
        $paginatedData = new LengthAwarePaginator(
            $currentPageMataKuliah,
            count($list_matakuliah), // Total number of records
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedData;
    }

    public function syncMataKuliah(Request $request)
    {
        $cloud_data = $this->matakuliah->getAllMataKuliah($this->token->getToken());
        foreach ($cloud_data as $data) {
            $usableTanggalMulai = date("Y-m-d", strtotime($data['tanggal_mulai_efektif']));
            $usableTanggalSelesai = date("Y-m-d", strtotime($data['tanggal_selesai_efektif']));
            // Use updateOrCreate to either update an existing record or create a new one
            MataKuliah::updateOrCreate(
                ['id_matkul' => $data['id_matkul']], // Match the record based on a unique identifier (e.g., 'id')
                [
                    'id_prodi' => $data['id_prodi'],
                    'id_jenjang_didik' => $data['id_jenj_didik'],
                    'id_jenis_mata_kuliah' => $data['id_jenis_mata_kuliah'],
                    'id_kelompok_mata_kuliah' => $data['id_kelompok_mata_kuliah'],

                    'kode_mata_kuliah' => $data['kode_mata_kuliah'],
                    'nama_mata_kuliah' => $data['nama_mata_kuliah'],
                    'sks_mata_kuliah' => $data['sks_mata_kuliah'],

                    'jns_mk' => $data['jns_mk'],
                    'kel_mk' => $data['kel_mk'],
                    'sks_tatap_muka' => $data['sks_tatap_muka'],
                    'sks_praktek' => $data['sks_praktek'],
                    'sks_praktek_lapangan' => $data['sks_praktek_lapangan'],
                    'sks_simulasi' => $data['sks_simulasi'],
                    'ada_sap' => $data['ada_sap'],
                    'ada_silabus' => $data['ada_silabus'],
                    'ada_bahan_ajar' => $data['ada_bahan_ajar'],
                    'ada_acara_praktek' => $data['ada_acara_praktek'],
                    'ada_diktat' => $data['ada_diktat'],
                    'tanggal_mulai_efektif' => $usableTanggalMulai,
                    'tanggal_selesai_efektif' => $usableTanggalSelesai,

                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        return response()->json(['message' => 'Data synchronized successfully.'], 200);
    }

    public function exportCurrentMataKuliahToExcel(Request $request)
    {
        // Extract search filters and pagination from the request
        $search = $request->get('search', null);
        $page = $request->get('page', 1);

        // Rebuild the paginated query
        $list_matakuliah = MataKuliah::when($search, function ($query, $search) {
            $query->where('nama_mata_kuliah', 'like', '%' . $search . '%')
                ->orWhere('kode_mata_kuliah', 'like', '%' . $search . '%')
                ->orWhere('kel_mk', 'like', '%' . $search . '%')
                ->orWhere('jns_mk', 'like', '%' . $search . '%')
                ->orWhereHas('prodi', function ($query) use ($search) {
                    $query->where('nama_program_studi', 'like', '%' . $search . '%');
                })
                ->orWhereHas('jenjang_pendidikan', function ($query) use ($search) {
                    $query->where('nama_jenjang_didik', 'like', '%' . $search . '%');
                });
        })->paginate(20, ['*'], 'page', $page);

        // Get the items for the current page
        $currentPageData = $list_matakuliah->items();

        // Export the current page's data
        return Excel::download(new MataKuliahExport(collect($currentPageData)), 'mata_kuliah_export.xlsx');
    }


    // ------- IPEPA ----------
    public function getIPEPA(Request $request)
    {
        // ----- START MAHASISWA -----
        $mahasiswa_data = Cache::remember('mahasiswa_datalist', now()->addMinutes(10), function () {
            return $this->rekap_ipepa_mahasiswa_dan_lulusan->all();
        });

        // Extract the latest updated_at value
        $latest_updated_at_mhs = $mahasiswa_data ? $mahasiswa_data->max('updated_at') : null;

        $mahasiswa_datalist = [
            'data' => $mahasiswa_data,
            'latest_updated_at' => $latest_updated_at_mhs
        ];

        // ----- END MAHASISWA -----

        // ----- START DOSEN TETAP -----
        $dosen_tetap_data = Cache::remember('dosen_tetap_datalist', now()->addMinutes(10), function () {
            return DB::table('dosen')
                ->leftJoin('aktivitas_mengajar_dosen', 'dosen.id_dosen', '=', 'aktivitas_mengajar_dosen.id_dosen')
                ->leftJoin('matakuliah', 'aktivitas_mengajar_dosen.id_matkul', '=', 'matakuliah.id_matkul')
                ->leftJoin('riwayat_fungsional_dosen', 'dosen.id_dosen', '=', 'riwayat_fungsional_dosen.id_dosen')
                ->select(
                    'dosen.id_dosen as id_dosen',
                    'dosen.nama_dosen as nama_dosen',
                    'dosen.nidn as nidn_nidk',
                    // 'dosen.gelar',
                    'riwayat_fungsional_dosen.nama_jabatan_fungsional as jabatan_akademik',
                    DB::raw('COUNT(aktivitas_mengajar_dosen.id_matkul) as jumlah_mata_kuliah'),
                    DB::raw('SUM(matakuliah.sks_mata_kuliah) as total_sks')
                )
                ->groupBy('dosen.id_dosen', 'dosen.nama_dosen', 'riwayat_fungsional_dosen.nidn', 'riwayat_fungsional_dosen.nama_jabatan_fungsional')
                ->orderBy('dosen.nama_dosen')
                ->get();
        });


        // Extract the latest updated_at value
        $latest_updated_at_dosen = $dosen_tetap_data ? $dosen_tetap_data->max('updated_at') : null;

        $dosen_tetap_datalist = [
            'data' => $dosen_tetap_data,
            'latest_updated_at' => $latest_updated_at_dosen
        ];

        // ----- END DOSEN TETAP -----

        return view('admin/ipepa', compact('mahasiswa_datalist', 'dosen_tetap_datalist'));
    }

    public function getIPEPADetailsMhs(Request $request, $category, $id_periode)
    {
        $list_periode = DB::table('mahasiswa')
            ->distinct()
            ->pluck('id_periode') // Only selecting unique id_periode values
            ->toArray(); // Convert collection to array

        // If id_periode does not exist, redirect to admin/ipepa
        if (!in_array($id_periode, $list_periode)) {
            return redirect()->route('admin.ipepa')->with('error', 'Periode tidak ditemukan.');
        }

        // Generate a list of id_periode from 20231 to the passed id_periode
        $valid_id_periode = array_filter($list_periode, function ($periode) use ($id_periode) {
            return $periode >= 20231 && $periode <= $id_periode;
        });

        if ($category == "mahasiswa-baru-regular" || $category == "mahasiswa-baru-transfer") {
            $query = $this->mahasiswa->where('id_periode', $id_periode)->with('prodi'); // Base query

            if ($id_periode == "20231") {
                $query->where('nim', 'LIKE', '23%'); // Only for period 20231
            }

            if ($category == "mahasiswa-baru-regular") {
                $query->whereRaw("SUBSTRING(nim, 5, 1) != '8'"); // Regular (5th digit ≠ 8)
            } else if ($category == "mahasiswa-baru-transfer") {
                $query->whereRaw("SUBSTRING(nim, 5, 1) = '8'"); // Transfer (5th digit = 8)
            }

            $list_mahasiswa = $query->paginate(50);

            return view('admin/ipepa-details-mhs', compact('list_mahasiswa', 'category'));
        } else if ($category == "lulusan") {
            $list_mahasiswa = $this->mahasiswa_lulus_do->where('id_periode_keluar', $id_periode)
                ->where("id_jenis_keluar", "1")
                ->with('mahasiswa') // Eager loading 'prodi' relation
                ->paginate(50);
            return view('admin/ipepa-details-mhs', compact('list_mahasiswa', 'category'));
        } else if ($category == "mahasiswa-aktif") {
            $list_mahasiswa = $this->mahasiswa->whereIn('id_periode', $valid_id_periode)
                ->where('nama_status_mahasiswa', 'aktif')
                ->with('prodi')
                ->paginate(50);
            return view('admin/ipepa-details-mhs', compact('list_mahasiswa', 'category', 'id_periode'));
        } else {
            return redirect()->route('admin.ipepa')->with('error', 'Kategori yang dipilih salah.');
        }
    }

    public function getIPEPADetailsDosenTetap(Request $request, $id_dosen)
    {
        // select specific dosen based on its id_dosen
        $dosen_info = $this->dosen->where('id_dosen', $id_dosen)->first();

        // select specific dosen's activities based on its id_dosen
        $list_matakuliah = DB::table('aktivitas_mengajar_dosen')
            ->where('aktivitas_mengajar_dosen.id_dosen', $id_dosen)
            ->join('matakuliah', 'matakuliah.id_matkul', '=', 'aktivitas_mengajar_dosen.id_matkul')
            ->leftJoin('prodi', 'prodi.id_prodi', '=', 'matakuliah.id_prodi')
            ->leftJoin('jenjang_pendidikan', 'jenjang_pendidikan.id', '=', 'matakuliah.id_jenjang_didik')
            ->select('matakuliah.*', 'prodi.nama_program_studi', 'jenjang_pendidikan.nama_jenjang_didik')
            ->get();

        // return view('admin/ipepa-details-dosen', compact('dosen_info', 'dosen_activities'));

        return view('admin/ipepa-details-dosen-tetap', compact('list_matakuliah', 'dosen_info'));
    }

    public function syncIPEPAMhs()
    {
        // ----- Mahasiswa Aktif Data
        $aktif_data = Cache::remember('aktif_data', now()->addMinutes(5), function () {
            try {
                $data = $this->mahasiswa->getRekapJumlahMhs($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch mhs aktif data: ' . $e->getMessage());
                return null;
            }
        });

        foreach ($aktif_data as $data) {
            // Use updateOrCreate to either update an existing record or create a new one
            $this->rekap_jumlah_mahasiswa->updateOrCreate(
                [
                    'id_prodi' => $data['id_prodi'],
                    'id_periode' => $data['id_periode'],
                ], // Match the record based on a unique identifier (e.g., 'id')
                [
                    'id_prodi' => $data['id_prodi'],
                    'id_periode' => $data['id_periode'],
                    'nama_periode' => $data['nama_periode'],
                    'aktif' => $data['aktif'],
                    'cuti' => $data['cuti'],
                    'non_aktif' => $data['non_aktif'],
                    'sedang_double_degree' => $data['sedang_double_degree'],

                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        // ----- Mahasiswa Lulus Data
        $lulus_data = Cache::remember('lulus_data', now()->addMinutes(5), function () {
            try {
                $data = $this->mahasiswa->getAllMhsLulusDO($this->token->getToken(), "");

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch lulusan data: ' . $e->getMessage());
                return null;
            }
        });

        foreach ($lulus_data as $data) {
            // Use updateOrCreate to either update an existing record or create a new one
            $this->mahasiswa_lulus_do->updateOrCreate(
                ['id_mahasiswa' => $data['id_mahasiswa']], // Match the record based on a unique identifier (e.g., 'id')
                [
                    'id_mahasiswa' => $data['id_mahasiswa'],
                    'id_registrasi_mahasiswa' => $data['id_registrasi_mahasiswa'],
                    'id_perguruan_tinggi' => $data['id_perguruan_tinggi'],
                    'id_prodi' => $data['id_prodi'],
                    'tgl_masuk_sp' => date("Y-m-d", strtotime($data['tgl_masuk_sp'])),
                    'tgl_keluar' => date("Y-m-d", strtotime($data['tgl_keluar'])),
                    'skhun' => $data['skhun'],
                    'no_peserta_ujian' => $data['no_peserta_ujian'],
                    'no_seri_ijazah' => $data['no_seri_ijazah'],
                    'tgl_create' => date("Y-m-d", strtotime($data['tgl_create'])),
                    'sks_diakui' => $data['sks_diakui'],
                    'jalur_skripsi' => $data['jalur_skripsi'],
                    'judul_skripsi' => $data['judul_skripsi'],
                    'bln_awal_bimbingan' => $data['bln_awal_bimbingan'],
                    'bln_akhir_bimbingan' => $data['bln_akhir_bimbingan'],
                    'sk_yudisium' => $data['sk_yudisium'],
                    'tgl_sk_yudisium' => $data['tgl_sk_yudisium'],
                    'ipk' => $data['ipk'],
                    'sert_prof' => $data['sert_prof'],
                    'a_pindah_mhs_asing' => $data['a_pindah_mhs_asing'],
                    'id_pt_asal' => $data['id_pt_asal'],
                    'id_prodi_asal' => $data['id_prodi_asal'],
                    'nm_pt_asal' => $data['nm_pt_asal'],
                    'nm_prodi_asal' => $data['nm_prodi_asal'],
                    'id_jns_daftar' => $data['id_jns_daftar'],
                    'id_jns_keluar' => $data['id_jns_keluar'],
                    'id_jalur_masuk' => $data['id_jalur_masuk'],
                    'id_pembiayaan' => $data['id_pembiayaan'],
                    'id_minat_bidang' => $data['id_minat_bidang'],
                    'bidang_minor' => $data['bidang_minor'],
                    'biaya_masuk_kuliah' => $data['biaya_masuk_kuliah'],
                    'namapt' => $data['namapt'],
                    'id_jur' => $data['id_jur'],
                    'nm_jns_daftar' => $data['nm_jns_daftar'],
                    'nm_smt' => $data['nm_smt'],
                    'nim' => $data['nim'],
                    'nama_program_studi' => $data['nama_program_studi'],
                    'angkatan' => $data['angkatan'],
                    'id_jenis_keluar' => $data['id_jenis_keluar'],
                    'nama_jenis_keluar' => $data['nama_jenis_keluar'],
                    'tanggal_keluar' => date("Y-m-d", strtotime($data['tanggal_keluar'])),
                    'id_periode_keluar' => $data['id_periode_keluar'],
                    'keterangan' => $data['keterangan'],

                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        $mahasiswa_data =
            DB::table(
                DB::raw("(SELECT 
                    id_periode, 
                    nama_periode, 
                    SUM(aktif) AS total_aktif, 
                    SUM(sedang_double_degree) AS total_double_degree, 
                    SUM(cuti) AS total_cuti,
                    SUM(aktif + sedang_double_degree + cuti) AS jumlah_aktif,
                    MAX(updated_at) AS updated_at
                    FROM rekap_jumlah_mahasiswa
                    WHERE id_periode >= 20231
                    GROUP BY id_periode, nama_periode) as rjm")
            )
            ->select(
                'rjm.id_periode',
                'rjm.nama_periode',
                'rjm.jumlah_aktif',
                DB::raw('COUNT(DISTINCT ml.id_mahasiswa) as jumlah_lulus'),
                DB::raw('COUNT(DISTINCT CASE WHEN SUBSTRING(m.nim, 5, 1) = "8" THEN m.id_mahasiswa END) as jumlah_maba_transfer'),
                DB::raw('COUNT(DISTINCT CASE WHEN SUBSTRING(m.nim, 5, 1) != "8" THEN m.id_mahasiswa END) as jumlah_maba_regular'),
                'rjm.updated_at'
            )
            ->leftJoin('mahasiswa_lulus_do as ml', function ($join) {
                $join->on('rjm.id_periode', '=', 'ml.id_periode_keluar')
                    ->where('ml.id_jenis_keluar', '=', 1);
            })
            ->leftJoin('mahasiswa as m', function ($join) {
                $join->on('rjm.id_periode', '=', 'm.id_periode')
                    ->whereRaw("(CASE 
                                        WHEN rjm.id_periode = 20231 THEN LEFT(m.nim, 2) = SUBSTRING(rjm.id_periode, 3, 2) 
                                        ELSE 1=1 
                                    END)");
            })
            ->groupBy('rjm.id_periode', 'rjm.nama_periode', 'rjm.jumlah_aktif', 'rjm.updated_at')
            ->orderBy('rjm.id_periode')
            ->get()
            ->map(function ($item) {
                $item->jumlah_aktif = (int) $item->jumlah_aktif;
                $item->jumlah_lulus = (int) $item->jumlah_lulus;
                $item->jumlah_maba_transfer = (int) $item->jumlah_maba_transfer;
                $item->jumlah_maba_regular = (int) $item->jumlah_maba_regular;
                return $item;
            });

        foreach ($mahasiswa_data as $data) {
            $this->rekap_ipepa_mahasiswa_dan_lulusan->updateOrCreate(
                ['id_periode' => $data->id_periode],
                [
                    'nama_periode' => $data->nama_periode,
                    'rekap_jumlah_maba_regular' => $data->jumlah_maba_regular,
                    'rekap_jumlah_maba_transfer' => $data->jumlah_maba_transfer,
                    'rekap_jumlah_mhs_aktif' => $data->jumlah_aktif,
                    'rekap_jumlah_lulusan' => $data->jumlah_lulus,
                ]
            );
        }

        return Cache::clear('mahasiswa_datalist');
    }

    public function syncIPEPADosen()
    {
        // ----- Ativitas Mengajar Dosen ------
        $aktivitas_mengajar = Cache::remember('aktivitas_mengajar', now()->addMinutes(5), function () {
            try {
                $data = $this->aktivitas_mengajar_dosen->getAktivitasMengajarDosen($this->token->getToken());

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch aktivitas mengajar: ' . $e->getMessage());
                return null;
            }
        });

        foreach ($aktivitas_mengajar as $data) {
            $this->aktivitas_mengajar_dosen->updateOrCreate(
                [
                    'id_dosen' => $data['id_dosen'],
                    'id_matkul' => $data['id_matkul'],
                    'id_periode' => $data['id_periode'],
                ],
                [
                    'id_prodi' => $data['id_prodi'],
                    'id_registrasi_dosen' => $data['id_registrasi_dosen'],
                    'id_periode' => $data['id_periode'],
                    'nama_periode' => $data['nama_periode'],
                    'nama_mata_kuliah' => $data['nama_mata_kuliah'],
                    'id_kelas' => $data['id_kelas'],
                    'nama_kelas_kuliah' => $data['nama_kelas_kuliah'],
                    'rencana_minggu_pertemuan' => $data['rencana_minggu_pertemuan'],
                    'realisasi_minggu_pertemuan' => $data['realisasi_minggu_pertemuan'],

                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        // ----- Riwayat Fungsional Dosen -----
        $riwayat_fungsional = Cache::remember('riwayat_fungsional', now()->addMinutes(5), function () {
            try {
                $data = $this->riwayat_fungsional_dosen->getRiwayatFungsionalDosen($this->token->getToken(), "");

                // Only cache if the response is valid
                if ($data && is_array($data)) {
                    return $data;
                }

                // Return null (don't cache invalid data)
                return null;
            } catch (\Exception $e) {
                // Log the error and return null (without caching)
                Log::error('Failed to fetch riwayat fungsional: ' . $e->getMessage());
                return null;
            }
        });

        foreach ($riwayat_fungsional as $data) {
            $usableDate = date("Y-m-d", strtotime($data['mulai_sk_jabatan']));

            $this->riwayat_fungsional_dosen->updateOrCreate(
                ['id_dosen' => $data['id_dosen']],
                [
                    'nidn' => $data['nidn'],
                    'nama_dosen' => $data['nama_dosen'],
                    'id_jabatan_fungsional' => $data['id_jabatan_fungsional'],
                    'nama_jabatan_fungsional' => $data['nama_jabatan_fungsional'],
                    'sk_jabatan_fungsional' => $data['sk_jabatan_fungsional'],
                    'mulai_sk_jabatan' => $usableDate,

                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        return Cache::clear('dosen_tetap_datalist');
    }

    public function getTest()
    {
        $test = fetchData('GetAktivitasMengajarDosen', $this->token->getToken());
        dd($test);
        return view('admin.test', compact('test'));
    }
}
