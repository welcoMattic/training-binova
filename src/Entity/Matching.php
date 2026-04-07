<?php

namespace App\Entity;

use App\Repository\MatchingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MatchingRepository::class)]
class Matching
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'matchings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $forUser = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conference $conference = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getForUser(): ?User
    {
        return $this->forUser;
    }

    public function setForUser(?User $forUser): static
    {
        $this->forUser = $forUser;

        return $this;
    }

    public function getConference(): ?Conference
    {
        return $this->conference;
    }

    public function setConference(?Conference $conference): static
    {
        $this->conference = $conference;

        return $this;
    }
}
