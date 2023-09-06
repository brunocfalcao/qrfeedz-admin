<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class Locale extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Locale::class;

    public static $title = 'name';

    public static $search = [
        'id',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
        ];
    }
}
