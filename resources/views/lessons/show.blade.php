<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('lessons.index', $lesson->id) }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $lesson->title }}
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Published {{ $lesson->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('lessons.show', $lesson->id) }}" class="btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Lesson
                    </a>
                @endif
                <button class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                    <i class="fas fa-bookmark text-gray-600"></i>
                </button>
                <button class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                    <i class="fas fa-share text-gray-600"></i>
                </button>
            </div>
        </div>
    </x-slot>

    <div id="app">
        <lesson-show :lesson="{{ $lesson }}"></lesson-show>
    </div>
</x-app-layout>
