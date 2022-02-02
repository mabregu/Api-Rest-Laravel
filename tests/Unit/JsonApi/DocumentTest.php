<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\Document;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_json_api_documents()
    {
        $category = Mockery::mock('Category', function($mock) {
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('category-id');
        });

        $document = Document::type('articles')
            ->id('article-id')
            ->attributes(['title' => 'Article Title',])
            ->relationshipData(['category' => $category])
            ->toArray()
        ;

        $expected = [
            'data' => [
                'type' => 'articles',
                'id' => 'article-id',
                'attributes' => [
                    'title' => 'Article Title',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => 'category-id',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $document);
    }
}
