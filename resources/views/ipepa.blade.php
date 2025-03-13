<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- theme meta -->
    <meta name="theme-name" content="quixlab" />
    <title>Feeder Universitas Almarisah Madani</title>

    <!-- pemanggilan css -->
    @include('_layouts.header')
</head>

<body class="bg-primer">
    <div id="main-wrapper">

        @include('_layouts.nav')

        <div class="container-fluid">
            <div class="p-4 my-3 card">
                <div class="row">
                    <div class="my-2 col-8">
                        <h2>Laporan IPEPA</h2>
                    </div>
                    <hr />
                    <div class="mt-4">
                        <h4>Mahasiswa</h4>
                        {{-- Data Table --}}
                        @if (count($mahasiswa_datalist['data']) > 0)
                            @php $no = 1; @endphp
                            <table class="table table-bordered">
                                <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                    <th scope="col" rowspan="2" class="text-center">No</th>
                                    <th scope="col" rowspan="2" class="text-center">Tahun Akademik</th>
                                    <th scope="col" colspan="2" class="text-center">Jumlah Mahasiswa Baru</th>
                                    <th scope="col" rowspan="2" class="text-center">Jumlah Mahasiswa Aktif</th>
                                    <th scope="col" rowspan="2" class="text-center">Jumlah Lulusan</th>
                                </tr>
                                <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                    <th scope="col" class="text-center">Regular</th>
                                    <th scope="col" class="text-center">Transfer</th>
                                </tr>
                                @foreach ($mahasiswa_datalist['data'] as $data)
                                    <tr class="[&>td]:text-center [&>td]:border [&>td>a]:p-2">
                                        <td scope="col" class="text-center">{{ $no++ }}</td>
                                        <td scope="col" class="text-center">{{ $data->nama_periode }}</td>
                                        {{-- Jumlah Maba Start --}}
                                        <td scope="col" class="text-center">
                                            {{ $data->rekap_jumlah_maba_regular }}
                                        </td>
                                        <td scope="col" class="text-center">
                                            {{ $data->rekap_jumlah_maba_transfer }}
                                        </td>
                                        {{-- Jumlah Maba End --}}
                                        <td scope="col" class="text-center">
                                            {{ $data->rekap_jumlah_mhs_aktif }}
                                        </td>
                                        <td scope="col" class="text-center">
                                            {{ $data->rekap_jumlah_lulusan }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <p class="text-center">Data Belum Tersedia.</p>
                        @endif
                        </table>
                    </div>
                </div>

                <!-- To display API response -->
                <div id="dataTable"></div>

            </div>
        </div>
    </div>

    @include('_layouts.footer')
</body>

<script>
    // JavaScript to handle the selection and API call
    document.getElementById('dataFilterDropdown').addEventListener('change', function() {
        const selectedTahun = this.value; // Get selected value

        // Make an API call
        fetch(`/mahasiswa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // For Laravel CSRF protection
                },
                body: JSON.stringify({
                    tahun: selectedTahun
                })
            })
            .then((response) => response.text()) // Convert the response to text
            .then((responseText) => {
                document.getElementById('dataTable').innerHTML = responseText
            })
    });
</script>

</html>
