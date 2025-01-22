<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-2 text-gray-90">
                    <h1 class="mb-4 font-bold">{{ __('Selamat datang di aplikasi BPH UNIVERAL!') }}</h1>
                    <p>
                        Aplikasi ini dibuat untuk memanajemen data mahasiswa, dosen, prodi, dan jenjang pendidikan.
                        Manajemen data dilakukan dengan melakukan sinkronisasi data terhadap data pada PDDikti.
                    </p>
                    <p>
                        Tahapan sinkronisasi:
                    </p>
                    <ol class="[&>li]:list-decimal [&>li]:list-inside pl-2 space-y-2">
                        <li class="[&>a]:text-blue-500 [&>a]:underline">
                            Pilih menu sesuai kebutuhan Anda!
                            <a href="/admin/mahasiswa">mahasiswa</a>,
                            <a href="/admin/prodi">program studi</a>,
                            <a href="/admin/jenjang-didik">jenjang pendidikan</a>
                        </li>
                        <li>Anda dapat melihat data yang tersimpan pada Database untuk setiap halamannya.</li>
                        <li>Untuk melakukan sinkronisasi data, tekan tombol <span
                                class="p-1 text-white bg-blue-500 rounded">Sinkronisasi Data</span>.</li>
                        <li>Kemudian akan muncul pop-up, yang menampilkan data yang ditarik dari PDDikti.</li>
                        <li>Untuk melakukan update data pada database, scroll ke bawah dan tekan tombol <span
                                class="p-1 text-white bg-blue-500 rounded">Sinkronkan</span>.</li>
                        <li>Untuk menutup pop-up, tekan <span class="p-1 text-white bg-red-500 rounded">Batal</span>.
                        </li>
                    </ol>
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 [&>code]:p-1 [&>code]:bg-slate-200 [&>code]:rounded">
                        Beberapa page menampilkan <strong>tabel yang terpotong</strong>, untuk melihat data lakukan
                        <code>scroll</code> secara horizontal. Atau jika menggunakan <i>mouse</i>, tekan
                        <code>shift</code> + <code>scroll ke bawah</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
