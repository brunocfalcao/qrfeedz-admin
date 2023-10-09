<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRDateTime;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class User extends QRFeedzResource
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
                // Adds all the users part of his client.
                $query->where('client_id', $user->client_id);
            }
            // Adds himself to the indexQuery.
            $query->orWhere('id', $user->id);
        });
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->rules('required', 'min:4', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/')
                ->withMeta([
                    'validationMessage' => [
                        'required' => 'The password field is required.',
                        'min' => 'The password must be at least 4 characters.',
                        'regex' => 'The password must contain at least one number and one special character.',
                    ],
                ])
                ->help('Min 4 characters, one number, and one special character')
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

            // Relationship ID: 7
            QRBelongsTo::make('Client', 'client', Client::class)
                     ->readonlyIfViaResource('clients')
                     ->nullable(),

            QRBelongsTo::make('Locale', 'locale', Locale::class)
                       ->readonlyIfViaResource('users'),

            Boolean::make('Is super admin?', 'is_super_admin')
                ->canSee(fn ($request) => $request->user()->isSuperAdmin()),

            // Relationship ID: 1
            HasMany::make('Affiliated Clients', 'affiliatedClients', Client::class)
                   ->canSee(fn () => $this->resource->isAffiliate()),

            QRDateTime::make('Created At'),

            QRDateTime::make('Updated At'),

            QRDateTime::make('Deleted At')
                         ->canSee(fn ($request) => ! $request->findModel()->deleted_at == null),

            // Relationship ID: 33
            HasMany::make('Client Authorizations', 'clientAuthorizations', ClientAuthorization::class),

            // Relationship ID: 32
            HasMany::make('Questionnaire Authorizations', 'questionnaireAuthorizations', QuestionnaireAuthorization::class),
        ];
    }
}
