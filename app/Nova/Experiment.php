<?php

namespace App\Nova;

use App\Actions\ChangeValidationStatus;
use App\Fields\SampleStatusField;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Trix;
use Tightenco\NovaReleases\LatestRelease;

class Experiment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Experiment';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->hideFromIndex(),
            BelongsTo::make('Assay'),
            BelongsTo::make('Requester', 'requester', User::class)->rules('required', 'exists:people,id')->searchable(),
            DateTime::make('Requested at')->rules('required', 'date'),
            DateTime::make('Processed at')->rules('date'),
            BelongsTo::make('Receiver', 'receiver', User::class)->rules('required', 'exists:people,id')->searchable(),
            BelongsTo::make('Collector', 'collector', User::class)->rules('required', 'exists:people,id')->searchable(),
            Trix::make('Comment')->hideFromIndex(),
            BelongsToMany::make('Samples')->actions(function () {
                return [
                    new ChangeValidationStatus()
                ];
            })->fields(new SampleStatusField),
            HasMany::make('Results')
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