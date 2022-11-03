<?php

namespace App\Form;

use App\Entity\Sprint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType ;
use Symfony\Component\Form\Extension\Core\Type\TextType ;


class SprintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,['attr' => ['class' => 'form-control']])
            ->add('date_debut',DateType::class, [
                                           'widget' => 'single_text',
                                           'html5' => false,
                                           'format' => 'yyyy-MM-dd',
                                           'attr' => ['class' => 'js-datepicker form-control']
                                            ])
            ->add('date_fin',DateType::class, [
                                           'widget' => 'single_text',
                                           'html5' => false,
                                           'format' => 'yyyy-MM-dd',
                                           'attr' => ['class' => 'js-datepicker form-control']
                                            ])
            ->add('description',TextareaType::class,['attr' => ['class' => 'form-control']])
            
            //->add('tickets')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sprint::class,
        ]);
    }
}
