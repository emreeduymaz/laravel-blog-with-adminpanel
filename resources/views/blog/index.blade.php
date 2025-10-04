@extends('layouts.blog')

@section('title', 'Blog')
@section('description', 'Discover latest articles about technology, web development, and design')

@section('content')
<!-- Hero Section -->
<div class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Welcome to Our Blog
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Discover the latest insights in technology, web development, and design
            </p>
            
            <!-- Search Form -->
            <form method="GET" action="{{ route('blog.index') }}" class="max-w-md mx-auto">
                <div class="flex">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search articles..." 
                           class="flex-1 px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <button type="submit" 
                            class="bg-blue-800 hover:bg-blue-900 px-6 py-3 rounded-r-lg transition-colors">
                        🔍
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Featured Posts -->
    @if($featuredPosts->count() > 0)
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Featured Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredPosts as $post)
            <!-- DEĞİŞTİ: article relative + görünmez tam ekran link, başlıktaki iç <a> kaldırıldı -->
            <article class="relative bg-white rounded-xl shadow-lg overflow-hidden card-hover cursor-pointer">
                <!-- Stretched link -->
                <a href="{{ route('blog.show', $post->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $post->title }}"></a>

                @if($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                    <span class="text-white text-4xl">📝</span>
                </div>
                @endif
                
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="badge relative z-20" style="background-color: {{ $post->category->color }}">
                            {{ $post->category->name }}
                        </span>
                        <span class="text-yellow-500 ml-2">⭐</span>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-blue-600 truncate">
                        {{ $post->title }}
                    </h3>
                    
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $post->excerpt }}
                    </p>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>{{ $post->user->name }}</span>
                        <span>{{ $post->published_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Latest Articles</h2>
            
            @if($posts->count() > 0)
            <div class="space-y-8">
                @foreach($posts as $post)
                <!-- DEĞİŞTİ: article relative + görünmez tam ekran link, başlıktaki iç <a> kaldırıldı, metin alanına min-w-0 -->
                <article class="relative bg-white rounded-xl shadow-lg overflow-hidden card-hover cursor-pointer">
                    <!-- Stretched link -->
                    <a href="{{ route('blog.show', $post->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $post->title }}"></a>

                    <div class="md:flex">
                        <div class="md:w-1/3">
                            @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-48 md:h-full object-cover">
                            @else
                            <div class="w-full h-48 md:h-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white text-4xl">📝</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="md:w-2/3 p-6 min-w-0">
                            <div class="flex items-center mb-3">
                                <span class="badge relative z-20" style="background-color: {{ $post->category->color }}">
                                    {{ $post->category->name }}
                                </span>
                                @if($post->is_featured)
                                <span class="text-yellow-500 ml-2">⭐</span>
                                @endif
                            </div>
                            
                            <h3 class="text-2xl font-bold text-gray-800 mb-3 hover:text-blue-600 truncate">
                                {{ $post->title }}
                            </h3>
                            
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                {{ $post->excerpt }}
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>👤 {{ $post->user->name }}</span>
                                    <span>📅 {{ $post->published_at->format('M j, Y') }}</span>
                                    <span>👁️ {{ number_format($post->views_count) }}</span>
                                    <span>⏱️ {{ $post->reading_time }} min read</span>
                                </div>
                                
                                <div class="flex space-x-2">
                                    @foreach($post->tags as $tag)
                                    <!-- İstersen bunları route ile link yap: a.relative.z-20 -->
                                    <a href="{{ route('blog.tag', $tag->slug) }}" 
                                       class="badge text-xs relative z-20 hover:opacity-80 transition-opacity" 
                                       style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                {{ $posts->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No articles found</h3>
                <p class="text-gray-600">Try adjusting your search or filters.</p>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            @guest
            <div class="space-y-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Giriş yap</h3>
                    <p class="text-gray-600 mb-4">Favori yazıları kaydetmek ve yorumları daha hızlı bırakmak için giriş yap.</p>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors w-full justify-center">
                        Giriş Yap
                    </a>
                </div>

                @if (Route::has('register'))
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hesap oluştur</h3>
                    <p class="text-gray-600 mb-4">Topluluğumuza katıl ve yeni yazılardan haberdar ol.</p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center bg-gray-800 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium transition-colors w-full justify-center">
                        Kayıt Ol
                    </a>
                </div>
                @endif
            </div>
            @endguest
            <!-- Categories -->
            @if($categories->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Categories</h3>
                <div class="space-y-3">
                    @foreach($categories as $category)
                    <a href="{{ route('blog.category', $category->slug) }}" 
                       class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                            <span class="font-medium text-gray-800">{{ $category->name }}</span>
                        </div>
                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                            {{ $category->posts_count }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Tags -->
            @if($tags->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Popular Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" 
                       class="badge hover:opacity-80 transition-opacity" 
                       style="background-color: {{ $tag->color }}">
                        {{ $tag->name }} ({{ $tag->posts_count }})
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
