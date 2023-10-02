<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\Canonical;
use QRFeedz\Admin\Fields\IDSuperAdmin;
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

            BelongsToMany::make('Related Client Authorizations', 'clients', Client::class)
                        ->fields(function ($request, $relatedModel) {
                            return [
                                Select::make('User', 'user_id')->options(
                                    User::all()->pluck('name', 'id')
                                )->onlyOnForms(),

                                Text::make('User', 'user_id')->resolveUsing(function ($id) {
                                    return User::firstWhere('id', $id)->name;
                                })->onlyOnIndex(),
                            ];
                        }),
        ];
    }
}
