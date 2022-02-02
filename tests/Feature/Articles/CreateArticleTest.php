<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
            '_relationships' => [
                'category' => $category,
                'author' => $user,
            ],
        ])->assertCreated();

        $article = Article::first();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertJsonApiResource($article, [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Nuevo artículo',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'test-article',
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Tes',
            'slug' => 'test-article',
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Test Article',
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Test Article',
            'slug' => $article->slug,
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Test Article With Invalid Characters $%^&*()',
            'slug' => 'test-article-with-invalid-characters-$%^&*()',
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'With Underscores',
            'slug' => 'with_underscores',
            'content' => 'With Underscores Content',
        ])
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'data.attributes.slug']))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => '- Start With Dashes',
            'slug' => '-start-with-dashes',
            'content' => 'With Underscores Content',
        ])
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug')
        ;
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'End With Dashes -',
            'slug' => 'end-with-dashes-',
            'content' => 'With Underscores Content',
        ])
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug')
        ;
    }

    /** @test */
    public function content_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Test Article',
            'slug' => 'test-article',
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function category_relationship_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'Test Article Content',
        ])->assertJsonApiValidationErrors('relationships.category');
    }

    /** @test */
    public function category_must_exist_in_database()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'Test Article Content',
            '_relationships' => [
                'category' => Category::factory()->make()
            ],
        ])->assertJsonApiValidationErrors('relationships.category');
    }
}
