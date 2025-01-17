<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Marque;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('image',FileType::class,['data_class'=>null])
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('id_marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'nom_marque', 
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
