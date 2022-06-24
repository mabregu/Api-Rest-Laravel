<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
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
    public function can_filter_articles_by_category()
    {
        Article::factory()->count(2)->create();
        $category1 = Category::factory()->hasArticles(3)->create(['slug' => 'category-1']);
        $category2 = Category::factory()->hasArticles()->create(['slug' => 'category-2']);

        // articles?filter[categories]=category-1
        $url = route('api.v1.articles.index', [
            'filter' => [
                'categories' => 'category-1,category-2'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(4, 'data')
            ->assertSee($category1->articles[0]->title)
            ->assertSee($category1->articles[1]->title)
            ->assertSee($category1->articles[2]->title)
            ->assertSee($category2->articles[0]->title)
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

        $this->getJson($url)->assertJsonApiError(
            title: "Bad Request",
            detail: "The filter 'unknown' is not allowed in the 'articles' resource type.",
            status: "400"
        );
    }
}
