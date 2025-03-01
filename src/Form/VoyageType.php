<?php

namespace App\Form;

use App\Entity\Vehicule;
use App\Entity\Voyage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoyageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DepartureLocation')
            ->add('DestinationLocation')
            ->add('StartDate', null, [
                'widget' => 'single_text',
            ])
            ->add('EndDate', null, [
                'widget' => 'single_text',
            ])
            ->add('Status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voyage::class,
        ]);
    }
}
