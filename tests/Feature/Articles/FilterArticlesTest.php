<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title()
    {
        Article::factory()->create(['title' => 'First Article']);

        Article::factory()->create(['title' => 'Second Article']);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'title' => 'First Article'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('First Article')
            ->assertDontSee('Second Article')
        ;
    }

    /** @test */
    public function can_filter_articles_by_content()
    {
        Article::factory()->create(['content' => 'First Article']);

        Article::factory()->create(['content' => 'Second Article']);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'content' => 'First Article'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('First Article')
            ->assertDontSee('Second Article');
    }

    /** @test */
    public function can_filter_articles_by_year()
    {
        Article::factory()->create([
            'title' => 'First Article 2022',
            'created_at' => now()->year(2022)
        ]);

        Article::factory()->create([
            'title' => 'Second Article 2023',
            'created_at' => now()->year(2023)
        ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'year' => '2022'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('First Article 2022')
            ->assertDontSee('Second Article 2023')
        ;
    }

    /** @test */
    public function can_filter_articles_by_month()
    {
        Article::factory()->create([
            'title' => 'First Article month 3',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' => 'Second Article month 3',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' => 'Second Article month 1',
            'created_at' => now()->month(1)
        ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'month' => '3'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('First Article month 3')
            ->assertSee('Second Article month 3')
            ->assertDontSee('Second Article month 1')
        ;
    }
    /** @test */
    public function cannot_filter_articles_by_unknown_filters()
    {
        Article::factory()->count(2)->create();

        $url = route('api.v1.articles.index', [
            'filter' => [
                'unknown' => 'filter'
            ]
        ]);

        $this->getJson($url)->assertStatus(400);
    }
}
