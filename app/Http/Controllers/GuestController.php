<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
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
        $this->rekap_jumlah_mahasiswa = new RekapJumlahMahasiswa();
        $this->mahasiswa_lulus_do = new MahasiswaLulusDO();
        $this->rekap_ipepa_mahasiswa_dan_lulusan = new RekapIPEPAMahasiswaDanLulusan();
        $this->aktivitas_mengajar_dosen = new AktivitasMengajarDosen();
        $this->riwayat_fungsional_dosen = new RiwayatFungsionalDosen();
    }

    // mahasiswa route as guest
    public function getTotalMahasiswaDataFilter()
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

    // Get a list of prodi with its total mahasiswa
    public function getTotalMahasiswaForEachProdi(Request $request)
    {
        $prodi_list = $this->prodi::all();
        $tahun = $request->tahun;
        $hasil = [];
        $total = 0;

        foreach ($prodi_list as $prodi) {
            if ($prodi['id_prodi'] == '343a3445-fc49-4bd2-9c53-9c514bafaf2e' && $tahun < 23) {
                $nimPattern = 'MU' . $tahun . '%';
            } else {
                $nimPattern = $tahun . '%';
            }
            // Get the students matching the filter
            $list_mhs = $this->mahasiswa::where('id_prodi', $prodi['id_prodi'])
                ->where('nim', 'like', $nimPattern)
                ->get();

            // Add the result to the $hasil array
            $hasil[] = [
                "program_studi" => $prodi['nama_program_studi'],
                "jenjang" => $prodi['jenjang_pendidikan']['nama_jenjang_didik'],
                "jumlah" => count($list_mhs),
            ];

            // Add the count to the total
            $total += count($list_mhs);
        }

        return view('components.mahasiswa_table', compact('hasil', 'total'));
    }

    // ipepa route as guest
    public function getIPEPA()
    {
        // ----- START MAHASISWA -----
        $mahasiswa_data = Cache::remember('mahasiswa_datalist', now(), function () {
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
        // $dosen_tetap_data = Cache::remember('dosen_tetap_datalist', now()->addMinutes(10), function () {});


        // // Extract the latest updated_at value
        // $latest_updated_at_dosen = $mahasiswa_data->max('updated_at');

        // $dosen_tetap_datalist = [
        //     'data' => $dosen_tetap_data,
        //     'latest_updated_at' => $latest_updated_at_dosen
        // ];

        return view('/ipepa', compact('mahasiswa_datalist'));
    }
}
