@props(['name' => 'prodi-modal', 'maxWidth' => 'lg'])

<div x-data="{
    data: [],
    showModal: false,
    isLoading: false,
    isStatusShown: false,

    {{-- 
        0 : Sync Gagal
        1 : Sync Berhasil
        2 : Fetch Gagal
    --}}
    syncedStatus: 0,

    fetchData() {
        this.isLoading = true;
        if (this.data.length > 0) {
            this.showModal = true;
            this.isLoading = false;
            return;
        }

        fetch('/admin/seeder/prodi')
            .then(res => res.json())
            .then(result => {
                this.data = result;
                this.showModal = true;
                this.isLoading = false;
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;

                this.syncedStatus = 2;
                setTimeout(() => {
                    this.isStatusShown = true;
                }, 250)
                setTimeout(() => {
                    this.isStatusShown = false;
                }, 2000)
            });
    },
    syncData() {
        this.showModal = false;
        this.isLoading = true;

        fetch('/admin/seeder/prodi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cloud_data: this.data
                })
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
                console.error(err);
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

        fetch('{{ route('admin.prodi.export-current') }}', {
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
                a.download = 'export_prodi.xlsx';
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
    <div class="flex justify-end w-full gap-2">
        <button type="button" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600" x-on:click="exportData()">
            Export Excel
        </button>
        <button type="button" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600" x-on:click="fetchData()">
            Sinkronkan Data
        </button>
    </div>

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
        <div class="w-full max-w-4xl p-6 overflow-scroll bg-white rounded-lg shadow-lg max-h-[90dvh]">
            <!-- Modal Header -->
            <div class="flex justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Program Studi Data (PDDikti)</h2>
                <button type="button" class="text-gray-500 hover:text-gray-700" x-on:click="showModal = false">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <template x-if="data.length > 0">
                <table class="w-full border border-collapse border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border border-gray-300">#</th>
                            <th class="px-4 py-2 border border-gray-300">ID Prodi</th>
                            <th class="px-4 py-2 border border-gray-300">Kode Prodi</th>
                            <th class="px-4 py-2 border border-gray-300">Nama Prodi</th>
                            <th class="px-4 py-2 border border-gray-300">Status</th>
                            <th class="px-4 py-2 border border-gray-300">Nama Jenjang Pendidikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in data" :key="index">
                            <tr>
                                <td class="px-4 py-2 border border-gray-300" x-text="index + 1"></td>
                                <td class="px-4 py-2 border border-gray-300" x-text="item.id_prodi"></td>
                                <td class="px-4 py-2 border border-gray-300" x-text="item.kode_program_studi"></td>
                                <td class="px-4 py-2 border border-gray-300" x-text="item.nama_program_studi"></td>
                                <td class="px-4 py-2 border border-gray-300" x-text="item.status"></td>
                                <td class="px-4 py-2 border border-gray-300" x-text="item.nama_jenjang_pendidikan"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>

            <!-- Loading or No Data -->
            <template x-if="data.length === 0">
                <p class="text-gray-500">Data tidak ditemukan.</p>
            </template>

            {{-- Button Container --}}
            <div class="flex justify-end gap-4 mt-6">
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
