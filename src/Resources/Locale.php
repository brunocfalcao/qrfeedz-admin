<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Brunocfalcao\LaravelNovaHelpers\Fields\Canonical;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Locale extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Locale::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Canonical::make()
                ->sortable(),

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 11
            HasMany::make('Clients', 'clients', Client::class)
                   ->collapsedByDefault(),

            // Relationship ID: 14
            HasMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                   ->collapsedByDefault(),

            // Relationship ID: 27
            HasMany::make('Users', 'users', User::class)
                   ->collapsedByDefault(),

            // Relationship ID: 15
            MorphedByMany::make('Related Question instances', 'questionInstances', QuestionInstance::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption')
                          ->sortable(),

                    Text::make('Placeholder', 'placeholder')
                          ->sortable(),
                ])
                ->nullable()
                ->collapsedByDefault(),

            // Relationship ID: 23
            MorphedByMany::make('Related Widget instances', 'widgetInstances', WidgetInstance::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption')
                          ->sortable(),

                    Text::make('Placeholder', 'placeholder')
                          ->sortable(),
                ])
                ->nullable()
                ->collapsedByDefault(),
        ];
    }
}
