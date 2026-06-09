{{-- Flash Messages Partial --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition
     class="bg-green-950 border border-green-800 text-green-400 rounded-xl px-5 py-4 mb-6 flex items-center justify-between text-sm font-medium">
    <div class="flex items-center gap-2">✅ {{ session('success') }}</div>
    <button @click="show = false" class="text-green-400 hover:text-green-300 ml-4 text-lg leading-none">&times;</button>
</div>
@endif

@if(session('error') || $errors->any())
<div x-data="{ show: true }" x-show="show" x-transition
     class="bg-red-950 border border-red-800 text-red-400 rounded-xl px-5 py-4 mb-6 text-sm font-medium">
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 font-semibold mb-1">⚠️ Terjadi Kesalahan</div>
            @if($errors->any())
                <ul class="list-disc pl-5 space-y-1 text-red-300">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @else
                {{ session('error') }}
            @endif
        </div>
        <button @click="show = false" class="text-red-400 hover:text-red-300 ml-4 text-lg leading-none">&times;</button>
    </div>
</div>
@endif

@if(session('info'))
<div x-data="{ show: true }" x-show="show" x-transition
     class="bg-blue-950 border border-blue-800 text-blue-400 rounded-xl px-5 py-4 mb-6 flex items-center justify-between text-sm font-medium">
    <div class="flex items-center gap-2">ℹ️ {{ session('info') }}</div>
    <button @click="show = false" class="text-blue-400 hover:text-blue-300 ml-4 text-lg leading-none">&times;</button>
</div>
@endif
