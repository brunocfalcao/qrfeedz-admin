<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\Canonical;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Locale extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Locale::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static function softDeletes()
    {
        return request()->user()->isSuperAdmin();
    }

    public static function availableForNavigation(Request $request)
    {
        $user = $request->user();

        return
            // The user is a super admin.
            $user->isSuperAdmin();
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Canonical::make()
                ->sortable(),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Clients', 'clients', Client::class)
                   ->collapsedByDefault(),

            HasMany::make('Questionnaires', 'questionnaires', Questionnaire::class)
                   ->collapsedByDefault(),

            HasMany::make('Users', 'users', User::class)
                   ->collapsedByDefault(),

            MorphedByMany::make('Related Question instances', 'questionInstances', QuestionInstance::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption')
                          ->sortable(),

                    Text::make('Placeholder', 'placeholder')
                          ->sortable(),
                ])
                ->nullable()
                ->collapsedByDefault(),

            MorphedByMany::make('Related Widget instances', 'widgetInstances', WidgetInstance::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption')
                          ->sortable(),

                    Text::make('Placeholder', 'placeholder')
                          ->sortable(),
                ])
                ->nullable()
                ->collapsedByDefault(),
        ];
    }
}
