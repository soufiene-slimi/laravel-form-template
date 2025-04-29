<?php

namespace SoufieneSlimi\LaravelFormTemplate\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateUsage extends Model
{
    protected $guarded = ['id'];
    protected $table = 'template_usages';

    /**
     * Get the template that was used.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query;
    }

    /**
     * Scope a query to filter by template.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $templateId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTemplate($query, $templateId)
    {
        if ($templateId) {
            return $query->where('template_id', $templateId);
        }
        
        return $query;
    }

    /**
     * Scope a query to filter by user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        
        return $query;
    }
}

