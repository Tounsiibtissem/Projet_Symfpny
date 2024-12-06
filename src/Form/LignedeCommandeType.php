<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\LignedeCommande;
use App\Entity\Panier;
use App\Entity\product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LignedeCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantité')
            ->add('produit', EntityType::class, [
                'class' => product::class,
                'choice_label' => 'id',
            ])
            ->add('id_commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'id',
            ])
            ->add('Id_panier', EntityType::class, [
                'class' => Panier::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LignedeCommande::class,
        ]);
    }
}
