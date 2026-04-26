<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuId    = $this->input('menu_id');
        $contentId = $this->route('content')?->id;

        return [
            'menu_id'      => ['required', 'integer', 'exists:menus,id'],
            'title'        => ['required', 'string', 'max:300'],
            'slug'         => [
                'nullable',
                'string',
                'max:300',
                Rule::unique('contents')
                    ->where(fn ($q) => $q->where('menu_id', $menuId))
                    ->ignore($contentId),
            ],
            'type'         => ['required', Rule::in(['content', 'file', 'external'])],
            'body'         => ['nullable', 'string', Rule::requiredIf($this->input('type') === 'content')],
            'file'         => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
                'max:' . config('content.max_file_size_kb', 10240),
            ],
            'external_url' => [
                'nullable',
                'url',
                Rule::requiredIf($this->input('type') === 'external'),
            ],
            'status'       => ['required', Rule::in(['draft', 'published', 'archived'])],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'meta_title'   => ['nullable', 'string', 'max:300'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords'    => ['nullable', 'string', 'max:500'],
        ];
    }
}
