<?php

namespace QRFeedz\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use QRFeedz\Admin\Resources\Authorization;
use QRFeedz\Admin\Resources\Category;
use QRFeedz\Admin\Resources\Client;
use QRFeedz\Admin\Resources\ClientAuthorization;
use QRFeedz\Admin\Resources\Country;
use QRFeedz\Admin\Resources\Locale;
use QRFeedz\Admin\Resources\Location;
use QRFeedz\Admin\Resources\OpenAIPrompt;
use QRFeedz\Admin\Resources\QuestionInstance;
use QRFeedz\Admin\Resources\Questionnaire;
use QRFeedz\Admin\Resources\QuestionnaireAuthorization;
use QRFeedz\Admin\Resources\Response;
use QRFeedz\Admin\Resources\Tag;
use QRFeedz\Admin\Resources\User;
use QRFeedz\Admin\Resources\Widget;
use QRFeedz\Admin\Resources\WidgetInstance;
use QRFeedz\Foundation\Abstracts\QRFeedzServiceProvider;

class AdminServiceProvider extends QRFeedzServiceProvider
{
    public function boot()
    {
        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::make('Main Menu', [
                    MenuItem::resource(Client::class),
                    MenuItem::resource(Location::class),
                    MenuItem::resource(Questionnaire::class),
                    MenuItem::resource(User::class),
                ])->icon('chart-bar')
                  ->canSee(function (NovaRequest $request) {
                      return
                       // User is affiliate.
                       Auth::user()->isAffiliate();
                  }),

                MenuSection::make('Main Menu', [
                    MenuItem::resource(Client::class),
                    MenuItem::resource(Location::class),
                    MenuItem::resource(Questionnaire::class),
                    MenuItem::resource(Response::class),
                    MenuItem::resource(User::class),
                ])->icon('chart-bar')
                  ->canSee(function (NovaRequest $request) {
                      return
                        // User is client-admin.
                        Auth::user()->isAtLeastAuthorizedAs('client-admin');
                  }),

                MenuSection::make('Main Menu', [
                    MenuItem::resource(Questionnaire::class),
                    MenuItem::resource(Response::class),
                ])->icon('chart-bar')
                  ->canSee(function (NovaRequest $request) {
                      return
                        // User is client-admin.
                        Auth::user()->isAtLeastAuthorizedAs('questionnaire-admin');
                  }),

                MenuSection::make('Main Menu', [
                    MenuItem::resource(ClientAuthorization::class),
                    MenuItem::resource(QuestionnaireAuthorization::class),
                    MenuItem::resource(QuestionInstance::class),
                    MenuItem::resource(OpenAIPrompt::class),
                    MenuItem::resource(Tag::class),
                    MenuItem::resource(WidgetInstance::class),
                ])->icon('server')
                  ->canSee(function (NovaRequest $request) {
                      return Auth::user()->isAdmin();
                  }),

                MenuSection::make('Main Menu', [
                    MenuGroup::make('Authorizations', [
                        MenuItem::resource(Authorization::class),
                        MenuItem::resource(ClientAuthorization::class),
                        MenuItem::resource(QuestionnaireAuthorization::class)
                                ->name('Quest. Authorizations'),
                    ])->collapsable()
                      ->collapsedByDefault(),

                    MenuGroup::make('Runtime', [
                        MenuItem::resource(Client::class),
                        MenuItem::resource(Location::class),
                        MenuItem::resource(Questionnaire::class),
                        MenuItem::resource(User::class),
                        MenuItem::resource(Response::class),
                        MenuItem::resource(Tag::class),
                    ])->collapsable()
                      ->collapsedByDefault(),

                    MenuGroup::make('Admin', [
                        MenuItem::resource(OpenAIPrompt::class),
                        MenuItem::resource(QuestionInstance::class),
                        MenuItem::resource(WidgetInstance::class),
                    ])->collapsable()
                      ->collapsedByDefault(),

                    MenuGroup::make('System', [
                        MenuItem::resource(Category::class),
                        MenuItem::resource(Country::class),
                        MenuItem::resource(Locale::class),
                        MenuItem::resource(Widget::class),
                    ])->collapsable()
                      ->collapsedByDefault(),
                ])->icon('server')
                  ->canSee(function (NovaRequest $request) {
                      return Auth::user()->isSuperAdmin();
                  }),
            ];
        });

        Nova::resources([
            User::class, // Added.
            Country::class, // Added.
            Authorization::class, // Added.
            Category::class, // Added.
            Location::class, // Added.
            Client::class, // Added.
            Locale::class, // Added.
            Questionnaire::class, // Added.
            OpenAIPrompt::class, // Added.
            QuestionInstance::class, // Added.
            WidgetInstance::class,
            Response::class, // Added.
            Tag::class, // Added.
            Widget::class, // Added.
            WidgetInstance::class, // Added.
            ClientAuthorization::class, // Added.
            QuestionnaireAuthorization::class, // Added.
        ]);
    }

    public function register()
    {
        //
    }
}
