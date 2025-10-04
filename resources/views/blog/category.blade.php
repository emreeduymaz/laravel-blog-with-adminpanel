@extends('layouts.blog')

@section('title', $category->name . ' - Category')
@section('description', $category->description ?? 'Articles in ' . $category->name . ' category')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Category Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-6" 
             style="background-color: {{ $category->color }}20">
            <div class="w-10 h-10 rounded-full" style="background-color: {{ $category->color }}"></div>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
            {{ $category->name }}
        </h1>
        
        @if($category->description)
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            {{ $category->description }}
        </p>
        @endif
        
        <div class="mt-6">
            <span class="inline-flex items-center text-gray-500">
                <span class="mr-2">📊</span>
                {{ $posts->total() }} {{ Str::plural('article', $posts->total()) }}
            </span>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('blog.index') }}" class="hover:text-gray-700">Home</a>
            <span>→</span>
            <span class="text-gray-800">{{ $category->name }}</span>
        </div>
    </nav>

    @if($posts->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($posts as $post)
        <article class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
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
                    <span class="badge" style="background-color: {{ $post->category->color }}">
                        {{ $post->category->name }}
                    </span>
                    @if($post->is_featured)
                    <span class="text-yellow-500 ml-2">⭐</span>
                    @endif
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-blue-600">
                    <a href="{{ route('blog.show', $post->slug) }}">
                        {{ $post->title }}
                    </a>
                </h3>
                
                <p class="text-gray-600 mb-4 line-clamp-3">
                    {{ $post->excerpt }}
                </p>
                
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>👤 {{ $post->user->name }}</span>
                    <span>📅 {{ $post->published_at->format('M j, Y') }}</span>
                </div>
                
                <!-- Tags -->
                @if($post->tags->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags->take(3) as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" 
                       class="badge text-xs hover:opacity-80 transition-opacity" 
                       style="background-color: {{ $tag->color }}">
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </article>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($posts->hasPages())
    <div class="mt-12">
        {{ $posts->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-16">
        <div class="text-6xl mb-4">📂</div>
        <h3 class="text-2xl font-semibold text-gray-800 mb-2">No articles in this category yet</h3>
        <p class="text-gray-600 mb-6">Check back later for new content!</p>
        <a href="{{ route('blog.index') }}" 
           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            ← Back to All Articles
        </a>
    </div>
    @endif
</div>

<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
