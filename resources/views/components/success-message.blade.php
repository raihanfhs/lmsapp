@if (session('success'))
    <div {{ $attributes->merge(['class' => 'mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-600 dark:text-green-300']) }} role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif