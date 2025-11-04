@php
    $linkClasses = 'text-primary-600 dark:text-primary-400 hover:underline font-semibold';
@endphp

<div class="space-y-3">
    @if ($url)
        <div>
            <a href="{{ $url }}" target="_blank" rel="noopener" class="{{ $linkClasses }}">
                Lihat gambar ({{ $name ?? 'lampiran' }})
            </a>
        </div>
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <img
                src="{{ $url }}"
                alt="{{ $name ?? 'Screenshot laporan bug' }}"
                class="max-h-[480px] w-full object-contain bg-gray-50 dark:bg-gray-900"
            >
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada gambar yang dilampirkan.</p>
    @endif
</div>
