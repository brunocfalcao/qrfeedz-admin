<?php

namespace QRFeedz\Admin\Actions;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResetPassword extends Action implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        //
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }

    /**
     * Register `then`, `catch` and `finally` event on batchable job.
     *
     * @return void
     */
    public function withBatch(ActionFields $fields, PendingBatch $batch)
    {
        //
    }
}
