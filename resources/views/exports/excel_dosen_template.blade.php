<table>
    <thead>
        <tr>
          <th>Nama Dosen</th>
          <th>NIDN</th>
          <th>NIP</th>
          <th>Jenis Kelamin</th>
          <th>Agama</th>
          <th>Tanggal Lahir</th>
          <th>Status Aktif</th>
          <th>ID Dosen</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($list_dosen as $dosen)
            <tr>
                <td>{{ $dosen['nama_dosen'] }}</td>
                <td>{{ $dosen['nidn'] }}</td>
                <td>{{ $dosen['nip'] }}</td>
                <td>{{ $dosen['jenis_kelamin'] }}</td>
                <td>{{ $dosen['nama_agama'] }}</td>
                <td>{{ $dosen['tanggal_lahir'] }}</td>
                <td>{{ $dosen['nama_status_aktif'] }}</td>
                <td>{{ $dosen['id_dosen'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
