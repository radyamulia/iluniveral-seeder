<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Token;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    private $token;
    private $prodi;
    private $mahasiswa;

    public function __construct()
    {
        $this->token = new Token();
        $this->prodi = new Prodi();
        $this->mahasiswa = new Mahasiswa();
    }

    public function index()
    {
        $dataFilter = [];
        $tahunSekarang = (int) date('Y');
        for ($tahunAwal = 2016; $tahunAwal <= $tahunSekarang; $tahunAwal++) {
            $dataFilter[] = [
                "tahunAwal" => $tahunAwal,
                "value" => substr($tahunAwal, 2, 2)
            ];
        }

        return view('mahasiswa', compact('dataFilter'));
    }

    public function store() {}

    public function update() {}

    public function edit() {}

    // API Request Methods
    // Get a list of prodi with its total mahasiswa
    public function getTotalMahasiswaForEachProdi(Request $request)
    {
        $get_token = $this->token->getToken();
        $prodi_list = $this->prodi->getAllProdi($get_token);
        $tahun = $request->tahun;
        $hasil = [];
        $total = 0;

        foreach ($prodi_list as $prodi) {
            // Set the filter based on condition
            if ($prodi['id_prodi'] == '343a3445-fc49-4bd2-9c53-9c514bafaf2e' && $tahun < 23) {
                $filter = "id_prodi = '" . $prodi['id_prodi'] . "' AND nim LIKE 'MU" . $tahun . "%'";
            } else {
                $filter = "id_prodi = '" . $prodi['id_prodi'] . "' AND nim LIKE '" . $tahun . "%'";
            }

            // Get the students matching the filter
            $banyakMhs = $this->mahasiswa->getAllMhs($get_token, $filter);

            // Add the result to the $hasil array
            $hasil[] = [
                "program_studi" => $prodi['nama_program_studi'],
                "jenjang" => $prodi['nama_jenjang_pendidikan'],
                "jumlah" => count($banyakMhs),
            ];

            // Add the count to the total
            $total += count($banyakMhs);
        }

        return view('components.mahasiswa_table', compact('hasil', 'total'));
    }
}
