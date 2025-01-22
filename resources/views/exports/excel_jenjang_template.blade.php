<table>
  <thead>
      <tr>
          <th>ID</th>
          <th>ID Jenjang Pendidikan</th>
          <th>Nama Jenjang Pendidikan</th>
      </tr>
  </thead>
  <tbody>
      @foreach ($list_jenjang as $jenjang)
          <tr>
              <td>{{ $jenjang['id'] }}</td>
              <td>{{ $jenjang['id_jenjang_didik'] }}</td>
              <td>{{ $jenjang['nama_jenjang_didik'] }}</td>
          </tr>
      @endforeach
  </tbody>
</table>