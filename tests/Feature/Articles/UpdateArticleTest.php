<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_update_articles()
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article))
            ->assertJsonApiError(
                title: 'Unauthenticated.',
                detail: 'This action requires authentication.',
                status: '401'
            )
        ;

        //$response->assertJsonApiResource
    }

    /** @test */
    public function can_update_owned_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ['article:update']);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated Article Content',
        ])->assertOk();

        $response->assertJsonApiResource($article, [
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated Article Content'
        ]);
    }

    /** @test */
    public function cannot_update_articles_owned_by_other_users()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated Article Content',
        ])->assertForbidden();
    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'updated-article',
            'content' => 'Updated Article Content',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Upd',
            'slug' => 'updated-article',
            'content' => 'Updated Article Content',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'content' => 'Updated Article Content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        Sanctum::actingAs($article1->author);

        $this->patchJson(route('api.v1.articles.update', $article1), [
            'title' => 'Test Article',
            'slug' => $article2->slug,
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Test Article With Invalid Characters $%^&*()',
            'slug' => 'test-article-with-invalid-characters-$%^&*()',
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'With Underscores',
            'slug' => 'with_underscores',
            'content' => 'With Underscores Content',
        ])
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'data.attributes.slug']))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => '- Start With Dashes',
            'slug' => '-start-with-dashes',
            'content' => 'With Underscores Content',
        ])
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'End With Dashes -',
            'slug' => 'end-with-dashes-',
            'content' => 'With Underscores Content',
        ])
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'slug' => 'updated-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
