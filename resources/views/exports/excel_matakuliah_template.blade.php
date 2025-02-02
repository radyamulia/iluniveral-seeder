<table>
    <tr>
        <th>Kode Mata Kuliah</th>
        <th>Nama Mata Kuliah</th>
        <th>SKS Mata Kuliah</th>
        <th>Jenis Mata Kuliah</th>
        <th>Kelompok Mata Kuliah</th>
        <th>Program Studi</th>
        <th>Jenjang</th>
        <th>SKS Tatap Muka</th>
        <th>SKS Praktek</th>
        <th>SKS Praktek Lapangan</th>
        <th>SKS Simulasi</th>
        <th>Status SAP</th>
        <th>Status Silabus</th>
        <th>Status Bahan Ajar</th>
        <th>Status Acara Praktek</th>
        <th>Status Diktat</th>
        <th>Tanggal Mulai Efektif</th>
        <th>Tanggal Selesai Efektif</th>
        <th>ID Jenis Mata Kuliah</th>
        <th>ID Kelompok Mata Kuliah</th>
    </tr>
    @foreach ($list_matakuliah as $data)
        <tr>
            <td>{{ $data['kode_mata_kuliah'] }}</td>
            <td>{{ $data['nama_mata_kuliah'] }}</td>
            <td>{{ $data['sks_mata_kuliah'] }}</td>

            {{-- <td>{{ $data['jns_mk'] }}</td> --}}
            <td>
                @php
                    $jns_mk = [
                        'A' => 'Wajib',
                        'B' => 'Pilihan',
                        'C' => 'Wajib Peminatan',
                        'D' => 'Pilihan Peminatan',
                        'S' => 'Tugas Akhir/Skripsi/Disertasi',
                    ];
                @endphp

                {{ $jns_mk[$data['jns_mk']] ?? '' }}
            </td>

            {{-- <td>{{ $data['kel_mk'] }}</td> --}}
            <td>
                @php
                    $kel_mk = [
                        'A' => 'MPK',
                        'B' => 'MKK',
                        'C' => 'MKB',
                        'D' => 'MPB',
                        'E' => 'MBB',
                        'F' => 'MKU / MKDU',
                        'F' => 'MKU / MKDU',
                        'G' => 'MKDK',
                        'H' => 'MKK',
                    ];
                @endphp

                {{ $kel_mk[$data['kel_mk']] ?? '' }}
            </td>

            <td>{{ $data['prodi']['nama_program_studi'] }}</td>
            <td>{{ $data['jenjang_pendidikan']['nama_jenjang_didik'] }}</td>

            <td>{{ $data['sks_tatap_muka'] }}</td>
            <td>{{ $data['sks_praktek'] }}</td>
            <td>{{ $data['sks_praktek_lapangan'] }}</td>
            <td>{{ $data['sks_simulasi'] }}</td>

            <td>{{ $data['ada_sap'] === 1 ? 'Ada' : '' }}</td>
            <td>{{ $data['ada_silabus'] === 1 ? 'Ada' : '' }}</td>
            <td>{{ $data['ada_bahan_ajar'] === 1 ? 'Ada' : '' }}</td>
            <td>{{ $data['ada_acara_praktek'] === 1 ? 'Ada' : '' }}</td>
            <td>{{ $data['ada_diktat'] === 1 ? 'Ada' : '' }}</td>

            <td>{{ $data['tanggal_mulai_efektif'] }}</td>
            <td>{{ $data['tanggal_selesai_efektif'] }}</td>
            <td>{{ $data['id_jenis_mata_kuliah'] }}</td>
            <td>{{ $data['id_kelompok_mata_kuliah'] }}</td>
        </tr>
    @endforeach
</table>
