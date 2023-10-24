<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\Canonical;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Fields\QRMorphedByMany;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Locale extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Locale::class;

    public static $globallySearchable = false;

    public function title()
    {
        return $this->name.' ('.$this->canonical.')';
    }

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
            QRHasMany::make('Default Client locales', 'clients', Client::class)
                   ->collapsedByDefault(),

            // Relationship ID: 14
            QRHasMany::make('Default Questionnaire locales', 'questionnaires', Questionnaire::class)
                   ->collapsedByDefault(),

            // Relationship ID: 27
            QRHasMany::make('Default User locales', 'users', User::class)
                   ->collapsedByDefault(),

            // Relationship ID: 15
            QRMorphedByMany::make('Related Question instance locales', 'questionInstances', QuestionInstance::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption')
                          ->sortable(),

                    Text::make('Placeholder', 'placeholder')
                          ->sortable(),
                ])
                ->nullable(),

            // Relationship ID: 23
            QRMorphedByMany::make('Related Widget instance locales', 'widgetInstances', WidgetInstance::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption')
                          ->sortable(),

                    Text::make('Placeholder', 'placeholder')
                          ->sortable(),
                ])
                ->nullable(),
        ];
    }
}
