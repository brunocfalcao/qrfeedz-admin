<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Country extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Country::class;

    public static $title = 'name';

    public static $globallySearchable = false;

    public function subtitle()
    {
        // User is super admin.
        if (request()->user()->isSuperAdmin()) {
            $total = $this->clients->count();

            return $total.' '.Str::plural('client', $total).'aa';
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
            QRHasMany::make('Clients', 'clients', Client::class)
                   ->collapsedByDefault(),

            // Relationship ID: 25
            QRHasMany::make('Locations', 'locations', Location::class)
                   ->collapsedByDefault(),

            // Relationship ID: 3
            QRHasMany::make('Users', 'users', User::class)
                   ->collapsedByDefault(),
        ];
    }
}
