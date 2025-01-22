<table>
    <tr>
        <th>NIM</th>
        <th>Nama Mahasiswa</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>IPK</th>
        <th>Agama</th>
        <th>Status Mahasiswa</th>
        <th>Periode Masuk</th>
        <th>Prodi</th>
        <th>Jenjang Pendidikan</th>
        <th>Kode Prodi</th>
        <th>ID Registrasi</th>
        <th>ID Mahasiswa</th>
        <th>ID SMS</th>
    </tr>
    @foreach ($list_mahasiswa as $data)
        <tr>
            <td>{{ $data['nim'] }}</td>
            <td>{{ $data['nama_mahasiswa'] }}</td>
            <td>{{ $data['jenis_kelamin'] }}</td>
            <td>{{ $data['tanggal_lahir'] }}</td>
            <td>{{ $data['ipk'] }}</td>
            <td>{{ $data['nama_agama'] }}</td>
            <td>{{ $data['nama_status_mahasiswa'] }}</td>
            <td>{{ $data['nama_periode_masuk'] }}</td>
            <td>{{ $data['prodi']['nama_program_studi'] }}</td>
            <td>{{ $data['prodi']['jenjang_pendidikan']['nama_jenjang_didik'] }}</td>
            <td>{{ $data['prodi']['kode_program_studi'] }}</td>
            <td>{{ $data['id_registrasi_mahasiswa'] }}</td>
            <td>{{ $data['id_mahasiswa'] }}</td>
            <td>{{ $data['id_sms'] }}</td>
        </tr>
    @endforeach
</table>
