<?php

namespace App\MessageHandler;

use App\Entity\Matching;
use App\Entity\User;
use App\Message\GetVolunteerMatchesQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetVolunteerMatchesQueryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {}

    public function __invoke(GetVolunteerMatchesQuery $message): array
    {
        $user = $this->manager->getRepository(User::class)->find($message->userId);

        return $this->manager
            ->getRepository(Matching::class)
            ->findBy(['forUser' => $user]);
    }
}
