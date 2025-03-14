<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query()->with(['category', 'newsSource']);

        // Apply filters
        if ($request->has('keyword')) {
            $query->search($request->keyword);
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('source')) {
            $query->where('news_source_id', $request->source);
        }

        if ($request->has('author')) {
            $query->where('author', 'like', "%{$request->author}%");
        }

        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        // Cache the results for 5 minutes
        $cacheKey = 'articles:' . md5($request->fullUrl());
        $articles = Cache::remember($cacheKey, 300, function () use ($query) {
            return $query->latest('published_at')->paginate(15);
        });

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Cache::remember('article:' . $id, 300, function () use ($id) {
            return Article::with(['category', 'newsSource'])->findOrFail($id);
        });

        return response()->json($article);
    }

    public function personalizedFeed(Request $request)
    {
        $user = $request->user();
        $preferences = $user->preferences()->with(['category', 'newsSource'])->get();

        $query = Article::query()->with(['category', 'newsSource']);

        if ($preferences->isNotEmpty()) {
            $query->where(function ($q) use ($preferences) {
                foreach ($preferences as $preference) {
                    if ($preference->category_id) {
                        $q->orWhere('category_id', $preference->category_id);
                    }
                    if ($preference->news_source_id) {
                        $q->orWhere('news_source_id', $preference->news_source_id);
                    }
                    if ($preference->preferred_author) {
                        $q->orWhere('author', 'like', "%{$preference->preferred_author}%");
                    }
                }
            });
        }

        $cacheKey = 'personalized:' . $user->id . ':' . md5($request->fullUrl());
        $articles = Cache::remember($cacheKey, 300, function () use ($query) {
            return $query->latest('published_at')->paginate(15);
        });

        return response()->json($articles);
    }
}
