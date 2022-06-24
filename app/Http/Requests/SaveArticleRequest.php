<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //dd(request()->all());
        return [
            'data.attributes.title' => ['required', 'min:4'],
            'data.attributes.slug' => [
                'required',
                'alpha_dash',
                new Slug(),
                Rule::unique('articles', 'slug')->ignore($this->route('article')),
            ],
            'data.attributes.content' => ['required'],
            'data.relationships.category.data.id' => [
                Rule::requiredIf(!$this->route('article')),
                Rule::exists('categories', 'slug'),
            ],
            'data.relationships.author.data.id' => [
                // Rule::requiredIf(!$this->route('article')),
                // Rule::exists('users', 'id'),
            ],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated()['data'];
        $attributes = $data['attributes'];

        if(isset($data['relationships'])) {
            $relationships = $data['relationships'];

            foreach ($relationships as $key => $relationship) {
                $attributes = array_merge($attributes, $this->{$key}($relationship));
            }
        }

        return $attributes;
    }

    public function author($relationship): array
    {
        $userUuid = $relationship['data']['id'];

        return ['user_id' => $userUuid];
    }

    public function category($relationship): array
    {
        $categorySlug = $relationship['data']['id'];
        $category = Category::where('slug', $categorySlug)->first();

        return ['category_id' => $category->id];
    }
}
