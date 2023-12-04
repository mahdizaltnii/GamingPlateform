<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Categorie;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('date')
            ->add('lieu')
            ->add('description')
            ->add('picture', FileType::class, ([
                 'mapped' => false,
                 'label'=>'ajouter votre image']
               ))
           
            ->add('Categorie' , EntityType::class, array(
                'class' => 'App\Entity\Categorie',
                'choice_label' => function ($Categorie){ 
                   
                    return  $Categorie->getName() ;}
            ))
        ;
    }
     
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
