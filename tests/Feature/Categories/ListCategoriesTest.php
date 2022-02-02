<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson(route('api.v1.categories.show', $category));

        $response->assertJsonApiResource($category, [
            'name' => $category->name,
        ]);
    }

    /** @test */
    public function can_fetch_all_categories()
    {
        $this->withoutExceptionHandling();

        $categories = Category::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.categories.index'));

        $response->assertJsonApiResourceCollection($categories, [
            'name'
        ]);
    }
}
