<?php

namespace App\Form;

use App\Entity\Conference;
use App\Message\CreateVolunteerCommand;
use App\Repository\ConferenceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

class VolunteeringType extends AbstractType
{
    public function __construct(
        private readonly ConferenceRepository $repository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('endAt', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('conferenceId', EntityType::class, [
                'class' => Conference::class,
                'choice_label' => 'name',
            ])
        ;
        $builder->get('conferenceId')->addModelTransformer(new CallbackTransformer(
            function (int $id): ?Conference {
                return $this->repository->find($id);
            },
            function (Conference $conference): int {
                return $conference->getId();
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateVolunteerCommand::class,
        ]);
    }
}
