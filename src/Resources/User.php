<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\HumanDateTime;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Cube\Models\Locale;

class User extends Resource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\User::class;

    public static $search = [
        'name', 'email',
    ];

    public function title()
    {
        return $this->name;
    }

    public function subtitle()
    {
        if ($this->client) {
            return $this->client->name;
        }
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        /**
         * The logged user can list users that:
         * View all users in case the user is super admin (OR)
         * View all users from the client that it belongs to if the logged user
         * has "admin" permission on the respective client (OR)
         * Its the user itself (so the user can change its own data)
         */
        $user = $request->user();

        // Super admin? Done.
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function ($query) use ($user) {
            if ($user->isAuthorizedAs($user->client, 'client-admin')) {
                $query->where('client_id', $user->client_id);
            }
            $query->orWhere('id', $user->id);
        });
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
                ->canSee(fn () => $request->user()->isSuperAdmin()),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->updateRules('min:8')
                ->help('Min 8 characters')
                ->canSee(function ($request) {
                    $model = $request->findModel();

                    $user = $request->user();

                    // The user is a super admin.
                    return $user->isSuperAdmin() ||

                    // The user has an "admin" authorization on this client.
                    $user->isAuthorizedAs($user->client, 'client-admin') ||

                    // It's the user itself.
                    $model->id == $user->id;
                }),

            Text::make(
                'Preferred Locale',
                fn () => Locale::where('canonical', $this->preferredLocale())
                             ->first()
                             ->name
            )->onlyOnDetail(),

            Text::make('Phone Number'),

            Boolean::make('Is super admin?', 'is_super_admin')
                ->canSee(fn ($request) => $request->user()->isSuperAdmin()),

            BelongsTo::make('Client', 'client', Client::class)
                     ->withoutTrashed()
                     ->nullable()
                     ->withoutTrashed(),

            HasMany::make('Affiliated Clients', 'affiliatedClients', Client::class)
                   ->canSee(fn () => $this->resource->isAffiliate()),

            HumanDateTime::make('Created At'),

            HumanDateTime::make('Updated At'),

            HumanDateTime::make('Deleted At')
                         ->canSee(fn ($request) => ! $request->findModel()->deleted_at == null),
        ];
    }
}