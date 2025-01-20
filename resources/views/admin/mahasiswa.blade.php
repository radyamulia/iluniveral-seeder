<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Mahasiswa') }}
        </h2>
    </x-slot>

    <main class="py-12">
        <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">
            {{-- Modal Trigger Button --}}
            <x-modal-mahasiswa name="mahasiswa-modal" />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                {{-- Data Table --}}
                <div class="p-6 space-y-8 overflow-scroll text-gray-900 scrollbar-hide">
                    @if (count($list_mahasiswa) > 0)
                        @php $no = 1; @endphp
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">ID Registrasi</th>
                                <th scope="col">ID Mahasiswa</th>
                                <th scope="col">ID SMS</th>
                                <th scope="col">NIM</th>
                                <th scope="col">Nama Mahasiswa</th>
                                <th scope="col">Jenis Kelamin</th>
                                <th scope="col">Tanggal Lahir</th>
                                <th scope="col">IPK</th>
                                <th scope="col">Agama</th>
                                <th scope="col">Status Mahasiswa</th>
                                <th scope="col">Periode Masuk</th>
                                <th scope="col">Prodi</th>
                            </tr>
                            @foreach ($list_mahasiswa as $data)
                                <tr class="[&>td]:text-center [&>td]:border [&>td]:p-2">
                                    <td scope="col">{{ $data['id_registrasi_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['id_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['id_sms'] }}</td>
                                    <td scope="col">{{ $data['nim'] }}</td>
                                    <td scope="col">{{ $data['nama_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['jenis_kelamin'] }}</td>
                                    <td scope="col">{{ $data['tanggal_lahir'] }}</td>
                                    <td scope="col">{{ $data['ipk'] }}</td>
                                    <td scope="col">{{ $data['nama_agama'] }}</td>
                                    <td scope="col">{{ $data['nama_status_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['nama_periode_masuk'] }}</td>
                                    <td scope="col">{{ $data['prodi']['nama_program_studi'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="text-center text-gray-600">
                            {{ __('Data mahasiswa belum tersedia.') }}
                        </div>
                    @endif

                    {{-- Pagination --}}
                    {{ $list_mahasiswa }}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
