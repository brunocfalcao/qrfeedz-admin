<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\BelongsToThrough;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Response extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Response::class;

    public static $globallySearchable = false;

    public function title()
    {
        /**
         * If this response has question in english,
         * then show the english locale, else the first index.
         */
        $questionInstance = $this->questionInstance;

        return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
               $questionInstance->captions->first()->pivot->caption;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $user = $request->user();

        // Admin-like? Return all responses.
        /*
        if ($user->isAdminLike()) {
            return $query;
        }
        */

        return $query
            ->quickJoin('question_instances', 'responses')
            ->quickJoin('page_instances', 'question_instances')
            ->quickJoin('questionnaires', 'page_instances')
            ->quickJoin('locations', 'questionnaires')
            ->quickJoin('clients as client_locations', 'locations')
            ->join('users', 'client_locations.id', '=', 'users.client_id')
            ->when($user->isAtLeastAuthorizedAs('client-admin'), function ($query) use ($user) {
                // Obtain the clients where the user is client-admin.
                $query->whereIn('client_locations.id', $user->authorizationsAs('client-admin')
                                                   ->pluck('model_id'));
            })
            ->when($user->isAtLeastAuthorizedAs('location-admin'), function ($query) use ($user) {
                // Obtain the clients where the user is location-admin.
                $query->whereIn('locations.id', $user->authorizationsAs('location-admin')
                                                     ->pluck('model_id'));
            })
            ->where('users.id', $user->id);
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            BelongsToThrough::make('Questionnaire', function () {
                return $this->resource
                            ->questionInstance
                            ->pageInstance
                            ->questionnaire;
            }, Questionnaire::class),

            BelongsToThrough::make('Page instance', function () {
                return $this->questionInstance
                            ->pageInstance;
            }, PageInstance::class),

            Text::make('Widget instance', function () {
                return $this->widgetInstance->widget->name;
            }),

            Text::make('Question caption', function () {
                $questionInstance = $this->questionInstance;

                return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
                       $questionInstance->captions->first()->pivot->caption;
            }),

            BelongsTo::make('Question instance', 'questionInstance', QuestionInstance::class)
                     ->withoutTrashed()
                     ->hideFromIndex(),

            Text::make('Computed value(s)', function () {
                return array_values($this->value);
            }),

            KeyValue::make('Value dataset', 'value'),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
