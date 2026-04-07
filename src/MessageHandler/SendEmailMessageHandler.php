<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class SendEmailMessageHandler
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(SendEmailMessage $message): void
    {
        $user = $this->repository->find($message->id);

        if (null === $user) {
            throw new \InvalidArgumentException('User not found');
        }

        $this->mailer->send(
            (new Email())
                ->subject('Welcome to our platform!')
                ->text(sprintf('Hello %s, welcome to our platform!', $user->getUserIdentifier()))
                ->from('sender@sensio-event.com')
                ->to($user->getEmail())
        );
    }
}
