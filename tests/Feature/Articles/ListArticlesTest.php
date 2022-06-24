<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_article()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show', $article));

        $response->assertJsonApiResource($article, [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content
        ])->assertJsonApiRelationshipLinks($article, ['category', 'author']);
    }

    /** @test */
    public function can_fetch_all_articles()
    {
        $this->withoutExceptionHandling();

        $articles = Article::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.articles.index'));

        $response->assertJsonApiResourceCollection($articles, [
            'title',
            'slug',
            'content'
        ]);
    }

    /** @test */
    public function it_returns_a_json_api_error_object_when_an_article_is_not_found()
    {
        $this->getJson(route('api.v1.articles.show', 'article-that-does-not-exist'))
            ->assertJsonApiError(
                title: 'Not found',
                detail: 'The resource with id "article-that-does-not-exist" and type "articles" could not be found.',
                status: "404"
            )
        ;
    }
}
