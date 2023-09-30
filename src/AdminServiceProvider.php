<?php

namespace QRFeedz\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use QRFeedz\Admin\Resources\Authorization;
use QRFeedz\Admin\Resources\Category;
use QRFeedz\Admin\Resources\Client;
use QRFeedz\Admin\Resources\Country;
use QRFeedz\Admin\Resources\Locale;
use QRFeedz\Admin\Resources\Location;
use QRFeedz\Admin\Resources\OpenAIPrompt;
use QRFeedz\Admin\Resources\Page;
use QRFeedz\Admin\Resources\PageInstance;
use QRFeedz\Admin\Resources\QuestionInstance;
use QRFeedz\Admin\Resources\Questionnaire;
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
        $this->registerMacros();

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::make('Management', [
                    MenuItem::resource(Questionnaire::class),
                    MenuItem::resource(Response::class),
                    MenuItem::resource(Location::class),
                    MenuItem::resource(User::class),
                    MenuItem::resource(Client::class),
                ])->icon('chart-bar')
                  ->canSee(function (NovaRequest $request) {
                      return
                            // User is super admin.
                            $request->user()->isSuperAdmin() ||

                             // User is client-admin.
                             $request->user()->isAtLeastAuthorizedAs('client-admin');
                  }),

                MenuSection::make('System', [
                    MenuItem::resource(QuestionInstance::class),
                    MenuItem::resource(OpenAIPrompt::class),
                    MenuItem::resource(Authorization::class),
                    MenuItem::resource(PageInstance::class),
                    MenuItem::resource(Category::class),
                    MenuItem::resource(Country::class),
                    MenuItem::resource(Locale::class),
                    MenuItem::resource(Page::class),
                ])->icon('server')
                  ->canSee(function (NovaRequest $request) {
                      return $request->user()->isSuperAdmin();
                  }),
            ];
        });

        Nova::resources([
            User::class,
            Country::class, // Added.
            Authorization::class, // Added.
            Category::class, // Added.
            Location::class, // Added.
            Client::class, // Added.
            Locale::class, // Added.
            Questionnaire::class, // Added.
            OpenAIPrompt::class, // Added.
            Page::class, // Added.
            PageInstance::class, // Added.
            QuestionInstance::class, // Added.
            WidgetInstance::class,
            Response::class, // Added.
            Tag::class,
            Widget::class,
        ]);
    }

    public function register()
    {
        //
    }

    protected function registerMacros(): void
    {
        // Include all files from the Macros folder.
        Collection::make(glob(__DIR__.'/Macros/*.php'))
              ->mapWithKeys(function ($path) {
                  return [$path => pathinfo($path, PATHINFO_FILENAME)];
              })
              ->each(function ($macro, $path) {
                  require_once $path;
              });
    }
}
