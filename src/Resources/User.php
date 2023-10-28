<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRDateTime;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Admin\Traits\HasAddressFields;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class User extends QRFeedzResource
{
    use DefaultDescPKSorting, HasAddressFields;

    public static $model = \QRFeedz\Cube\Models\User::class;

    public static $search = [
        'name', 'email',
    ];

    public static $searchRelations = [
        'country' => ['name'],
        'client' => ['name'],
        'locale' => ['name'],
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

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            new Panel('Address Information', $this->addressFields()),

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
            QRBelongsTo::make('Client', 'client', Client::class),

            // Relationship ID: 27
            QRBelongsTo::make('Locale', 'locale', Locale::class),

            new Panel('Address Information', $this->addressFields()),

            Boolean::make('Is super admin?', 'is_super_admin')
                ->helpInfo('Admin access including access to system resources and responses')
                ->canSee(fn ($request) => $request->user()->isSuperAdmin()),

            Boolean::make('Is admin?', 'is_admin')
                ->helpInfo('Admin access, without access to system resources, neither to responses')
                ->canSee(fn ($request) => $request->user()->isSystemAdminLike()),

            // Relationship ID: 1
            QRHasMany::make('Affiliated Clients', 'affiliatedClients', Client::class)
                   ->canSee(fn () => $this->resource->isAffiliate()),

            QRDateTime::make('Created At'),

            QRDateTime::make('Updated At'),

            QRDateTime::make('Deleted At')
                         ->canSee(fn ($request) => ! $request->findModel()->deleted_at == null),

            // Relationship ID: 33
            QRHasMany::make('Client Authorizations', 'clientAuthorizations', ClientAuthorization::class),

            // Relationship ID: 32
            QRHasMany::make('Questionnaire Authorizations', 'questionnaireAuthorizations', QuestionnaireAuthorization::class),
        ];
    }
}
