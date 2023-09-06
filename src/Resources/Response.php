<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class Response extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\QRFeedz\Cube\Models\Response>
     */
    public static $model = \QRFeedz\Cube\Models\Response::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
        ];
    }
}
