<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\UUID;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Fields\QRMorphToMany;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class QuestionInstance extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\QuestionInstance::class;

    public static $globallySearchable = false;

    public function title()
    {
        return 'Question from '.
               $this->questionnaire->name.
               ' questionnaire';
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            UUID::make(),

            QRBelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class),

            Boolean::make('Is analytical?', 'is_analytical')
                   ->helpInfo('If the question instance value will be used for reports.<br/>If it is not then it can be to display a message, or to capture custom information'),

            Boolean::make('Contains personal data?', 'is_used_for_personal_data')
                   ->helpInfo('If the value is data-sensitive to GDPR scopes'),

            Boolean::make('Is required?', 'is_required'),

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 20
            QRHasMany::make('Widget instances', 'widgetInstances', WidgetInstance::class),

            // Relationship ID: 19
            QRHasMany::make('Responses', 'responses', Response::class),

            // Relationship ID: 15
            QRMorphToMany::make('Captions', 'captions', Locale::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption'),
                    Text::make('Placeholder', 'placeholder'),
                ])
                ->nullable(),
        ];
    }
}
