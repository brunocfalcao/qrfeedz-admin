<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Fields\QRMorphedByMany;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Tag extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\Tag::class;

    public static $title = 'name';

    public static $globallySearchable = false;

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->rules('required'),

            Text::make('Description'),

            new Panel('Last data activity', $this->timestamps($request)),

            // Relationship ID: 13
            QRMorphedByMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                ->nullable()
                ->collapsedByDefault(),
        ];
    }
}
