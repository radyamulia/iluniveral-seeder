<table>
    <tr>
        <th>ID</th>
        <th>Kode Program Studi</th>
        <th>Nama Program Studi</th>
        <th>Status</th>
        <th>Jenjang Pendidikan</th>
    </tr>
    @foreach ($list_prodi as $data)
        <tr>
            <td>{{ $data['id_prodi'] }}</td>
            <td>{{ $data['kode_program_studi'] }}</td>
            <td>{{ $data['nama_program_studi'] }}</td>
            <td>{{ $data['status'] }}</td>
            <td>{{ $data['jenjang_pendidikan']['nama_jenjang_didik'] }}</td>
        </tr>
    @endforeach
</table>
