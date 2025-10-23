<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(): Response
    {
        $articles = Article::query()
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Article $article) => $this->transformForList($article))
            ->values();

        return Inertia::render('Artikel', [
            'articles' => $articles,
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $article = Article::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        $page = Inertia::render('ArticleDetail', [
            'article' => $article ? $this->transformForDetail($article) : null,
        ]);

        if (! $article) {
            return $page->toResponse($request)->setStatusCode(404);
        }

        return $page;
    }

    /**
     * @return array<string, mixed>
     */
    protected function transformForList(Article $article): array
    {
        $publishedAt = $article->published_at;

        return [
            'id' => $article->id,
            'title' => $article->title,
            'excerpt' => $article->excerpt
                ?? str($article->body ?? '')
                    ->stripTags()
                    ->squish()
                    ->limit(140)
                    ->value(),
            'date' => optional($publishedAt)->translatedFormat('d/m/Y'),
            'datetime' => optional($publishedAt)?->toDateString(),
            'image' => $article->cover_image_url,
            'imageAlt' => $article->meta_title ?? $article->title,
            'href' => route('articles.show', $article->slug),
            'slug' => $article->slug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function transformForDetail(Article $article): array
    {
        $publishedAt = $article->published_at;

        return [
            'id' => $article->id,
            'title' => $article->title,
            'body' => $article->body,
            'excerpt' => $article->excerpt,
            'published_at' => optional($publishedAt)?->toIso8601String(),
            'formatted_published_at' => optional($publishedAt)->translatedFormat('d F Y'),
            'cover_image' => $article->cover_image_url,
            'meta_title' => $article->meta_title ?? $article->title,
            'meta_description' => $article->meta_description ?? $article->excerpt,
            'slug' => $article->slug,
        ];
    }
}
