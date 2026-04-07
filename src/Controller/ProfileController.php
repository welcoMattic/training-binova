<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VolunteerProfile;
use App\Message\GetVolunteerMatchesQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ProfileController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Target('query.bus')] MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(#[CurrentUser] User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$user->getVolunteerProfile()) {
            $user->setVolunteerProfile((new VolunteerProfile())->setForUser($user));
            $entityManager->flush();
        }

        $matchings = $this->handle(new GetVolunteerMatchesQuery($user->getId()));

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'matchings' => $matchings,
        ]);
    }
}
