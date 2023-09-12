<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\Canonical;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Category extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Category::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public function subtitle()
    {
        $total = $this->questionnaires()->count();

        return $total.' '.Str::plural('questionnaire', $total);
    }

    public static function availableForNavigation(Request $request)
    {
        return
            // The user is a super admin.
            $request->user()->isSuperAdmin();
    }

    public static function softDeletes()
    {
        return request()->user()->isSuperAdmin();
    }

    public function fields(Request $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Canonical::make()
                ->helpInfo('Please ensure that, if you change this value, you do not have it hard-coded somewhere!')
                ->sortable(),

            Text::make('Description')
                ->rules('required', 'max:255'),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                   ->collapsedByDefault(),
        ];
    }
}
