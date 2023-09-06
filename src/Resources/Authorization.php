<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\Canonical;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Authorization extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Authorization::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static function softDeletes()
    {
        return request()->user()->isSuperAdmin();
    }

    public function fields(Request $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Canonical::make()
                ->sortable(),

            Text::make('Description')
                ->charLimit(100)
                ->nullable(),

            new Panel('Timestamps', $this->timestamps($request)),

            MorphToMany::make('Related Users', 'clients', Client::class)
                ->fields(fn () => [
                    FKLink::make('User', 'user_id', User::class)
                          ->sortable(),
                ])
                ->nullable()
                ->collapsedByDefault(),

            MorphToMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                ->fields(fn () => [
                    FKLink::make('User', 'user_id', User::class)
                          ->sortable(),
                ])
                ->nullable()
                ->collapsedByDefault(),
        ];
    }
}
