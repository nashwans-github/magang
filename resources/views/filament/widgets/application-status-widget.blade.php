<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $application = $this->getViewData()['application'];
            $statusColors = [
                'pending' => 'text-yellow-600 bg-yellow-50 ring-yellow-600/20',
                'approved' => 'text-green-600 bg-green-50 ring-green-600/20',
                'rejected' => 'text-red-600 bg-red-50 ring-red-600/20',
            ];
            $statusLabels = [
                'pending' => 'Menunggu Konfirmasi',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
            ];
        @endphp

        @if($application)
            <div class="flex flex-col gap-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                        Halo, {{ auth()->user()->name }}!
                    </h2>
                    <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">
                        Berikut adalah status terkini permohonan magang Anda.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4 mb-4 dark:border-white/10">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Permohonan</p>
                            @php
                                $statusStyle = match($application->status) {
                                    'approved' => 'background-color: transparent; color: #059669; border: 2px solid #059669; font-weight: 600;', // Emerald-600
                                    'rejected' => 'background-color: transparent; color: #dc2626; border: 2px solid #dc2626; font-weight: 600;', // Red-600
                                    default => 'background-color: transparent; color: #d97706; border: 2px solid #d97706; font-weight: 600;',   // Amber-600
                                };
                            @endphp
                            <div class="mt-1 inline-flex items-center rounded-md px-3 py-1 text-sm" style="{{ $statusStyle }}">
                                {{ $statusLabels[$application->status] ?? ucfirst($application->status) }}
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                            <p class="mt-1 text-gray-950 dark:text-white">{{ $application->created_at->format('d F Y') }}</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Instansi Asal</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-950 dark:text-white">{{ $application->institution_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tujuan OPD</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-950 dark:text-white">{{ $application->opd->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Periode Magang</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-950 dark:text-white">
                                {{ \Carbon\Carbon::parse($application->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($application->end_date)->format('d M Y') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                
                @if($application->documents)
                    <div class="mt-8">
                         <h3 class="text-lg font-medium mb-4">Berkas Persyaratan</h3>
                         <div class="grid grid-cols-1 gap-4">
                            @php
                                $docs = $application->documents;
                                if (is_string($docs)) {
                                    $decoded = json_decode($docs, true);
                                    if (is_array($decoded)) {
                                        $docs = $decoded;
                                    }
                                }
                                $docs = \Illuminate\Support\Arr::wrap($docs);
                            @endphp

                            @foreach($docs as $doc)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-2 border-b flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600 truncate">Dokumen {{ $loop->iteration }}</span>
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($doc) }}" target="_blank" class="text-xs text-primary-600 hover:text-primary-500 font-medium">Download</a>
                                    </div>
                                    <div class="w-full bg-gray-100 relative group">
                                         @if(Str::endsWith(strtolower($doc), '.pdf'))
                                            <iframe src="{{ \Illuminate\Support\Facades\Storage::url($doc) }}" class="w-full" style="height: 1150px; border: none;"></iframe>
                                         @elseif(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png']))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($doc) }}" class="w-full object-contain" style="max-height: 1150px;">
                                         @else
                                            <div class="flex items-center justify-center p-10">
                                                <div class="text-center">
                                                    <x-heroicon-o-document class="mx-auto h-12 w-12 text-gray-400" />
                                                    <span class="mt-2 block text-sm font-medium text-gray-900">Preview tidak tersedia</span>
                                                </div>
                                            </div>
                                         @endif
                                    </div>
                                </div>
                            @endforeach
                         </div>
                    </div>
                @endif
                
                @if($application->status === 'pending')
                    <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/30">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-m-information-circle class="h-5 w-5 text-blue-400" />
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-blue-700 dark:text-blue-200">
                                    Permohonan Anda sedang ditinjau oleh tim kami. Mohon cek secara berkala untuk pembaruan status.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-10">
                <x-heroicon-o-document-text class="mx-auto h-16 w-16 text-gray-300" />
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Belum ada permohonan</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Anda belum mengajukan permohonan magang. Silakan ajukan sekarang untuk memulai.</p>
                <div class="mt-6">
                    <x-filament::button
                        tag="a"
                        :href="\App\Filament\Resources\MagangApplicationResource::getUrl('create')"
                        size="lg"
                    >
                        Ajukan Permohonan Magang
                    </x-filament::button>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
