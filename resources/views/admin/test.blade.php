<x-app-layout>
    @if (count($list) > 0)
        @php $no = 1; @endphp
        <div class="w-full">
            @foreach ($list as $data)
                <div class="grid grid-cols-4 gap-2">
                    <p>{{ $data['nama_program_studi'] }}</p>
                    <p>{{ $data['id_periode'] }}</p>
                    <p>{{ $data['nama_periode'] }}</p>
                    <p>{{ (int) $data['aktif'] + (int) $data['cuti'] + (int) $data['sedang_double_degree'] }}</p>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-600">
            {{ __('Data mahasiswa tidak tersedia.') }}
        </div>
    @endif
</x-app-layout>
