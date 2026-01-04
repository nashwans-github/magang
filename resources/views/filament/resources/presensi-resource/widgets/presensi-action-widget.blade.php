<x-filament-widgets::widget>
    @if($peserta)
        <x-filament::section>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                
                {{-- Kiri: Info Tanggal & Jam --}}
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 rounded-full dark:bg-primary-900/30">
                        <x-heroicon-o-clock class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-950 dark:text-white">
                            {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                        </h2>
                        <div x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }" 
                             x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)">
                            <p class="text-2xl font-mono font-semibold text-primary-600 dark:text-primary-400" x-text="time"></p>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Status & Action Button --}}
                <div class="flex flex-col items-center md:items-end gap-2">
                    @if(!$existingPresensi)
                        {{-- Belum Absen Masuk --}}
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-medium text-gray-500">Anda belum absen hari ini, silahkan absen.</p>
                        </div>
                        
                        {{-- Trigger Modal Action --}}
                        {{ $this->checkInAction }}
                    
                    @elseif($existingPresensi->status !== 'hadir')
                        {{-- Status Izin / Sakit --}}
                        <div class="flex items-center gap-2 text-info-600 dark:text-info-400 bg-info-50 dark:bg-info-900/10 px-4 py-2 rounded-lg border border-info-200 dark:border-info-800">
                             <x-heroicon-o-information-circle class="w-6 h-6" />
                             <span class="font-medium">Status Hari Ini: {{ ucfirst($existingPresensi->status) }}</span>
                        </div>
                        @if($existingPresensi->notes)
                        <div class="text-xs text-gray-400 mt-1 text-right">
                            Ket: {{ Str::limit($existingPresensi->notes, 30) }}
                        </div>
                        @endif

                    @elseif(!$existingPresensi->check_out)
                        {{-- Sudah Masuk (Hadir), Belum Keluar --}}
                        <div class="text-right">
                             <p class="text-sm text-success-600 font-bold mb-1">Anda sudah absen masuk hari ini.</p>
                             <div class="flex flex-col items-end text-xs text-gray-500">
                                <span>Waktu Masuk: <span class="font-mono font-bold">{{ $existingPresensi->check_in }}</span></span>
                                
                                {{-- Durasi Counter Live --}}
                                <span x-data="{ 
                                    start: new Date('{{ \Carbon\Carbon::parse($existingPresensi->date->format('Y-m-d') . ' ' . $existingPresensi->check_in)->toIso8601String() }}'), 
                                    elapsed: '' 
                                }" 
                                x-init="
                                    setInterval(() => {
                                        let now = new Date();
                                        let diff = Math.floor((now - start) / 1000);
                                        let h = Math.floor(diff / 3600).toString().padStart(2, '0');
                                        let m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
                                        let s = (diff % 60).toString().padStart(2, '0');
                                        elapsed = `${h}:${m}:${s}`;
                                    }, 1000)
                                ">
                                    Durasi: <span class="font-mono font-bold text-primary-600" x-text="elapsed">Loading...</span>
                                </span>
                             </div>
                        </div>
                        
                        <x-filament::button wire:click="checkOut" size="lg" color="danger" icon="heroicon-o-arrow-left-start-on-rectangle" wire:loading.attr="disabled">
                            Absen Keluar
                        </x-filament::button>
                    
                    @else
                        {{-- Sudah Lengkap --}}
                        <div class="flex items-center gap-2 text-success-600 dark:text-success-400 bg-success-50 dark:bg-success-900/10 px-4 py-2 rounded-lg border border-success-200 dark:border-success-800">
                             <x-heroicon-o-check-circle class="w-6 h-6" />
                             <span class="font-medium">Presensi Hari Ini Lengkap</span>
                        </div>
                        <div class="text-xs text-gray-400">
                            Masuk: {{ $existingPresensi->check_in }} | Keluar: {{ $existingPresensi->check_out }}
                        </div>
                    @endif
                </div>

            </div>
            
            <x-filament-actions::modals />
        </x-filament::section>
    @else
        <x-filament::section>
             <div class="text-center py-4">
                <p class="text-danger-600">Anda tidak terdaftar sebagai peserta aktif.</p>
             </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
