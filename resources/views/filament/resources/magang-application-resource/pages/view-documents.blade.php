<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    @php
        // Documents are already processed and passed as array
        $docs = $documents;
    @endphp
    @forelse($docs as $doc)
        @php
            $url = \Illuminate\Support\Facades\Storage::url($doc);
            $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
        @endphp
        <div class=" rounded-lg overflow-hidden shadow-sm">
            @if($isImage)
                <img src="{{ $url }}" alt="Preview" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 ">
                     <iframe src="{{ $url }}" class="w-full h-full" style="border:none;"></iframe>
                </div>
            @endif
            
            <div class="p-3 flex items-center justify-between text-sm">
                <span class="truncate font-medium" title="{{ basename($doc) }}">
                    {{ \Illuminate\Support\Str::limit(basename($doc), 20) }}
                </span>
                <a href="{{ $url }}" target="_blank" class="text-primary-600 hover:text-primary-500 font-bold hover:underline shrink-0 ml-2">
                    Lihat
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center p-4 text-gray-500 italic">
            Tidak ada berkas yang dilampirkan.
        </div>
    @endforelse
</div>
