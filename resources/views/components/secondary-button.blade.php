@props(['href' => null])

@if($href)
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => '
            inline-flex items-center px-4 py-2 rounded-md
            bg-gray-400 text-white font-medium shadow-sm
            hover:bg-gray-300 hover:shadow
            active:bg-gray-400 transition
        ']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => '
            inline-flex items-center px-4 py-2 rounded-md
            bg-gray-400 text-white font-medium shadow-sm
            hover:bg-gray-300 hover:shadow
            active:bg-gray-400 transition
        ']) }}>
        {{ $slot }}
    </button>
@endif
