<?php

namespace SoufieneSlimi\LaravelFormTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class Template extends Model
{
    protected $guarded = ['id'];
    protected $table = 'form_templates';

    /**
     * Get the usages for the template.
     */
    public function usages()
    {
        return $this->hasMany(TemplateUsage::class);
    }

    /**
     * Apply a template
     *
     * @param bool $trackUsage Whether to track the usage of this template
     * @return \SoufieneSlimi\LaravelFormTemplate\Models\Template
     */
    public function apply($trackUsage = true)
    {
        if (! Session::has('_old_input')) {
            Session::now('_old_input', json_decode($this->form, true));
        }

        if ($trackUsage) {
            $this->trackUsage();
        }

        return $this;
    }

    /**
     * Track the usage of this template
     *
     * @return \SoufieneSlimi\LaravelFormTemplate\Models\TemplateUsage
     */
    protected function trackUsage()
    {
        // Check if analytics is enabled
        if (!config('laravel-form-template.analytics.enabled', true)) {
            return null;
        }

        $data = [
            'user_id' => Auth::id(),
        ];

        // Add optional tracking data based on config
        if (config('laravel-form-template.analytics.track_ip_address', true)) {
            $data['ip_address'] = Request::ip();
        }

        if (config('laravel-form-template.analytics.track_user_agent', true)) {
            $data['user_agent'] = Request::userAgent();
        }

        if (config('laravel-form-template.analytics.track_page_url', true)) {
            $data['page_url'] = Request::fullUrl();
        }

        return $this->usages()->create($data);
    }

    /**
     * Create a new template using an array
     *
     * @param string $name the template name
     * @param array  $data the template data
     *
     * @return void
     */
    public static function make(string $name, array $data, $modelFqn = null)
    {
        return self::create([
            'name' => $name,
            'model_fqn' => $modelFqn,
            'form' => json_encode($data),
        ]);
    }

    /**
     * Create a new template using a model instance
     *
     * @param string                              $name                the template name
     * @param \Illuminate\Database\Eloquent\Model $model               the model to extract data from
     * @param array                               $extra               [optional] any extra data to save with this
     *                                                                 template
     * @param bool                                $withDefaultExcluded [optional] whether to remove some default data
     *                                                                 from the model or not
     *
     * @return \SoufieneSlimi\LaravelFormTemplate\Models\Template
     */
    public static function makeForModel(string $name, Model $model, array $extra = [], bool $withDefaultExcluded = true)
    {
        if ($withDefaultExcluded) {
            $excluded = config('laravel-form-template.excluded');
        }

        return self::make($name, array_merge($extra, $model->makeHidden(array_merge(
            $excluded ?? [],
            ['_token']
        ))->toArray()), get_class($model));
    }
}
