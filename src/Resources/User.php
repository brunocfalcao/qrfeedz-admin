<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Actions\ResetPassword;

class User extends Resource
{
    public static $model = \QRFeedz\Cube\Models\User::class;

    public static $search = [
        'id', 'name', 'email', 'phone_number'
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
        if ($user->is_admin) {
            return $query;
        }

        /**
         * Only users that have "admin" authorization on the respective
         * client, can manage users from its own client.
         */
        if ($user->isAuthorizedAs($user->client, 'admin')) {
            return $query->where('client_id', $user->client_id);
        }

        // Can only see itself.
        return $query->where('id', $user->id);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Text::make('Phone Number'),

            Boolean::make('Is super admin?', 'is_admin')
                ->canSee(function ($request) {
                    return $request->user()->is_admin;
                }),

            DateTime::make('Created At')
                    ->hideFromIndex()
                    ->readonly()
                    ->displayUsing(function ($value) {
                        return $value->diffForHumans();
                    }),

            DateTime::make('Updated At')
                    ->hideFromIndex()
                    ->readonly()
                    ->displayUsing(function ($value) {
                        return $value->diffForHumans();
                    }),

            DateTime::make('Deleted At')
                ->hideFromIndex()
                ->readonly()
                ->canSee(function ($request) {
                    $model = $request->findModel();
                    return !$model->deleted_at == null;
                })
                ->displayUsing(function ($value) {
                    return $value?->diffForHumans();
                })
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new ResetPassword())
                ->onlyOnDetail()
                ->size('5xl')
                ->canSee(function () use ($request) {

                    return true;

                    // Get targeted resource.
                    $model = $request->findModel();

                    // Get logged user.
                    $user = $request->user();

                    return
                        // Is super admin.
                        $request->user()->is_admin ||

                        // Is the same user.
                        $user->id == $model->id ||

                        // Is a client-based admin.
                        $user->isAuthorizedAs($user->client, 'admin');
                })
        ];
    }
}
