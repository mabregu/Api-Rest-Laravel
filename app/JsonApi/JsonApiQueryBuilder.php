<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonApiQueryBuilder
{
    public function allowedSorts(): Closure
    {
        return function ($allowedSortFields) {
            /** @var Builder $this */
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));

                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

                    $sortField = ltrim($sortField, '-');

                    if (! in_array($sortField, $allowedSortFields)) {
                        throw new BadRequestHttpException("The sort field '{$sortField}' is not allowed in the '{$this->getResourceType()}' resource type.");
                    }

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
    }
    
    public function allowedFilters(): Closure
    {
        return function ($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {

                if (!in_array($filter, $allowedFilters)) {
                    throw new BadRequestHttpException("The filter '{$filter}' is not allowed in the '{$this->getResourceType()}' resource type.");
                }

                $this->hasNamedScope($filter)
                    ? $this->{$filter}($value)
                    : $this->where($filter, 'like', "%$value%")
                ;
            }

            return $this;
        };
    }

    public function allowedIncludes(): Closure
    {
        return function ($allowedIncludes) {
            /** @var Builder $this */

            if (request()->isNotFilled('include')) {
                return $this;
            }

            $includes = explode(',', request()->input('include'));

            foreach ($includes as $include) {

                if (!in_array($include, $allowedIncludes)) {
                    throw new BadRequestHttpException("The included relationships '{$include}' is not allowed in the '{$this->getResourceType()}' resource type.");
                }

                $this->with($include);
            }

            return $this;
        };
    }

    public function sparseFieldset(): Closure
    {
        return function () {
            /** @var Builder $this */
            if (request()->isNotFilled('fields')) {
                return $this;
            }            

            $fields = explode(',', request()->input('fields.' . $this->getResourceType()));

            $routeKeyName = $this->model->getRouteKeyName();

            if (! in_array($routeKeyName, $fields)) {
                $fields[] = $routeKeyName;
            }

            return $this->addSelect($fields);
        };
    }

    public function jsonPaginate(): Closure
    {
        return function () {
            /** @var Builder $this */
            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1)
            )->appends(request()->only('sort','filter','page.size'));
        };
    }

    public function getResourceType(): Closure
    {
        return function () {
            /** @var Builder $this */
            if (property_exists($this->model, 'resourceType')) {
                return $this->model->resourceType;
            }

            return $this->model->getTable();
        };
    }
}