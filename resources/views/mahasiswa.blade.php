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
            <div class="card my-3 p-2">
                <div class="row">
                    <div class="col-8">
                        <h2>Data Mahasiswa</h2>
                        <span>Jumlah Mahasiswa Per Tahun Angkatan</span>
                    </div>
                    <div class="col-4">
                        <select class="form-select" aria-label="Default select example" id="dataFilterDropdown"
                            aria-selected="16">
                            <option value=" " selected>Filter Tahun</option>
                            @foreach ($dataFilter as $filter)
                                <option value="{{ $filter['value'] }}">{{ $filter['tahunAwal'] }}</option>
                            @endforeach
                        </select>
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
