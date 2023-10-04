<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\QRCanonical;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Resources\User as UserResource;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Cube\Models\User;
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

            MorphToMany::make('Clients')
                       ->fields(function ($request, $relatedModel) {
                           return [
                               Select::make('User', 'user_id')->options(
                                   User::all()->pluck('name', 'id')
                               )->onlyOnForms(),

                               FKLink::make('User', 'user_id', UserResource::class),
                           ];
                       })
                       ->canSee(function ($request) {
                           return str_starts_with($this->canonical, 'client');
                       }),

            MorphToMany::make('Questionnaires')
                       ->fields(function ($request, $relatedModel) {
                           return [
                               Select::make('User', 'user_id')->options(
                                   User::all()->pluck('name', 'id')
                               )->onlyOnForms(),

                               FKLink::make('User', 'user_id', UserResource::class),
                           ];
                       })
                       ->canSee(function ($request) {
                           return str_starts_with($this->canonical, 'questionnaire');
                       }),
        ];
    }
}
