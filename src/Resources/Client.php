<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Client extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Client::class;

    public static $search = [
        'id', 'name'
    ];

    public function title()
    {
        return $this->name;
    }

    public function subtitle()
    {
        $total = $this->questionnaires()->count();
        return $total . ' ' . Str::plural('questionnaire', $total);
    }

    public static function availableForNavigation(Request $request)
    {
        $user = $request->user();

        return
            // The user is a super admin.
            $user->is_admin ||

            // The user has an "admin" authorization on this client.
            $user->isAuthorizedAs($user->client, 'admin');
    }

    public static function softDeletes()
    {
        return false;
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()
              ->sortable()
              ->canSee(function ($request) {
                    return $request->user()->is_admin;
              }),

            Text::make('Name')
                ->rules('required', 'max:255'),

            Text::make('Address')
                ->rules('required', 'max:255'),

            Text::make('Postal Code')
                ->rules('required', 'max:255'),

            Text::make('Locality')
                ->rules('required', 'max:255'),

            Text::make('VAT number')
                ->rules('required', 'max:255'),

            Select::make('Default locale')->options([
                'en' => 'English',
                'cn' => 'Chinese',
                'pt' => 'Portuguese',
                'de' => 'German',
                'it' => 'Italian',
                'fr' => 'French'
            ])->displayUsingLabels(),

            DateTime::make('Created At')
                    ->hideFromIndex()
                    ->readonly()
                    ->displayUsing(function ($value) {
                        return $value->diffForHumans();
                    }),

            DateTime::make('Updated At')
                    ->hideFromIndex()
                    ->readonly()
                    ->displayUsing(function ($value) {
                        return $value->diffForHumans();
                    }),

            DateTime::make('Deleted At')
                ->hideFromIndex()
                ->readonly()
                ->canSee(function ($request) {
                    $model = $request->findModel();
                    return !$model->deleted_at == null;
                })
                ->displayUsing(function ($value) {
                    return $value?->diffForHumans();
                })
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }
}
