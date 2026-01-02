<div class="w-full" style="margin: 0; padding: 0;">
    @php
        $state = $getState();
    @endphp
    @if($state)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($state) }}" 
             style="width: 100%; height: 200px; object-fit: cover; display: block;" 
             class="w-full rounded-t-xl border-none p-0 m-0">
    @else
        <div style="width: 100%; height: 200px;" 
             class="bg-gray-200 rounded-t-xl flex items-center justify-center text-gray-400 w-full">
            <span class="italic">No Image</span>
        </div>
    @endif
</div>
