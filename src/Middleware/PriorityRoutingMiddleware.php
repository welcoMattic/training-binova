<?php

namespace App\Middleware;

use App\Message\MatchVolunteerMessage;
use App\Stamp\PriorityStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

final class PriorityRoutingMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $priorityStamp = $envelope->last(PriorityStamp::class);

        if ($priorityStamp) {
            $priority = $priorityStamp->priority;

            $transport = $priority >= 8 ? 'high_priority' : 'normal_priority';
            $envelope = $envelope->with(new TransportNamesStamp([$transport]));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
