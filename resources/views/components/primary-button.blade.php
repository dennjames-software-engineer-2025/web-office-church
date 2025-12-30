<button {{ $attributes->merge(['class' => '
    inline-flex items-center px-5 py-2.5 rounded-md
    bg-blue-600 text-white font-semibold shadow
    hover:bg-blue-700 hover:shadow-md
    active:bg-blue-800 transition duration-150
']) }}>
    {{ $slot }}
</button>
