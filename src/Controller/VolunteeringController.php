<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Volunteering;
use App\Form\VolunteeringType;
use App\Message\CreateVolunteerCommand;
use App\Stamp\PriorityStamp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class VolunteeringController extends AbstractController
{
    #[Route('/volunteering/{id}', name: 'app_volunteering_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Volunteering $volunteering): Response
    {
        return $this->render('volunteering/show.html.twig', [
            'volunteering' => $volunteering,
        ]);
    }

    #[Route('/volunteering/new', name: 'app_volunteering_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        #[Target('command.bus')] MessageBusInterface $commandBus,
    ): Response
    {
        $message = (new CreateVolunteerCommand($this->getUser()->getId()));
        $options = [];

        if (!$request->query->has('conference')) {
            throw $this->createNotFoundException('Conference not found');
        }

        $conference = $manager->getRepository(Conference::class)->find($request->query->get('conference'));
        $message->conferenceId = $conference->getId();

        $form = $this->createForm(VolunteeringType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->dispatch($message, [new PriorityStamp(10)]);

            return $this->redirectToRoute('app_conference_show', ['id' => $conference->getId()]);
        }

        return $this->render('volunteering/new.html.twig', [
            'form' => $form,
        ]);
    }
}
