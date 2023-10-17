<?php

namespace QRFeedz\Admin\Macros;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;

Field::macro('charLimit', function ($limit) {
    return $this->displayUsing(function ($value) use ($limit) {
        if (strlen($value) <= $limit) {
            return $value;
        }

        $breakpoint = strpos($value, ' ', $limit);
        if (false === $breakpoint) {
            return $value;
        }

        return substr($value, 0, $breakpoint) . ' [...]';
    });
});

Field::macro('readonlyIfViaResource', function (string|array $resources = []) {

    if (is_string($resources)) {
        $resources = [$resources];
    }

    return $this->readonly(function ($request) use ($resources) {

        return
            // HTTP method is GET.
            $request->isMethod('GET') &&

            // There is a query key 'viaResource'.
            $request->has('viaResource') &&

            // And that viaResource key is not blank.
            ! blank($request->input('viaResource')) &&

            // ViaResource value is one of the argument array values.
            in_array($request->input('viaResource'), $resources);
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

Field::macro('resolveOnIndex', function ($value) {
});
