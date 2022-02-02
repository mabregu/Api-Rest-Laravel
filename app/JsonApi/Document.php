<?php

namespace App\JsonApi;

use Illuminate\Support\Collection;

class Document extends Collection
{
    public static function type(string $type): Document
    {
        return new self([
            'data' => [
                'type' => $type,
            ],
        ]);
    }

    public function id(string $id): Document
    {
        if($id) $this->items['data']['id'] = (string) $id;

        return $this;
    }

    public function attributes(array $attributes): Document
    {
        unset($attributes['_relationships']);

        $this->items['data']['attributes'] = $attributes;

        return $this;
    }

    public function links(array $links): Document
    {
        $this->items['data']['links'] = $links;

        return $this;
    }

    public function relationshipData(array $relationships): Document
    {
        foreach ($relationships as $name => $relationship) {
            $this->items['data']['relationships'][$name]['data'] = [
                'type' => $relationship->getResourceType(),
                'id' => $relationship->getRouteKey(),
            ];
        }

        return $this;
    }

    public function relationshipLinks(array $relationships): Document
    {
        foreach ($relationships as $name) {
            $this->items['data']['relationships'][$name]['links'] = [
                'self' => route(
                    "api.v1.{$this->items['data']['type']}.relationships.$name",
                    $this->items['data']['id']
                ),
                'related' => route(
                    "api.v1.{$this->items['data']['type']}.$name",
                    $this->items['data']['id']
                ),
            ];
        }

        return $this;
    }
}