<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRCanonical;
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
        $segments = request()->segments();
        /**
         * Separate distinct authorization types by authorization
         * canonical prefix.
         */
        if (in_array('client-authorizations', $segments)) {
            return $query->where('canonical', 'like', 'client-%');
        }

        if (in_array('questionnaire-authorizations', $segments)) {
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

            QRCanonical::make(),

            Text::make('Description')
                ->charLimit(50)
                ->nullable(),

            new Panel('Last data activity', $this->timestamps($request)),

            HasMany::make('Client Authorizations', 'clientAuthorizations', ClientAuthorization::class),

            HasMany::make('Questionnaire Authorizations', 'questionnaireAuthorizations', QuestionnaireAuthorization::class),
        ];
    }
}
