<?php

namespace App\MessageHandler;

use App\Entity\Matching;
use App\Entity\User;
use App\Message\MatchVolunteerMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class MatchVolunteerMessageHandler
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $manager,
        #[AutowireIterator('app.matching_strategy')]
        private readonly iterable $strategies,
    ) {}

    public function __invoke(MatchVolunteerMessage $message): void
    {
        $user = $this->repository->find($message->userId);

        $matches = $this->findMatches($user);
        $this->parseMatches($matches, $user);
    }

    private function findMatches(User $user): array
    {
        $matches = [];

        foreach ($this->strategies as $strategy) {
            $matches = \array_merge($matches, $strategy->match($user));
        }

        return \array_unique($matches, SORT_REGULAR);
    }

    private function parseMatches(array $matches, User $user): void
    {
        foreach ($matches as $match) {
            $matching = (new Matching())
                ->setForUser($user)
                ->setConference($match)
            ;

            $this->manager->persist($matching);
        }

        $this->manager->flush();
    }
}
