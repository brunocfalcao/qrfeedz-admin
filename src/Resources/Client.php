<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\HasManyThrough;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Fields\QRImage;
use QRFeedz\Admin\Resources\User as UserResource;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Admin\Traits\HasAddressFields;
use QRFeedz\Cube\Models\User;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Client extends QRFeedzResource
{
    use DefaultDescPKSorting, HasAddressFields;

    public static $model = \QRFeedz\Cube\Models\Client::class;

    public static $search = [
        'name', 'vat_number', 'address', 'city', 'postal_code',
    ];

    public static $searchRelations = [
        'locale' => ['name'],
        'country' => ['name'],
        'affiliate' => ['name'],
    ];

    public function title()
    {
        return $this->name.' ('.$this->city.', '.$this->country->name.')';
    }

    public function subtitle()
    {
        $total = $this->questionnaires()->count();

        return $total.' '.Str::plural('questionnaire', $total);
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            QRImage::make('Logo', 'logo_file')
                 ->disableDownload()
                 ->acceptedTypes('image/*'),

            Text::make('Name')
                ->rules('required', 'max:255'),

            new Panel('Address Information', $this->addressFields()),

            Text::make('VAT number')
                ->rules('max:255'),

            // Relationship ID: 1
            QRBelongsTo::make('Affiliate', 'affiliate', UserResource::class)
                     ->nullable()
                     ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                         return $query->asAffiliate();
                     })
                     ->canSee(function ($request) {
                         return
                             // User is super admin. So we can add an affiliate.
                             $request->user()->isSuperAdmin() ||

                             // There is an affiliate for this client.
                             optional($this->affiliate)->exists();
                     })
                    ->readonly(function ($request) {
                        return ! $request->user()->isSuperAdmin();
                    }),

            // Relationship ID: 11
            QRBelongsTo::make('Locale', 'locale', Locale::class)
                     ->withoutTrashed()
                     ->helpInfo('This is not only the default client locale but also a default locale for the questionnaires'),

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 5
            QRHasMany::make('Locations', 'locations', Location::class)
                   ->collapsedByDefault(),

            // Relationship ID: 7
            QRHasMany::make('Users', 'users', UserResource::class)
                   ->collapsedByDefault(),

            // Relationship ID: 30
            HasManyThrough::make('Questionnaires', 'questionnaires', Questionnaire::class)
                          ->collapsedByDefault(),

            // Relationship ID: 34
            QRHasMany::make('Authorizations', 'authorizations', ClientAuthorization::class),

        ];
    }
}
