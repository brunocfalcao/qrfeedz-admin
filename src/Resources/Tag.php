<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Tag extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\Tag::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static $searchRelations = [
        'questionnaire' => ['name'],
    ];

    public static function defaultOrderings($query)
    {
        return $query->orderBy('name', 'asc');
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 13
            QRBelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class),

            Text::make('Name')
                ->rules('required'),

            Text::make('Description'),

            new Panel('Last data activity', $this->timestamps($request)),
        ];
    }
}
