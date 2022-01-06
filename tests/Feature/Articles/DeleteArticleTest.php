<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_delete_articles()
    {
        $this->deleteJson(route('api.v1.articles.destroy', Article::factory()->create()))
            ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }
}
