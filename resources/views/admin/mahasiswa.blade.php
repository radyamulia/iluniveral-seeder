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

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.mahasiswa') }}" class="mb-6">
                <div class="flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mahasiswa..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button type="submit" class="px-4 py-2 ml-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                        Cari
                    </button>
                </div>
            </form>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                {{-- Data Table --}}
                <div class="p-6 space-y-8 overflow-scroll text-gray-900">
                    @if (count($list_mahasiswa) > 0)
                        @php $no = 1; @endphp
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">NIM</th>
                                <th scope="col">Nama Mahasiswa</th>
                                <th scope="col">Jenis Kelamin</th>
                                <th scope="col">Tanggal Lahir</th>
                                <th scope="col">IPK</th>
                                <th scope="col">Agama</th>
                                <th scope="col">Status Mahasiswa</th>
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
                                    <td scope="col">{{ $data['nim'] }}</td>
                                    <td scope="col">{{ $data['nama_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['jenis_kelamin'] }}</td>
                                    <td scope="col">{{ $data['tanggal_lahir'] }}</td>
                                    <td scope="col">{{ $data['ipk'] }}</td>
                                    <td scope="col">{{ $data['nama_agama'] }}</td>
                                    <td scope="col">{{ $data['nama_status_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['nama_periode_masuk'] }}</td>
                                    <td scope="col">{{ $data['prodi']['nama_program_studi'] }}</td>
                                    <td scope="col">{{ $data['prodi']['jenjang_pendidikan']['nama_jenjang_didik'] }}
                                    </td>
                                    <td scope="col">{{ $data['prodi']['kode_program_studi'] }}</td>
                                    <td scope="col">{{ $data['id_registrasi_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['id_mahasiswa'] }}</td>
                                    <td scope="col">{{ $data['id_sms'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="text-center text-gray-600">
                            {{ __('Data mahasiswa tidak tersedia.') }}
                        </div>
                    @endif
                </div>
                {{-- Pagination --}}
                <div class="px-8 py-4">
                    {{ $list_mahasiswa->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
