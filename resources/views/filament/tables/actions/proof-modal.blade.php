<div class="flex flex-col items-center justify-center p-4">
    @if($record->proof_file)
        @php
            $extension = pathinfo($record->proof_file, PATHINFO_EXTENSION);
            $url = asset('storage/' . $record->proof_file);
        @endphp

        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            <div class="relative w-full overflow-hidden rounded-lg shadow-md group">
                <img src="{{ $url }}" alt="Bukti Presensi" class="w-full h-auto object-contain max-h-[80vh]">
                <a href="{{ $url }}" target="_blank" class="absolute bottom-4 right-4 p-2 bg-white/80 rounded-full shadow hover:bg-white transition opacity-0 group-hover:opacity-100" title="Buka di tab baru">
                    <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-gray-700"/>
                </a>
            </div>
        @elseif(strtolower($extension) === 'pdf')
            <iframe src="{{ $url }}" class="w-full h-[80vh] rounded-lg border border-gray-200" frameborder="0"></iframe>
        @else
            <div class="text-center py-8">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                    <x-heroicon-o-document-text class="h-6 w-6 text-gray-600" />
                </div>
                <p class="text-gray-900 font-medium mb-1">File Format: {{ strtoupper($extension) }}</p>
                <p class="text-gray-500 text-sm mb-6">File ini tidak dapat dipratinjau langsung.</p>
                <a href="{{ $url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2"/>
                    Download / Buka
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-exclamation-circle class="w-12 h-12 mx-auto mb-2 text-gray-400"/>
            <p>Tidak ada bukti lampiran untuk presensi ini.</p>
        </div>
    @endif
</div>
