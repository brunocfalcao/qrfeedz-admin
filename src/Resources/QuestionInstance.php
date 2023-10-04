<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Fields\QRUUID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class QuestionInstance extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\QuestionInstance::class;

    public static $globallySearchable = false;

    public function title()
    {
        return 'Question '.$this->pageInstance->page->name.' instance';
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
            QRID::make(),

            QRUUID::make(),

            BelongsTo::make('Page instance', 'pageInstance', PageInstance::class)
                     ->withoutTrashed(),

            Boolean::make('Is analytical?', 'is_analytical')
                   ->helpInfo('If the question instance value will be used for reports.<br/>If it is not then it can be to display a message, or to capture custom information'),

            Boolean::make('Contains personal data?', 'is_used_for_personal_data')
                   ->helpInfo('If the value is data-sensitive to GDPR scopes'),

            Boolean::make('Is required?', 'is_required'),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Widget instances', 'widgetInstances', WidgetInstance::class),

            HasMany::make('Responses', 'responses', Response::class),

            MorphToMany::make('Captions', 'captions', Locale::class)
                ->fields(fn () => [
                    Text::make('Caption', 'caption'),
                    Text::make('Placeholder', 'placeholder'),
                ])
                ->nullable()
                ->collapsedByDefault(),

        ];
    }
}
