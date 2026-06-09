<?php

namespace Workdo\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Workdo\Portfolio\Models\PortfolioCategory;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',

        // Personal Information fields
        'name',
        'email',
        'role',
        'experience_years',
        'photo',
        'education',

        // Work Details fields
        'title',
        'description',
        'category_id',
        'client',
        'live_url',
        'repository_url',
        'skills',
        'duration',
        'team_size',
        'start_date',
        'end_date',
        'budget',
        'industry',

        // Overview fields
        'overview',
        'show_overview',

        // Gallery fields
        'show_gallery',
        'images',
        'video_link',

        // Contact Section fields
        'show_contact',
        'contact_heading',
        'contact_message',

        // System fields
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'date',
            'end_date'      => 'date',
            'skills'        => 'array',
            'show_overview' => 'boolean',
            'show_gallery'  => 'boolean',
            'images'        => 'array',
            'show_contact'  => 'boolean'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug($model->title);
            }
        });
    }

    public function generateUniqueSlug($title)
    {
        $slug         = Str::slug($title);
        $originalSlug = $slug;
        $counter      = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function portfolio_category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }

    public function custom_sections()
    {
        return $this->hasMany(PortfolioCustomSection::class)->orderBy('sort_order');
    }
}
