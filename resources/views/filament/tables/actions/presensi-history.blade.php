<div class="space-y-4">
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @php
                    $history = \App\Models\Presensi::where('peserta_id', $record->peserta_id)
                        ->orderBy('date', 'desc')
                        ->get();
                @endphp
                @foreach($history as $item)
                    <tr>
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
                    </tr>
                @endforeach
                @if($history->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada riwayat presensi.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
