<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom',TextType::class,[
                'label'=>'Nom Produit',
                'attr'=>[
                    'placeholder'=>'Merci de définir le nom'
                    ]
            ])

            ->add('Description', TextType::class,[
                'label'=>'Description',
                'attr'=>[
                    'placeholder'=>'Description'
                    ]
            ])

           

            ->add('PrixProd', NumberType::class,[
                'label'=>'Prix',
                'attr'=>[
                    'placeholder'=>'Prix'
                    ]
            ])

            ->add('image')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
