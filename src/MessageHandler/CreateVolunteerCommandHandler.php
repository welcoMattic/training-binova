<?php

namespace App\MessageHandler;

use App\Entity\Conference;
use App\Entity\User;
use App\Entity\Volunteering;
use App\Message\CreateVolunteerCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
final class CreateVolunteerCommandHandler
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(CreateVolunteerCommand $message): void
    {
        $user = $this->manager->getRepository(User::class)->find($message->userId);
        $conference = $this->manager->getRepository(Conference::class)->find($message->conferenceId);
        $volunteering = (new Volunteering())
            ->setForUser($user)
            ->setConference($conference)
            ->setStartAt($message->startAt)
            ->setEndAt($message->endAt)
        ;

        $errors = $this->validator->validate($volunteering);

        if (0 < \count($errors)) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        $this->manager->persist($volunteering);
    }
}
