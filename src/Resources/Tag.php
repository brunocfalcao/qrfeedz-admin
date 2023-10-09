<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\QRID;

class Tag extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Tag::class;

    public static $title = 'name';

    public static $search = [
        'name', 'description',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 13
            MorphedByMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                ->nullable()
                ->collapsedByDefault(),
        ];
    }
}
