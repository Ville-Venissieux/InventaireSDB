<?php

namespace Venissieux\InventaireSDB\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use \Doctrine\ORM\EntityRepository;

/**
 * Formulaire Edition
 */
class EditionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('disponible', ChoiceType::class, array('label' => 'Disponibilité',
                    'choices' => array('1' => 'Tous', '2' => 'Disponible', '3' => 'Prêté',
            )))
                ->add('categorie', EntityType::class, array('label' => 'Catégorie',
                    'class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Categorie',
                    'choice_label' => 'libelle',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')->orderBy('u.libelle', 'ASC');
                    },
                    'required' => false,
                    'empty_data' => 'Tous'))
                ->add('usager', EntityType::class, array('label' => 'Usager',
                    'class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Usager',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')->orderBy('u.nom', 'ASC');
                    },
                    'choice_label' => 'nomComplet',
                    'expanded' => false,
                    'required' => false))
                ->add('historique', CheckboxType::class, array(
                    'label' => 'Avec historique des mouvements',
                    'required' => false))
                ->add('editer', SubmitType::class, array('label' => 'Lancer l\'édition'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array());
    }

}
