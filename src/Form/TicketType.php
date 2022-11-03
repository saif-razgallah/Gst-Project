<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType ;
use Symfony\Component\Form\Extension\Core\Type\TextType ;



class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,['attr' => ['class' => 'form-control']])
            ->add('description',TextareaType::class,['attr' => ['class' => 'form-control']])
            ->add('priorite',ChoiceType::class, ['attr' => ['class' => 'form-control'],
                                            'choices' => [
                                                'Haut' => 'haut',
                                                'Normale' => 'normal',
                                                'Faible' => 'faible',
                                                ]
                                            ])
            ->add('statut',ChoiceType::class, ['attr' => ['class' => 'form-control'],
                                            'choices' => [
                                                'Non Entamé' => 'nonentame',
                                                'En Cours' => 'encours',
                                                'Achevée' => 'achevee',
                                                ]
                                            ])
            ->add('responsable')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
