<?php

namespace QRFeedz\Admin;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use QRFeedz\Admin\Resources\Authorization;
use QRFeedz\Admin\Resources\Category;
use QRFeedz\Admin\Resources\Client;
use QRFeedz\Admin\Resources\Country;
use QRFeedz\Admin\Resources\Group;
use QRFeedz\Admin\Resources\Locale;
use QRFeedz\Admin\Resources\Question;
use QRFeedz\Admin\Resources\Questionnaire;
use QRFeedz\Admin\Resources\Response;
use QRFeedz\Admin\Resources\Tag;
use QRFeedz\Admin\Resources\User;
use QRFeedz\Admin\Resources\UserAuthorization;
use QRFeedz\Admin\Resources\Widget;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Nova::resources([
            User::class,
            Country::class,
            Authorization::class,
            Category::class,
            Client::class,
            Group::class,
            Locale::class,
            Question::class,
            Questionnaire::class,
            Response::class,
            Tag::class,
            UserAuthorization::class,
            Widget::class
        ]);
    }

    public function register()
    {
        //
    }
}
