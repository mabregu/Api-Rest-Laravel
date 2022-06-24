<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_category_identifier()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $article->category->getRouteKey(),
            ],
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_category_resource()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.category', $article);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'type' => 'categories',
                'id' => $article->category->getRouteKey(),
                'attributes' => [
                    'name' => $article->category->name,
                ]
            ]
        ]);
    }

    /** @test */
    public function can_update_the_associated_category()
    {
        $category = Category::factory()->create();

        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $this->withoutJsonApiDocumentFormatting();

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $category->id
        ]);
    }

    /** @test */
    public function category_must_exist_in_database()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $this->withoutJsonApiDocumentFormatting();

        $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => 'invalid-id',
            ]
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $article->category_id
        ]);
    }
}
