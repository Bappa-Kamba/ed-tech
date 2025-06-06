<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 leading-tight gradient-text">
                    Available Lessons
                </h2>
                <p class="text-gray-600 mt-1">Explore and learn from our comprehensive course collection</p>
            </div>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('lessons.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Create New Lesson
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Search and Filter Bar -->
            <div class="glass rounded-xl p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               placeholder="Search lessons..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/80">
                    </div>
                    <div class="flex items-center space-x-4">
                        <select class="px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/80">
                            <option>All Categories</option>
                            <option>Programming</option>
                            <option>Science</option>
                            <option>Mathematics</option>
                        </select>
                        <button class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <i class="fas fa-filter text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lessons Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="glass rounded-xl p-6 text-center">
                    <i class="fas fa-book text-3xl text-blue-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $lessons->count() }}</h3>
                    <p class="text-gray-600">Total Lessons</p>
                </div>
                <div class="glass rounded-xl p-6 text-center">
                    <i class="fas fa-users text-3xl text-green-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'student')->count() }}</h3>
                    <p class="text-gray-600">Active Learners</p>
                </div>
                <div class="glass rounded-xl p-6 text-center">
                    <i class="fas fa-comments text-3xl text-purple-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-gray-900">{{ \App\Models\Question::count() }}</h3>
                    <p class="text-gray-600">Questions Asked</p>
                </div>
            </div>

            <!-- Lessons Grid -->
            @if($lessons->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($lessons as $lesson)
                <div class="glass rounded-xl overflow-hidden hover-lift group">
                    <!-- Lesson Header -->
                    <div class="relative">
                        <div class="h-48 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 flex items-center justify-center">
                            <i class="fas fa-book-open text-6xl text-white opacity-80"></i>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-medium">
                                New
                            </span>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                                <h3 class="font-bold text-white text-lg line-clamp-2 group-hover:text-blue-200 transition-colors">
                                    {{ $lesson->title }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Lesson Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm line-clamp-3">
                                {{ Str::limit(strip_tags($lesson->content), 120) }}
                            </p>
                        </div>

                        <!-- Lesson Meta -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <i class="fas fa-clock text-xs mr-1"></i>
                                    {{ $lesson->created_at->diffForHumans() }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-comments text-xs mr-1"></i>
                                    {{ $lesson->questions()->count() }} questions
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar (Mock) -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>{{ rand(0, 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" 
                                     style="width: {{ rand(0, 100) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('lessons.show', $lesson->id) }}" 
                               class="flex-1 btn-primary justify-center text-sm py-3">
                                <i class="fas fa-play text-xs"></i>
                                Start Learning
                            </a>
                            <button class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors group">
                                <i class="fas fa-bookmark text-gray-600 group-hover:text-blue-600"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Lesson Footer -->
                    <div class="px-6 pb-6">
                        <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-xs text-gray-600"></i>
                                </div>
                                <span>{{ \App\Models\User::where('id', ($lesson->created_by))->first()->name }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span>{{ number_format(rand(35, 50)/10, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-12">
                <button class="btn-primary">
                    <i class="fas fa-refresh"></i>
                    Load More Lessons
                </button>
            </div>

            @else
            <!-- Empty State -->
            <div class="glass rounded-xl p-12 text-center">
                <div class="mb-6">
                    <i class="fas fa-book-open text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Lessons Available</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    There are currently no lessons to display. 
                    @if(Auth::user()->role === 'admin')
                    Get started by creating your first lesson!
                    @else
                    Please check back later for new content.
                    @endif
                </p>
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('lessons.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Create Your First Lesson
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
