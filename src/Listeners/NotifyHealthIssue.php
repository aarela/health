<?php

namespace PragmaRX\Health\Listeners;

use Notification;
use PragmaRX\Health\Events\RaiseHealthIssue;
use PragmaRX\Health\Notifications\HealthStatus;

class NotifyHealthIssue
{
    /**
     * @return static
     */
    private function getNotifiableUsers()
    {
        return collect(config('health.notifications.users.emails'))->map(
            function ($item) {
                $model = instantiate(
                    config('health.notifications.users.model')
                );

                $model->email = $item;

                return $model;
            }
        );
    }

    /**
     * Handle the event.
     *
     * @param  RaiseHealthIssue  $event
     * @return void
     */
    public function handle(RaiseHealthIssue $event)
    {
        try {
            Notification::send(
                $this->getNotifiableUsers(),
                new HealthStatus($event->failure, $event->channel)
            );
        } catch (\Exception $exception) {
            // do nothing
        } catch (\ErrorException $exception) {
            // do nothing
        }
    }
}
