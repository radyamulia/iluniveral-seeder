<?php

namespace App\Http\Controllers;

use App\Exports\JenjangExport;
use App\Exports\MahasiswaExport;
use App\Exports\ProdiExport;
use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Token;
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

    public function __construct()
    {
        $this->token = new Token();
        $this->prodi = new Prodi();
        $this->jenjang = new Jenjang();
        $this->mahasiswa = new Mahasiswa();
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
        })->paginate(25, ['*'], 'page', $page);

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
        })->paginate(25, ['*'], 'page', $page);

        // Get the items for the current page
        $currentPageData = $list_jenjang->items();

        // Export the current page's data
        return Excel::download(new JenjangExport(collect($currentPageData)), 'jenjang_pendidikan_export.xlsx');
    }
}
