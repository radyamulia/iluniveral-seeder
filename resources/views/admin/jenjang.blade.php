<x-app-layout>
  <x-slot name="header">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
          {{ __('Jenjang Pendidikan') }}
      </h2>
  </x-slot>

  <main class="py-12">
      <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">
          {{-- Modal Trigger Button --}}
          <x-modal-jenjang name="jenjang-modal" />

          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
              {{-- Data Table --}}
              <div class="p-6 space-y-8 overflow-scroll text-gray-900 scrollbar-hide">
                  @if (count($list_jenjang) > 0)
                      @php $no = 1; @endphp
                      <table class="w-full">
                          <tr class="[&>th]:text-center [&>th]:border [&>th]:p-2">
                              <th scope="col">ID</th>
                              <th scope="col">ID Jenjang Pendidikan</th>
                              <th scope="col">Nama Jenjang Pendidikan</th>
                          </tr>
                          @foreach ($list_jenjang as $data)
                              <tr class="[&>td]:text-center [&>td]:border [&>td]:p-2">
                                  <td scope="col">{{ $data['id'] }}</td>
                                  <td scope="col">{{ $data['id_jenjang_didik'] }}</td>
                                  <td scope="col">{{ $data['nama_jenjang_didik'] }}</td>
                              </tr>
                          @endforeach
                      </table>
                  @else
                      <div class="text-center text-gray-600">
                          {{ __('Data jenjang pendidikan belum tersedia.') }}
                      </div>
                  @endif

                  {{-- Pagination --}}
                  {{ $list_jenjang }}
              </div>
          </div>
      </div>
  </main>
</x-app-layout>
