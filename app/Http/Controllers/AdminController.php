<?php

namespace App\Http\Controllers;

use App\Exports\DosenExport;
use App\Exports\JenjangExport;
use App\Exports\MahasiswaExport;
use App\Exports\MataKuliahExport;
use App\Exports\ProdiExport;
use App\Models\Dosen;
use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    private $token;
    private $prodi;
    private $jenjang;
    private $mahasiswa;
    private $dosen;
    private $matakuliah;

    public function __construct()
    {
        $this->token = new Token();
        $this->prodi = new Prodi();
        $this->jenjang = new Jenjang();
        $this->mahasiswa = new Mahasiswa();
        $this->dosen = new Dosen();
        $this->matakuliah = new MataKuliah();
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
        $list_mahasiswa = Cache::remember('mahasiswa_all_data', now()->addMinutes(10), function () {
            // Cache miss: fetch the data from the source (API or database)
            return $this->mahasiswa->getAllMhs($this->token->getToken());
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
        $list_prodi = Cache::remember('prodi_all_data', now()->addMinutes(10), function () {
            return $this->prodi->getAllProdi($this->token->getToken());
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
        $list_jenjang = Cache::remember('jenjang_all_data', now()->addMinutes(10), function () {
            return $this->jenjang->getAllJenjang($this->token->getToken());
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
        $list_dosen = Cache::remember('dosen_all_data', now()->addMinutes(10), function () {
            // Cache miss: fetch the data from the source (API or database)
            return $this->dosen->getAllDosen($this->token->getToken());
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
        $list_matakuliah = Cache::remember('matakuliah_all_data', now()->addMinutes(10), function () {
            // Cache miss: fetch the data from the source (API or database)
            return $this->matakuliah->getAllMataKuliah($this->token->getToken());
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
}
