{{-- Flash Messages Partial --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition
     class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 mb-6 flex items-center justify-between text-sm font-medium">
    <div class="flex items-center gap-2">✅ {{ session('success') }}</div>
    <button @click="show = false" class="text-green-600 hover:text-green-800 ml-4 text-lg leading-none">&times;</button>
</div>
@endif

@if(session('error') || $errors->any())
<div x-data="{ show: true }" x-show="show" x-transition
     class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 mb-6 text-sm font-medium">
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 font-semibold mb-1">⚠️ Terjadi Kesalahan</div>
            @if($errors->any())
                <ul class="list-disc pl-5 space-y-1 text-red-600">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @else
                {{ session('error') }}
            @endif
        </div>
        <button @click="show = false" class="text-red-600 hover:text-red-800 ml-4 text-lg leading-none">&times;</button>
    </div>
</div>
@endif

@if(session('info'))
<div x-data="{ show: true }" x-show="show" x-transition
     class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-5 py-4 mb-6 flex items-center justify-between text-sm font-medium">
    <div class="flex items-center gap-2">ℹ️ {{ session('info') }}</div>
    <button @click="show = false" class="text-blue-600 hover:text-blue-800 ml-4 text-lg leading-none">&times;</button>
</div>
@endif
