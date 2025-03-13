<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('IPEPA') }}
        </h2>
    </x-slot>


    <main class="py-12">
        <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">

            <x-modal-ipepa-mhs
                latestDate="{{ count($mahasiswa_datalist['data']) > 0 ? \Carbon\Carbon::parse($mahasiswa_datalist['latest_updated_at'])->locale('id')->translatedFormat('d F Y') : null }}" />

            {{-- Mahasiswa --}}
            <div class="overflow-scroll bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-8 text-gray-900">
                    <h2 class="text-lg font-bold">Mahasiswa</h2>

                    {{-- Data Table --}}
                    @if (count($mahasiswa_datalist['data']) > 0)
                        @php $no = 1; @endphp
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col" rowspan="2">No</th>
                                <th scope="col" rowspan="2">Tahun Akademik</th>
                                <th scope="col" colspan="2">Jumlah Mahasiswa Baru</th>
                                <th scope="col" rowspan="2">Jumlah Mahasiswa Aktif</th>
                                <th scope="col" rowspan="2">Jumlah Lulusan</th>
                            </tr>
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">Regular</th>
                                <th scope="col">Transfer</th>
                            </tr>
                            @foreach ($mahasiswa_datalist['data'] as $data)
                                <tr class="[&>td]:text-center [&>td]:border [&>td>a]:p-2">
                                    <td scope="col">{{ $no++ }}</td>
                                    <td scope="col">{{ $data->nama_periode }}</td>
                                    {{-- Jumlah Maba Start --}}
                                    <td scope="col">
                                        <a href="ipepa/mhs/mahasiswa-baru-regular/{{ $data->id_periode }}"
                                            class="block group size-full hover:bg-slate-50">
                                            <p class="group-hover:hidden">{{ $data->rekap_jumlah_maba_regular }}</p>
                                            <p class="hidden text-blue-500 group-hover:block">Lihat Detail</p>
                                        </a>
                                    </td>
                                    <td scope="col">
                                        <a href="ipepa/mhs/mahasiswa-baru-transfer/{{ $data->id_periode }}"
                                            class="block group size-full hover:bg-slate-50">
                                            <p class="group-hover:hidden">{{ $data->rekap_jumlah_maba_transfer }}</p>
                                            <p class="hidden text-blue-500 group-hover:block">Lihat Detail</p>
                                        </a>
                                    </td>
                                    {{-- Jumlah Maba End --}}
                                    <td scope="col">
                                        <a href="ipepa/mhs/mahasiswa-aktif/{{ $data->id_periode }}"
                                            class="block group size-full hover:bg-slate-50">
                                            <p class="group-hover:hidden">{{ $data->rekap_jumlah_mhs_aktif }}</p>
                                            <p class="hidden text-blue-500 group-hover:block">Lihat Detail</p>
                                        </a>
                                    </td>
                                    <td scope="col">
                                        <a href="ipepa/mhs/lulusan/{{ $data->id_periode }}"
                                            class="block group size-full hover:bg-slate-50">
                                            <p class="group-hover:hidden">{{ $data->rekap_jumlah_lulusan }}</p>
                                            <p class="hidden text-blue-500 group-hover:block">Lihat Detail</p>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <p class="text-center">Data Belum Tersedia.</p>
                    @endif
                    </table>
                </div>
            </div>


            <x-modal-ipepa-dosen-tetap
                latestDate="{{ count($dosen_tetap_datalist['data']) ? \Carbon\Carbon::parse($dosen_tetap_datalist['latest_updated_at'])->locale('id')->translatedFormat('d F Y') : null }}" />

            {{-- Dosen --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-8 text-gray-900">
                    <h2 class="text-lg font-bold">Dosen Tetap</h2>

                    {{-- Data Table --}}
                    @if (count($dosen_tetap_datalist['data']) > 0)
                        @php $no = 1; @endphp
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">No</th>
                                <th scope="col">Nama Dosen Tetap</th>
                                <th scope="col">NIDN/NIDK</th>
                                {{-- <th scope="col">Gelar</th> --}}
                                <th scope="col">Jabatan Akademik</th>
                                <th scope="col">Mata Kuliah yang Diampu</th>
                                <th scope="col">Mata Kuliah yang Bobot Kredit (sks)</th>
                            </tr>
                            @foreach ($dosen_tetap_datalist['data'] as $data)
                                <tr class="[&>td]:text-center [&>td]:border [&>td>a]:p-2">
                                    <td scope="col">{{ $no++ }}</td>
                                    <td scope="col">{{ $data->nama_dosen }}</td>
                                    <td scope="col">{{ $data->nidn_nidk }}</td>
                                    {{-- <td scope="col">{{ $data->gelar }}</td> --}}
                                    <td scope="col">{{ $data->jabatan_akademik }}</td>
                                    {{-- <td scope="col">
                                        <a href="">
                                            {{ $data->jumlah_mata_kuliah }}
                                        </a>
                                    </td> --}}
                                    <td scope="col">
                                        <a href="ipepa/dosen-tetap/{{ $data->id_dosen }}"
                                            class="block group size-full hover:bg-slate-50">
                                            <p class="group-hover:hidden">{{ $data->jumlah_mata_kuliah }}</p>
                                            <p class="hidden text-blue-500 group-hover:block">Lihat Detail</p>
                                        </a>
                                    </td>
                                    <td scope="col">{{ $data->total_sks }}</td>
                                </tr>
                            @endforeach
                        @else
                            <p class="text-center">Data Belum Tersedia.</p>
                    @endif
                    </table>

                </div>
            </div>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

            </div>
        </div>
    </main>
</x-app-layout>
