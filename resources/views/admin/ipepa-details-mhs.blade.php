<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail IPEPA Tahun Akademik ') }}
            @if ($category == 'mahasiswa-baru-regular' || $category == 'mahasiswa-baru-transfer')
                {{ $list_mahasiswa->first()->nama_periode_masuk }}
            @elseif ($category == 'lulusan')
                {{ $list_mahasiswa->first()->id_periode_keluar }}
            @elseif ($category == 'mahasiswa-aktif')
                {{ $id_periode }}
            @endif
        </h2>
    </x-slot>


    <main class="py-12">
        <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">

            <a href="/admin/ipepa" class="text-blue-500 hover:underline">← Kembali</a>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-8 overflow-scroll text-gray-900">
                    <h2 class="text-lg font-bold">
                        {{ $category == 'mahasiswa-baru'
                            ? 'List Mahasiswa Baru'
                            : ($category == 'lulusan'
                                ? 'List Lulusan'
                                : 'List Mahasiswa Aktif') }}
                    </h2>

                    {{-- Data Table --}}
                    @if (count($list_mahasiswa) > 0)
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">NIM</th>
                                <th scope="col">Nama Mahasiswa</th>
                                <th scope="col">Jenis Kelamin</th>
                                <th scope="col">Tanggal Lahir</th>
                                <th scope="col">IPK</th>
                                <th scope="col">Agama</th>
                                <th scope="col">Status Mahasiswa</th>
                                <th scope="col">ID Periode</th>
                                <th scope="col">Periode Masuk</th>
                                <th scope="col">Prodi</th>
                                <th scope="col">Jenjang Pendidikan</th>
                                <th scope="col">Kode Prodi</th>
                                <th scope="col">ID Registrasi</th>
                                <th scope="col">ID Mahasiswa</th>
                                <th scope="col">ID SMS</th>
                            </tr>
                            @foreach ($list_mahasiswa as $data)
                                <tr class="[&>td]:text-center [&>td]:border [&>td]:p-2">
                                    @if ($category == 'lulusan')
                                        <td scope="col">{{ $data->mahasiswa->nim }}</td>
                                        <td scope="col">{{ $data->mahasiswa->nama_mahasiswa }}</td>
                                        <td scope="col">{{ $data->mahasiswa->jenis_kelamin }}</td>
                                        <td scope="col">{{ $data->mahasiswa->tanggal_lahir }}</td>
                                        <td scope="col">{{ $data->mahasiswa->ipk }}</td>
                                        <td scope="col">{{ $data->mahasiswa->nama_agama }}</td>
                                        <td scope="col">{{ $data->mahasiswa->nama_status_mahasiswa }}</td>
                                        <td scope="col">{{ $data->mahasiswa->id_periode }}</td>
                                        <td scope="col">{{ $data->mahasiswa->nama_periode_masuk }}</td>
                                        <td scope="col">{{ $data->mahasiswa->prodi->nama_program_studi }}</td>
                                        <td scope="col">
                                            {{ $data->mahasiswa->prodi->jenjang_pendidikan->nama_jenjang_didik }}
                                        </td>
                                        <td scope="col">{{ $data->mahasiswa->prodi->kode_program_studi }}</td>
                                        <td scope="col">{{ $data->mahasiswa->id_registrasi_mahasiswa }}</td>
                                        <td scope="col">{{ $data->mahasiswa->id_mahasiswa }}</td>
                                        <td scope="col">{{ $data->mahasiswa->id_sms }}</td>
                                    @else
                                        <td scope="col">{{ $data->nim }}</td>
                                        <td scope="col">{{ $data->nama_mahasiswa }}</td>
                                        <td scope="col">{{ $data->jenis_kelamin }}</td>
                                        <td scope="col">{{ $data->tanggal_lahir }}</td>
                                        <td scope="col">{{ $data->ipk }}</td>
                                        <td scope="col">{{ $data->nama_agama }}</td>
                                        <td scope="col">{{ $data->nama_status_mahasiswa }}</td>
                                        <td scope="col">{{ $data->id_periode }}</td>
                                        <td scope="col">{{ $data->nama_periode_masuk }}</td>
                                        <td scope="col">{{ $data->prodi->nama_program_studi }}</td>
                                        <td scope="col">{{ $data->prodi->jenjang_pendidikan->nama_jenjang_didik }}
                                        </td>
                                        <td scope="col">{{ $data->prodi->kode_program_studi }}</td>
                                        <td scope="col">{{ $data->id_registrasi_mahasiswa }}</td>
                                        <td scope="col">{{ $data->id_mahasiswa }}</td>
                                        <td scope="col">{{ $data->id_sms }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <p class="text-center">Data Belum Tersedia.</p>
                    @endif
                    </table>

                </div>
                <div class="p-6">
                    {{ $list_mahasiswa->links() }} {{-- This will generate pagination links --}}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
