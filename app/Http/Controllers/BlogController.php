<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::published()
            ->with(['category', 'tags', 'user'])
            ->when($request->get('category'), function ($query, $category) {
                $query->byCategory($category);
            })
            ->when($request->get('tag'), function ($query, $tag) {
                $query->byTag($tag);
            })
            ->when($request->get('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate(12);

        $featuredPosts = Post::published()
            ->featured()
            ->with(['category', 'tags', 'user'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('posts')
            ->orderBy('name')
            ->get();

        $tags = Tag::has('posts')
            ->withCount('posts')
            ->orderBy('name')
            ->get();

        return view('blog.index', compact('posts', 'featuredPosts', 'categories', 'tags'));
    }

    public function show(Post $post): View
    {
        if (!$post->is_published) {
            abort(404);
        }

        $post->incrementViews();
        $post->load(['category', 'tags', 'user', 'comments.user']);

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function category(Category $category): View
    {
        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['category', 'tags', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.category', compact('category', 'posts'));
    }

    public function tag(Tag $tag): View
    {
        $posts = $tag->posts()
            ->published()
            ->with(['category', 'tags', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.tag', compact('tag', 'posts'));
    }
}
