@extends('layouts.blog')

@section('title', $post->meta['title'] ?? $post->title)
@section('description', $post->meta['description'] ?? $post->excerpt)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('blog.index') }}" class="hover:text-gray-700">Home</a>
            <span>→</span>
            <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-gray-700">
                {{ $post->category->name }}
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $post->title }}</span>
        </div>
    </nav>

    <article class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Featured Image -->
        @if($post->featured_image)
        <div class="w-full h-64 md:h-96">
            <img src="{{ Storage::url($post->featured_image) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-full object-cover">
        </div>
        @endif

        <div class="p-8">
            <!-- Article Header -->
            <header class="mb-8">
                <div class="flex items-center mb-4">
                    <span class="badge" style="background-color: {{ $post->category->color }}">
                        {{ $post->category->name }}
                    </span>
                    @if($post->is_featured)
                    <span class="text-yellow-500 ml-2 text-lg">⭐</span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                    {{ $post->title }}
                </h1>

                @if($post->excerpt)
                <p class="text-xl text-gray-600 mb-6 leading-relaxed">
                    {{ $post->excerpt }}
                </p>
                @endif

                <!-- Article Meta -->
                <div class="flex flex-wrap items-center gap-6 text-gray-500 border-b border-gray-200 pb-6">
                    <div class="flex items-center">
                        <span class="text-lg mr-2">👤</span>
                        <span class="font-medium">{{ $post->user->name }}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <span class="text-lg mr-2">📅</span>
                        <span>{{ $post->published_at->format('F j, Y') }}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <span class="text-lg mr-2">⏱️</span>
                        <span>{{ $post->reading_time }} min read</span>
                    </div>
                    
                    <div class="flex items-center">
                        <span class="text-lg mr-2">👁️</span>
                        <span>{{ number_format($post->views_count) }} views</span>
                    </div>
                </div>

                <!-- Tags -->
                @if($post->tags->count() > 0)
                <div class="flex flex-wrap gap-2 mt-6">
                    @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" 
                       class="badge hover:opacity-80 transition-opacity" 
                       style="background-color: {{ $tag->color }}">
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @endif
            </header>

            <!-- Article Content -->
            <div class="prose prose-lg max-w-none">
                {!! $post->content !!}
            </div>
        </div>
    </article>

    <!-- Comments -->
    <section class="mt-16">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Comments</h2>
            <span class="inline-flex items-center text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                {{ $post->comments->where('status', 'approved')->count() }}
            </span>
        </div>

        @if(session('status'))
            <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        @php $approved = $post->comments->where('status', 'approved'); @endphp
        @if($approved->count() > 0)
            <div class="space-y-6">
                @foreach($approved as $comment)
                    @php
                        $displayName = $comment->name ?? ($comment->user->name ?? 'Guest');
                        $initial = mb_strtoupper(mb_substr($displayName, 0, 1));
                    @endphp
                    <article class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-semibold">
                                {{ $initial }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="font-semibold text-gray-900">{{ $displayName }}</span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-2 text-gray-700 leading-relaxed whitespace-pre-line">{{ $comment->content }}</div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <article class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center">💬</div>
                <p class="text-gray-700 font-medium">No comments yet</p>
                <p class="text-gray-500 text-sm">Be the first to share your thoughts.</p>
            </article>
        @endif

        <!-- Comment form card -->
        <article class="bg-white rounded-xl shadow-lg p-6 mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Leave a comment</h3>
            @guest
                <div class="text-gray-700">
                    Lütfen yorum bırakmak için
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">giriş yap</a>
                    @endif
                    @if (Route::has('register'))
                        veya <a href="{{ route('register') }}" class="text-blue-600 hover:underline">kayıt ol</a>.
                    @endif
                </div>
            @else
                <form action="{{ route('blog.comments.store', $post->slug) }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Comment</label>
                        <textarea name="content" rows="4" placeholder="Write your comment..." class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('content') }}</textarea>
                        @error('content')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Your comment will appear after approval.</p>
                        <button type="submit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium">
                            Submit Comment
                        </button>
                    </div>
                </form>
            @endguest
        </article>
    </section>

    <!-- Related Posts -->
    @if($relatedPosts->count() > 0)
    <section class="mt-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedPosts as $relatedPost)
            <article class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                @if($relatedPost->featured_image)
                <img src="{{ Storage::url($relatedPost->featured_image) }}" 
                     alt="{{ $relatedPost->title }}" 
                     class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                    <span class="text-white text-4xl">📝</span>
                </div>
                @endif
                
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="badge" style="background-color: {{ $relatedPost->category->color }}">
                            {{ $relatedPost->category->name }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-800 mb-3 hover:text-blue-600">
                        <a href="{{ route('blog.show', $relatedPost->slug) }}">
                            {{ $relatedPost->title }}
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        {{ $relatedPost->excerpt }}
                    </p>
                    
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>{{ $relatedPost->user->name }}</span>
                        <span>{{ $relatedPost->published_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Back to Blog -->
    <div class="text-center mt-16">
        <a href="{{ route('blog.index') }}" 
           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            ← Back to Blog
        </a>
    </div>
</div>

<style>
    .prose {
        color: #374151;
        line-height: 1.75;
    }
    .prose h2 {
        color: #1f2937;
        font-weight: 700;
        font-size: 1.875rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
        line-height: 1.3;
    }
    .prose h3 {
        color: #1f2937;
        font-weight: 600;
        font-size: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }
    .prose p {
        margin-bottom: 1.25rem;
    }
    .prose ul, .prose ol {
        margin: 1.25rem 0;
        padding-left: 1.5rem;
    }
    .prose li {
        margin-bottom: 0.5rem;
    }
    .prose blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6b7280;
    }
    .prose code {
        background-color: #f3f4f6;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
        color: #db2777;
    }
    .prose pre {
        background-color: #1f2937;
        color: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
    }
    .prose pre code {
        background-color: transparent;
        color: inherit;
        padding: 0;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
