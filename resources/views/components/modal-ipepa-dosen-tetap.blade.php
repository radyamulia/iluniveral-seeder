@props(['name' => 'ipepa-modal', 'maxWidth' => 'xl', 'latestDate' => null])

<div x-data="{
    data: null,
    currentPage: 1,
    totalPages: 0,
    showModal: false,
    isLoading: false,
    isStatusShown: false,

    {{-- 
        0 : Sync Gagal
        1 : Sync Berhasil
        2 : Fetch Gagal
    --}}
    syncedStatus: 0,
    syncData() {
        this.showModal = false;
        this.isLoading = true;

        fetch('/admin/seeder/ipepa/dosen', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(res => res.json())
            .then(result => {
                this.isLoading = false;

                this.syncedStatus = 1;
                setTimeout(() => {
                    this.isStatusShown = true;
                }, 250)

                setTimeout(() => {
                    this.isStatusShown = false;
                    {{-- page reload --}}
                    window.location.reload();
                }, 2000)
            })
            .catch(err => {
                console.error(err)
                this.isLoading = false;

                this.syncedStatus = 0;
                setTimeout(() => {
                    this.isStatusShown = true;
                }, 250)

                setTimeout(() => {
                    this.isStatusShown = false;
                }, 2000)
            });
    },
    exportData() {
        this.isLoading = true;

        fetch('{{ route('admin.dosen.export-current') }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Export failed');
            })
            .then(blob => {
                this.isLoading = false;

                // Berhasil eksport
                this.syncedStatus = 3; // Status 3 untuk Export Excel Berhasil
                setTimeout(() => {
                    this.isStatusShown = true;
                }, 250);

                setTimeout(() => {
                    this.isStatusShown = false;
                }, 2000);

                // Buat download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'export_dosen.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;

                // Gagal eksport
                this.syncedStatus = 4; // Status 4 untuk Export Excel Gagal
                setTimeout(() => {
                    this.isStatusShown = true;
                }, 250);

                setTimeout(() => {
                    this.isStatusShown = false;
                }, 2000);
            });
    },
}" class="relative">
    <!-- Trigger Button -->
    <section class="flex items-center justify-between">
        <p>Data per Tanggal: {{ $latestDate != null ? $latestDate : 'Belum ada data' }}</p>
        <button type="button" class="px-4 py-2 text-white bg-blue-500 rounded-lg cursor-pointer hover:bg-blue-600"
            x-on:click="() => { showModal = true }">Sinkronkan Data</button>
    </section>

    {{-- Sync Status --}}
    <div x-show="isStatusShown" class="fixed top-6 right-6">
        <!-- Sync Messages -->
        <template
            x-for="(message, index) in [
        { status: 0, text: 'Sinkronisasi Gagal!', color: 'bg-red-400' },
        { status: 1, text: 'Sinkronisasi Berhasil!', color: 'bg-green-400' },
        { status: 2, text: 'Gagal menampilkan data dari server.', color: 'bg-red-400' },
        { status: 3, text: 'Export Excel Berhasil!', color: 'bg-green-400' },
        { status: 4, text: 'Export Excel Gagal!', color: 'bg-red-400' }
    ]"
            :key="index">
            <p x-show="syncedStatus === message.status" x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
                :class="`p-4 text-white rounded ${message.color}`">
                <span x-text="message.text"></span>
            </p>
        </template>
    </div>

    {{-- Loading Indicator --}}
    <div x-show="isLoading" x-cloak class="fixed inset-0 z-50 grid bg-black bg-opacity-25 place-items-center"
        x-transition.opacity>
        <div class="w-16 h-16 border-4 border-gray-300 rounded-full border-t-blue-500 animate-spin"></div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center size-screen">
        <div class="absolute bg-gray-800 bg-opacity-75 -z-10 size-full" x-on:click="showModal = false"></div>
        <div class="w-full max-w-xl p-6 overflow-scroll bg-white rounded-lg shadow-lg max-h-[90dvh]">
            <!-- Modal Header -->
            <div class="flex flex-col gap-6 mb-4">
                <div class="flex justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Apakah Anda yakin ingin melakukan sinkronisasi?</h2>
                    <button type="button" class="text-gray-500 hover:text-gray-700" x-on:click="showModal = false">
                        &times;
                    </button>
                </div>
                <p>Proses sinkronisasi akan memakan waktu yang cukup lama. Karena akan dilakukan penyesuaian dari
                    beberapa tabel data.</p>
            </div>

            {{-- Button Container --}}
            <div class="flex justify-center gap-8 mt-14">
                <button class="px-4 py-2 font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600"
                    x-on:click="showModal = false">
                    Batal
                </button>
                <button class="px-4 py-2 font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600"
                    x-on:click="syncData()">
                    Sinkronkan
                </button>
            </div>
        </div>
    </div>
</div>
