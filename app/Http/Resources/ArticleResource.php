<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type' => 'articles',
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => array_filter([
                'title' => $this->resource->title,
                'slug' => $this->resource->slug,
                'content' => $this->resource->content,
            ], function($value) {
                if (request()->isNotFilled('fields')) {
                    return true;
                }

                $fields = explode(',', request()->input('fields.articles'));

                if ($value === $this->getRouteKey()) {
                    return in_array('slug', $fields);
                }

                return $value;
            }),
            'links' => [
                'self' => route('api.v1.articles.show', $this->resource),
            ]
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->withHeaders([
            'Location' => route('api.v1.articles.show', $this->resource),
        ]);
    }
}
