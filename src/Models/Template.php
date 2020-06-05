<?php

namespace SoufieneSlimi\LaravelFormTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Template extends Model
{
    protected $guarded = ['id'];
    protected $table = 'form_templates';

    /**
     * Apply a template
     *
     * @return \SoufieneSlimi\LaravelFormTemplate\Models\Template
     */
    public function apply()
    {
        if (! Session::has('_old_input')) {
            Session::now('_old_input', json_decode($this->form, true));
        }
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
