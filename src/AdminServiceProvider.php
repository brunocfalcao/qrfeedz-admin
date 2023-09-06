<?php

namespace QRFeedz\Admin;

use Illuminate\Support\Collection;
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
    private $novaBooted = false;

    public function boot()
    {
        if ($this->novaBooted) {
            $this->registerMacros();

            Nova::resources([
                User::class,
                Country::class,
                Authorization::class,
                Category::class,
                Location::class,
                Client::class,
                Locale::class,
                Questionnaire::class,
                OpenAIPrompt::class,
                Page::class,
                PageInstance::class,
                QuestionInstance::class,
                WidgetInstance::class,
                Response::class,
                Tag::class,
                Widget::class,
            ]);
        }
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
