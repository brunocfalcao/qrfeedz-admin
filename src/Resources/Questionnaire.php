<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Fields\UUID;

class Questionnaire extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Questionnaire::class;

    public static $title = 'name';

    public static $search = [
        'name', 'title',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            UUID::make(),

            Text::make('Name')
                ->rules('required'),

            Text::make('Title')
                ->rules('required'),

            BelongsTo::make('Client', 'client', Client::class)
                     ->withoutTrashed(),

            BelongsTo::make('Location', 'location', Location::class)
                     ->withoutTrashed(),

            Textarea::make('Description'),

            Image::make('Logo', 'file_logo'),

            Boolean::make('Active?', 'is_active'),

            Color::make('Primary color', 'color_primary'),

            Color::make('Secondary color', 'color_secondary'),

            KeyValue::make('Data', 'data'),

            DateTime::make('Started at', 'starts_at')
                    ->hideFromIndex(),

            DateTime::make('Ending at', 'ends_at')
                     ->hideFromIndex(),

            BelongsTo::make('Default locale', 'locale', Locale::class)
                     ->withoutTrashed(),

            BelongsTo::make('Category', 'category', Category::class)
                     ->withoutTrashed(),

        ];
    }
}
