<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class OpenAIPrompt extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\OpenAIPrompt::class;

    public static $search = [
        'prompt_i_am_a_business_of',
        'prompt_i_am_paying_attention_to',
    ];

    public static function label()
    {
        return 'OpenAI Prompts';
    }

    public function title()
    {
        return 'AI Prompt for '.$this->questionnaire->name;
    }

    public static function defaultOrderings($query)
    {
        return $query->orderBy('questionnaire_id', 'desc');
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('I am a business of', 'prompt_i_am_a_business_of')
                ->rules('required')
                ->helpInfo('E.g.: A restaurant in Nancy'),

            Text::make('I am paying attention to', 'prompt_i_am_paying_attention_to')
                ->rules('required')
                ->helpInfo('Food quality, and arrival assiduity'),

            Select::make('Balance type', 'balance_type')->options([
                'balanced' => 'Balanced',
                'worst-case' => 'Worst cases',
                'best-case' => 'Best cases',
            ])
                ->helpInfo('On what do you want to focus your improvement feedback?'),

            Boolean::make('Should OpenAI be email-aware?', 'should_be_email_aware')
                   ->helpInfo('If it is, then a notification is sent to the questionnaire owner if an email is given by a visitor'),

            // Relationship ID: 18
            QRBelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
