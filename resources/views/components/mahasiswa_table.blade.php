<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">Prodi</th>
            <th scope="col">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach ($hasil as $data)
            <tr>
                <td scope="row">{{ $no++ }}</td>
                <td>{{ $data['program_studi'] . ' - ' . $data['jenjang'] }}</td>
                <td>{{ $data['jumlah'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">TOTAL</td>
            <td>{{ $total }}</td>
        </tr>
    </tbody>
</table>
