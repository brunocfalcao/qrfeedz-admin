<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\Canonical;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Authorization extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Authorization::class;

    public static $search = [
        'name',
    ];

    public function title()
    {
        return $this->name;
    }

    public static function relatableQuery(NovaRequest $request, $query)
    {
        /**
         * Separate distinct authorization types by authorization
         * canonical prefix, on this case by the viaResource.
         */
        if (via_resource('client-authorizations')) {
            return
                // Return all client related authorizations.
                $query->where('canonical', 'like', 'client-%');
        }

        if (via_resource('questionnaire-authorizations')) {
            // Return all questionnaire related authorizations.
            return $query->where('canonical', 'like', 'questionnaire-%');
        }

        return $query;
    }

    public function fields(Request $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->rules('required', 'max:255'),

            Canonical::make(),

            Text::make('Description')
                ->charLimit(50)
                ->nullable(),

            new Panel('Last data activity', $this->timestamps($request)),

            // Relationship ID: 4
            QRHasMany::make('Client Authorizations', 'clientAuthorizations', ClientAuthorization::class)
                   ->canSee(function ($request) {
                       return str_starts_with($this->canonical, 'client');
                   }),

            // Relationship ID: 29
            QRHasMany::make('Questionnaire Authorizations', 'questionnaireAuthorizations', QuestionnaireAuthorization::class)
                   ->canSee(function ($request) {
                       return str_starts_with($this->canonical, 'questionnaire');
                   }),
        ];
    }
}
