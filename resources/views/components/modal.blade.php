@props([
    'show' => 'open',
    'title' => 'Modal Title',
])

<div
    x-show="{{ $show }}"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
    aria-modal="true"
    role="dialog"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40" @click="{{ $show }} = false"></div>

    {{-- Modal --}}
    <div class="relative bg-white w-full max-w-lg mx-4 rounded-lg shadow-lg">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h2 class="font-semibold text-lg">{{ $title }}</h2>
            <button type="button"
                    class="px-2 py-1 rounded hover:bg-gray-100"
                    @click="{{ $show }} = false">
                ✕
            </button>
        </div>

        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>