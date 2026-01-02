<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Persetujuan Presensi ({{ count($approvals) }})</h2>
        </div>

        @if($approvals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($approvals as $item)
                    <div class="p-4 bg-white dark:bg-gray-900 border border-gray-600 rounded-lg shadow-sm flex flex-col gap-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-lg">{{ $item->peserta->user->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $item->peserta->bidang->name ?? '-' }}</p>
                            </div>
                            <x-filament::badge :color="$item->status === 'hadir' ? 'success' : 'warning'">
                                {{ ucfirst($item->status) }}
                            </x-filament::badge>
                        </div>
                        
                        <div class="text-sm space-y-1 my-2">
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</p>
                            @if($item->status === 'hadir')
                                <p><strong>Masuk:</strong> {{ $item->check_in }}</p>
                                <p><strong>Keluar:</strong> {{ $item->check_out ?? '-' }}</p>
                            @endif
                            
                            @if($item->notes)
                                <div class="bg-gray-50 p-2 rounded text-xs italic dark:bg-gray-800">
                                    "{{ $item->notes }}"
                                </div>
                            @endif
                            
                            @if($item->proof_file)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($item->proof_file) }}" target="_blank" class="text-primary-600 underline text-xs">
                                    Lihat Bukti
                                </a>
                            @endif
                        </div>

                        <div class="mt-auto pt-2 border-t border-gray-600 flex justify-end">
                            <x-filament::button size="sm" wire:click="approve({{ $item->id }})">
                                Setujui
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-8">
                <x-heroicon-o-check-circle class="w-8 h-8 mx-auto mb-2 opacity-50" />
                <p>Tidak ada presensi yang perlu disetujui.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
