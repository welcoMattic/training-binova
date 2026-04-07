<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('sync')]
final class GetVolunteerMatchesQuery
{
    public function __construct(
        public readonly int $userId
    ) {}
}
