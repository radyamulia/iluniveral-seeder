<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Mata Kuliah') }}
        </h2>
    </x-slot>

    <main class="py-12">
        <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">
            {{-- Modal Trigger Button --}}
            <x-modal-matakuliah name="matakuliah-modal" />

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.matakuliah') }}" class="mb-6">
                <div class="flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari mata kuliah..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button type="submit" class="px-4 py-2 ml-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                        Cari
                    </button>
                </div>
            </form>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                {{-- Data Table --}}
                <div class="p-6 space-y-8 overflow-scroll text-gray-900">
                    @if (count($list_matakuliah) > 0)
                        @php $no = 1; @endphp
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">Kode Mata Kuliah</th>
                                <th scope="col">Nama Mata Kuliah</th>
                                <th scope="col">SKS Mata Kuliah</th>
                                <th scope="col">Jenis Mata Kuliah</th>
                                <th scope="col">Kelompok Mata Kuliah</th>
                                <th scope="col">Program Studi</th>
                                <th scope="col">Jenjang</th>
                                <th scope="col">SKS Tatap Muka</th>
                                <th scope="col">SKS Praktek</th>
                                <th scope="col">SKS Praktek Lapangan</th>
                                <th scope="col">SKS Simulasi</th>
                                <th scope="col">Status SAP</th>
                                <th scope="col">Status Silabus</th>
                                <th scope="col">Status Bahan Ajar</th>
                                <th scope="col">Status Acara Praktek</th>
                                <th scope="col">Status Diktat</th>
                                <th scope="col">Tanggal Mulai Efektif</th>
                                <th scope="col">Tanggal Selesai Efektif</th>
                                <th scope="col">ID Jenis Mata Kuliah</th>
                                <th scope="col">ID Kelompok Mata Kuliah</th>
                            </tr>
                            @foreach ($list_matakuliah as $data)
                                <tr class="[&>td]:text-center [&>td]:border [&>td]:p-2">
                                    <td scope="col">{{ $data['kode_mata_kuliah'] }}</td>
                                    <td scope="col">{{ $data['nama_mata_kuliah'] }}</td>
                                    <td scope="col">{{ $data['sks_mata_kuliah'] }}</td>

                                    {{-- <td scope="col">{{ $data['jns_mk'] }}</td> --}}
                                    <td scope="col">
                                        @php
                                            $jns_mk = [
                                                'A' => 'Wajib',
                                                'B' => 'Pilihan',
                                                'C' => 'Wajib Peminatan',
                                                'D' => 'Pilihan Peminatan',
                                                'S' => 'Tugas Akhir/Skripsi/Disertasi',
                                            ];
                                        @endphp

                                        {{ $jns_mk[$data['jns_mk']] ?? '' }}
                                    </td>

                                    {{-- <td scope="col">{{ $data['kel_mk'] }}</td> --}}
                                    <td scope="col">
                                        @php
                                            $kel_mk = [
                                                'A' => 'MPK',
                                                'B' => 'MKK',
                                                'C' => 'MKB',
                                                'D' => 'MPB',
                                                'E' => 'MBB',
                                                'F' => 'MKU / MKDU',
                                                'F' => 'MKU / MKDU',
                                                'G' => 'MKDK',
                                                'H' => 'MKK',
                                            ];
                                        @endphp

                                        {{ $kel_mk[$data['kel_mk']] ?? '' }}
                                    </td>

                                    <td scope="col">{{ $data['prodi']['nama_program_studi'] }}</td>
                                    <td scope="col">{{ $data['jenjang_pendidikan']['nama_jenjang_didik'] }}</td>

                                    <td scope="col">{{ $data['sks_tatap_muka'] }}</td>
                                    <td scope="col">{{ $data['sks_praktek'] }}</td>
                                    <td scope="col">{{ $data['sks_praktek_lapangan'] }}</td>
                                    <td scope="col">{{ $data['sks_simulasi'] }}</td>

                                    <td scope="col">{{ $data['ada_sap'] === 1 ? 'Ada' : '' }}</td>
                                    <td scope="col">{{ $data['ada_silabus'] === 1 ? 'Ada' : '' }}</td>
                                    <td scope="col">{{ $data['ada_bahan_ajar'] === 1 ? 'Ada' : '' }}</td>
                                    <td scope="col">{{ $data['ada_acara_praktek'] === 1 ? 'Ada' : '' }}</td>
                                    <td scope="col">{{ $data['ada_diktat'] === 1 ? 'Ada' : '' }}</td>

                                    <td scope="col">{{ $data['tanggal_mulai_efektif'] }}</td>
                                    <td scope="col">{{ $data['tanggal_selesai_efektif'] }}</td>
                                    <td scope="col">{{ $data['id_jenis_mata_kuliah'] }}</td>
                                    <td scope="col">{{ $data['id_kelompok_mata_kuliah'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="text-center text-gray-600">
                            {{ __('Data mata kuliah tidak tersedia.') }}
                        </div>
                    @endif
                </div>
                {{-- Pagination --}}
                <div class="px-8 py-4">
                    {{ $list_matakuliah->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
