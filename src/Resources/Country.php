<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Country extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Country::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public function subtitle()
    {
        // User is super admin.
        if (request()->user()->isSuperAdmin()) {
            $total = $this->clients->count();

            return $total.' '.Str::plural('client', $total);
        }
    }

    public static function availableForNavigation(Request $request)
    {
        return
            // The user is a super admin.
            $request->user()->isSuperAdmin();
    }

    public static function softDeletes()
    {
        return request()->user()->isSuperAdmin();
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Code')
                ->hideFromIndex(),

            Text::make('name'),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Clients', 'clients', Client::class)
                   ->collapsedByDefault(),

            HasMany::make('Users', 'users', User::class)
                   ->collapsedByDefault(),
        ];
    }
}
