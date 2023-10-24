<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\Canonical;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Category extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Category::class;

    public static $globallySearchable = false;

    public function title()
    {
        return $this->name;
    }

    public function subtitle()
    {
        $total = $this->questionnaires()->count();

        return $total.' '.Str::plural('questionnaire', $total);
    }

    public function fields(Request $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Canonical::make()
                ->helpInfo('Please ensure that, if you change this value, you do not have it hard-coded somewhere!')
                ->sortable(),

            Text::make('Description')
                ->rules('required', 'max:255'),

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 6
            QRHasMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                   ->collapsedByDefault(),
        ];
    }
}
