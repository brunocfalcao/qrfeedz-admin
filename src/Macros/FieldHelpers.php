<?php

namespace QRFeedz\Admin\Macros;

use Laravel\Nova\Fields\Field;

Field::macro('helpError', function ($message) {
    return $this->help("<span class='text-base text-red-500'>{$message}</span>");
});

Field::macro('helpWarning', function ($message) {
    return $this->help("<span class='text-base text-yellow-600'>{$message}</span>");
});

Field::macro('helpInfo', function ($message) {
    return $this->help("<span class='text-base text-primary-500'>{$message}</span>");
});
