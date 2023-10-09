<?php

namespace QRFeedz\Admin\Macros;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;

Field::macro('charLimit', function ($limit) {
    return $this->displayUsing(fn ($value) => Str::limit($value, $limit, '...'));
});

Field::macro('readonlyIfViaResource', function (string|array $resources = []) {

    if (is_string($resources)) {
        $resources = [$resources];
    }

    return $this->readonly(function ($request) use ($resources) {

        return
            // HTTP method is GET.
            $request->isMethod('get') &&

            // There is a query key 'viaResource'
            $request->has('viaResource') &&

            (
                // ViaResource is one of the parameter array values.
                in_array($request->input('viaResource'), $resources)
            );
    });
});

Field::macro('helpError', function ($message) {
    return $this->help("<span class='text-base text-red-500'>{$message}</span>");
});

Field::macro('helpWarning', function ($message) {
    return $this->help("<span class='text-base text-yellow-600'>{$message}</span>");
});

Field::macro('helpInfo', function ($message) {
    return $this->help("<span class='text-base text-primary-500'>{$message}</span>");
});
