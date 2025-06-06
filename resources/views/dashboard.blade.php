<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 leading-tight gradient-text">
                    Welcome back, {{ Auth::user()->name }}!
                </h2>
                <p class="text-gray-600 mt-1">Ready to continue your learning journey?</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                    <div class="text-lg font-semibold text-gray-700">{{ now()->format('g:i A') }}</div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Lessons -->
                <div class="glass rounded-xl p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Available Lessons</p>
                            <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Lesson::count() }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-book text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up text-xs"></i> Ready to explore
                        </span>
                    </div>
                </div>

                <!-- Questions Asked -->
                <div class="glass rounded-xl p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Questions Asked</p>
                            <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Question::where('user_id', Auth::id())->count() }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-question-circle text-2xl text-green-600"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-blue-600 text-sm font-medium">
                            <i class="fas fa-brain text-xs"></i> Keep learning!
                        </span>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="glass rounded-xl p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Recent Activity</p>
                            <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Question::where('user_id', Auth::id())->where('created_at', '>=', now()->subDays(7))->count() }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-purple-600 text-sm font-medium">
                            <i class="fas fa-calendar-week text-xs"></i> This week
                        </span>
                    </div>
                </div>

                <!-- Learning Streak -->
                <div class="glass rounded-xl p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Learning Streak</p>
                            <p class="text-3xl font-bold text-gray-900">5</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <i class="fas fa-fire text-2xl text-orange-600"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-orange-600 text-sm font-medium">
                            <i class="fas fa-star text-xs"></i> Days active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Recent Lessons -->
                <div class="lg:col-span-2">
                    <div class="glass rounded-xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Recent Lessons</h3>
                            <a href="{{ route('lessons.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View all <i class="fas fa-arrow-right text-xs ml-1"></i>
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse(\App\Models\Lesson::latest()->take(3)->get() as $lesson)
                            <div class="flex items-start space-x-4 p-4 bg-white/50 rounded-lg hover:bg-white/80 transition-colors">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-book-open text-white text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-semibold text-gray-900 truncate">{{ $lesson->title }}</h4>
                                    <p class="text-gray-600 text-sm line-clamp-2">{{ Str::limit(strip_tags($lesson->content), 100) }}</p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $lesson->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('lessons.show', $lesson->id) }}" class="btn-primary text-sm py-2 px-4">
                                        <i class="fas fa-play text-xs"></i>
                                        Start
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No lessons available yet.</p>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('lessons.create') }}" class="btn-primary mt-4">
                                    <i class="fas fa-plus"></i>
                                    Create First Lesson
                                </a>
                                @endif
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Recent Activity -->
                <div class="space-y-6">
                    
                    <!-- Quick Actions -->
                    <div class="glass rounded-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('lessons.index') }}" class="flex items-center p-3 bg-white/50 rounded-lg hover:bg-white/80 transition-colors group">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200">
                                    <i class="fas fa-graduation-cap text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Browse Lessons</p>
                                    <p class="text-xs text-gray-600">Explore all available content</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 ml-auto group-hover:text-gray-600"></i>
                            </a>

                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('lessons.create') }}" class="flex items-center p-3 bg-white/50 rounded-lg hover:bg-white/80 transition-colors group">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200">
                                    <i class="fas fa-plus text-green-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Create Lesson</p>
                                    <p class="text-xs text-gray-600">Add new educational content</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 ml-auto group-hover:text-gray-600"></i>
                            </a>
                            @endif

                            <div class="flex items-center p-3 bg-white/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">AI Assistant</p>
                                    <p class="text-xs text-gray-600">Available in each lesson</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Questions -->
                    <div class="glass rounded-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Questions</h3>
                        <div class="space-y-3">
                            @forelse(\App\Models\Question::where('user_id', Auth::id())->with('lesson')->latest()->take(3)->get() as $question)
                            <div class="p-3 bg-white/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $question->question }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-500">{{ $question->lesson->title ?? 'Unknown Lesson' }}</span>
                                    <span class="text-xs text-gray-400">{{ $question->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="fas fa-comments text-2xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">No questions asked yet.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
