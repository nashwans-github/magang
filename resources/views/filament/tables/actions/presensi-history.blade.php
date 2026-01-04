<div class="space-y-4" x-data="{ showProof: false, proofUrl: '', proofType: '' }">
    <div class="flex items-center gap-4">
        @if($record->peserta->user->avatar_url)
            <img src="{{ $record->peserta->user->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full">
        @else
            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                <span class="text-gray-500 font-bold text-lg">{{ substr($record->peserta->user->name, 0, 1) }}</span>
            </div>
        @endif
        <div>
            <h3 class="text-lg font-bold">{{ $record->peserta->user->name }}</h3>
            <p class="text-sm text-gray-500">{{ $record->peserta->user->email }}</p>
        </div>
    </div>

    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Jam Masuk</th>
                    <th class="px-4 py-2">Jam Keluar</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Keterangan</th>
                    <th class="px-4 py-2 text-center">Bukti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @php
                    $history = \App\Models\Presensi::where('peserta_id', $record->peserta_id)
                        ->orderBy('date', 'desc')
                        ->get();
                @endphp
                @foreach($history as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-2">{{ $item->check_in ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->check_out ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @php
                                $colors = [
                                    'hadir' => 'text-green-600 bg-green-100',
                                    'sakit' => 'text-yellow-600 bg-yellow-100',
                                    'izin' => 'text-blue-600 bg-blue-100',
                                    'alpa' => 'text-red-600 bg-red-100',
                                ];
                                $color = $colors[$item->status] ?? 'text-gray-600 bg-gray-100';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 max-w-xs truncate">{{ $item->notes ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            @if($item->proof_file)
                                <button 
                                    type="button"
                                    @click="
                                        proofUrl = '{{ asset('storage/' . $item->proof_file) }}'; 
                                        proofType = '{{ pathinfo($item->proof_file, PATHINFO_EXTENSION) }}';
                                        showProof = true;
                                    "
                                    class="text-primary-600 hover:text-primary-900 transition flex items-center justify-center w-full"
                                    title="Lihat Bukti"
                                >
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($history->isEmpty())
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Belum ada riwayat presensi.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Proof Display Section (Below Table) -->
    <div 
        x-show="showProof" 
        style="display: none;"
        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
    >
        <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">Pratinjau Bukti</h3>
            <button type="button" @click="showProof = false" class="text-gray-500 hover:text-red-500 transition">
                <span class="flex items-center gap-1 text-sm font-medium">Tutup <x-heroicon-o-x-mark class="w-4 h-4" /></span>
            </button>
        </div>

        <div class="flex items-center justify-center bg-gray-200 dark:bg-gray-800 rounded-lg p-4 min-h-[200px]">
            <template x-if="['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(proofType.toLowerCase())">
                <img :src="proofUrl" class="max-w-full max-h-[400px] rounded shadow-sm object-contain" alt="Bukti Presensi">
            </template>
            
            <template x-if="proofType.toLowerCase() === 'pdf'">
                <iframe :src="proofUrl" class="w-full h-[500px] rounded border border-gray-300 dark:border-gray-600"></iframe>
            </template>
            
            <template x-if="!['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'].includes(proofType.toLowerCase()) && proofUrl">
                <div class="text-center py-8">
                     <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white dark:bg-gray-700 mb-4 shadow-sm">
                        <x-heroicon-o-document-arrow-down class="w-8 h-8 text-primary-600" />
                    </div>
                    <p class="text-gray-900 dark:text-gray-100 font-medium mb-1">File Format: <span x-text="proofType.toUpperCase()"></span></p>
                    <p class="text-gray-500 text-sm mb-4">File ini tidak dapat dipratinjau.</p>
                    <a :href="proofUrl" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 transition">
                        Download File
                    </a>
                </div>
            </template>
        </div>
        
        <div class="mt-2 text-right">
             <a :href="proofUrl" target="_blank" class="text-xs text-primary-600 hover:underline inline-flex items-center gap-1">
                 Buka di Tab Baru <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3"/>
            </a>
        </div>
    </div>
</div>
