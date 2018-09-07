<?php

namespace App\Nova\Actions;

use App\Events\ProjectHasNewEmailAddressWithAccessEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class SendHasAccessToProjectEmail extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Fields\ActionFields $fields
     * @param \Illuminate\Support\Collection    $models
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $models->each(function ($pivot) {
            event(
                new ProjectHasNewEmailAddressWithAccessEvent(
                    \App\Project::find($pivot->project_id),
                    \App\EmailAddress::find($pivot->email_address_id)
                )
            );
        });
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
