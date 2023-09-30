<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\MorphedByMany;
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

    public static $search = [
        'name',
    ];

    public function title()
    {
        return $this->name;
    }

    public function subtitle()
    {
        $total = DB::table('authorizables')
                   ->where('authorization_id', $this->id)
                   ->count();

        return $total.' '.Str::plural('entity', $total);
    }

    public function fields(Request $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Name')
                ->rules('required', 'max:255'),

            Canonical::make(),

            Text::make('Description')
                ->charLimit(50)
                ->nullable(),

            new Panel('Last data activity', $this->timestamps($request)),

            MorphedByMany::make('Related Clients / Users', 'clients', Client::class)
                ->fields(fn () => [
                    FKLink::make('User', 'user_id', User::class)
                          ->sortable(),

                    Text::make('Type', function ($data) {
                        return Authorization::find($data->authorization_id)->name;
                    })->asHtml(),
                ])
                ->nullable()
                ->collapsedByDefault(),

            MorphedByMany::make('Related Locations / Users', 'locations', Location::class)
                ->fields(fn () => [
                    FKLink::make('User', 'user_id', User::class)
                          ->sortable(),

                    Text::make('Type', function ($data) {
                        return Authorization::find($data->authorization_id)->name;
                    })->asHtml(),
                ])
                ->nullable()
                ->collapsedByDefault(),

            MorphedByMany::make('Related Questionnaires / Users', 'questionnaires', Questionnaire::class)
                ->fields(fn () => [
                    FKLink::make('User', 'user_id', User::class)
                          ->sortable(),

                    Text::make('Type', function ($data) {
                        return Authorization::find($data->authorization_id)->name;
                    })->asHtml(),
                ])
                ->nullable()
                ->collapsedByDefault(),
        ];
    }
}
