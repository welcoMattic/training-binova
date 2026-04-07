<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessage('high_priority')]
final class CreateVolunteerCommand
{
    public function __construct(
        public int $userId,
        public ?int $conferenceId = null,
        #[Assert\GreaterThanOrEqual('today')] public ?\DateTimeImmutable $startAt = null,
        #[Assert\GreaterThanOrEqual(propertyPath: 'startAt')] public ?\DateTimeImmutable $endAt = null,
    ) {}
}
