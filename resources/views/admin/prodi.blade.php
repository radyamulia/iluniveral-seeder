<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Program Studi') }}
        </h2>
    </x-slot>

    <main class="py-12">
        <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">
            {{-- Modal Trigger Button --}}
            <x-modal-prodi name="prodi-modal" />

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.prodi') }}" class="mb-6">
                <div class="flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari program studi..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button type="submit" class="px-4 py-2 ml-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                        Cari
                    </button>
                </div>
            </form>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                {{-- Data Table --}}
                <div class="p-6 space-y-8 overflow-scroll text-gray-900">
                    @if (count($list_prodi) > 0)
                        @php $no = 1; @endphp
                        <table class="w-full">
                            <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                                <th scope="col">ID</th>
                                <th scope="col">Kode Program Studi</th>
                                <th scope="col">Nama Program Studi</th>
                                <th scope="col">Status</th>
                                <th scope="col">Jenjang Pendidikan</th>
                            </tr>
                            @foreach ($list_prodi as $data)
                                <tr class="[&>td]:text-center [&>td]:border [&>td]:p-2">
                                    <td scope="col">{{ $data['id_prodi'] }}</td>
                                    <td scope="col">{{ $data['kode_program_studi'] }}</td>
                                    <td scope="col">{{ $data['nama_program_studi'] }}</td>
                                    <td scope="col">{{ $data['status'] }}</td>
                                    <td scope="col">{{ $data['jenjang_pendidikan']['nama_jenjang_didik'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="text-center text-gray-600">
                            {{ __('Data prodi tidak tersedia.') }}
                        </div>
                    @endif

                    {{-- Pagination --}}
                    {{ $list_prodi->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
