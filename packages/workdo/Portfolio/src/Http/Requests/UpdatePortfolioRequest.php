<?php

namespace Workdo\Portfolio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Information fields
            'photo'                     => 'nullable|string',
            'name'                      => 'required|max:100',
            'role'                      => 'required|max:100',
            'experience_years'          => 'nullable|numeric|min:0|max:50',
            'email'                     => 'required|email|max:255',
            'education'                 => 'nullable|max:500',

            // Work Details fields
            'title'                     => 'required|max:100',
            'description'               => 'required|max:200',
            'category_id'               => 'required|exists:portfolio_categories,id',
            'live_url'                  => 'nullable|url',
            'repository_url'            => 'nullable|url',
            'skills'                    => 'nullable',
            'client'                    => 'required|max:100',
            'duration'                  => 'nullable|max:50',
            'team_size'                 => 'nullable|integer|min:1',
            'start_date'                => 'nullable|date',
            'end_date'                  => 'nullable|date|after:start_date',
            'budget'                    => 'nullable|max:100',
            'industry'                  => 'nullable|max:100',

            // overview fields
            'show_overview'             => 'boolean',
            'overview'                  => 'nullable',

            // Gallery fields
            'images'                    => 'nullable|array',
            'video_link'                => 'nullable|url',
            'show_gallery'              => 'boolean',

            // Contact Section fields
            'contact_heading'           => 'nullable|max:200',
            'contact_message'           => 'nullable|max:500',
            'show_contact'              => 'boolean',

            // Custom Sections
            'custom_sections'           => 'nullable|array',
            'custom_sections.*.title'   => 'nullable|string|max:200',
            'custom_sections.*.content' => 'nullable|string'
        ];
    }
}
