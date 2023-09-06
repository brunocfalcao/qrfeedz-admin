<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Location extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Location::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()
                ->sortable()
                ->canSee(function ($request) {
                    return $request->user()->isSuperAdmin();
                }),

            Text::make('Name'),
        ];
    }
}
