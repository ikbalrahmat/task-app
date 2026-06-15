{{-- Flash Messages Partial --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
     class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 mb-6 flex items-center justify-between text-sm font-medium shadow-sm">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 ml-4 p-1 rounded-lg hover:bg-emerald-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

@if(session('error') || $errors->any())
<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
     class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 mb-6 text-sm font-medium shadow-sm">
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 font-semibold mb-1">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Terjadi Kesalahan
            </div>
            @if($errors->any())
                <ul class="list-disc pl-9 space-y-1 text-red-600 text-xs mt-2">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @else
                <div class="text-red-700 mt-1 pl-7 text-xs">{{ session('error') }}</div>
            @endif
        </div>
        <button @click="show = false" class="text-red-600 hover:text-red-800 ml-4 p-1 rounded-lg hover:bg-red-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
@endif

@if(session('info'))
<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
     class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-5 py-4 mb-6 flex items-center justify-between text-sm font-medium shadow-sm">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('info') }}
    </div>
    <button @click="show = false" class="text-blue-600 hover:text-blue-800 ml-4 p-1 rounded-lg hover:bg-blue-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif
