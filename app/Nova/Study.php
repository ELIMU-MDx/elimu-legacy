<?php

namespace App\Nova;

use App\Fields\StorageSizeField;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Study extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Study';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
        'study_id',
        'id',
    ];

    public function title()
    {
        return $this->study_id . ": " . str_limit($this->name, 20);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->hideFromDetail()->hideFromIndex(),
            Text::make('Study ID')->sortable()->creationRules(
                'required',
                'unique:studies,study_id'
            )->updateRules('required', 'unique:studies,study_id,{{resourceId}}'),
            Text::make('Name')->sortable()->creationRules('required', 'unique:studies,name')->updateRules(
                'required',
                'unique:studies,name,{{resourceId}}'
            ),
            HasMany::make('SampleInformations'),
            BelongsToMany::make('Sample Types', 'sampleTypes', SampleType::class)->fields(new StorageSizeField),
            BelongsToMany::make('Users'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
