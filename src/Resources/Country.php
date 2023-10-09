<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRID;
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

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Code')
                ->hideFromIndex(),

            Text::make('name'),

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 9
            HasMany::make('Clients', 'clients', Client::class)
                   ->collapsedByDefault(),

            // Relationship ID: 25
            HasMany::make('Locations', 'locations', Location::class)
                   ->collapsedByDefault(),

            // Relationship ID: 3
            HasMany::make('Users', 'users', User::class)
                   ->collapsedByDefault(),
        ];
    }
}
