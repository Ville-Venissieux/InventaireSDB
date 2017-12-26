<?php

namespace Venissieux\InventaireSDB\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Formulaire Utilisateur
 */
class UtilisateurType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', TextType::class, array('max_length' => 255))
                ->add('prenom', TextType::class, array('max_length' => 30, 'required' => false))
                ->add('nom', TextType::class, array('max_length' => 30, 'required' => false))
                ->add('roles', ChoiceType::class, array('label' => 'Rôles',
                    'choices' => array(
                        'ROLE_GESTIONNAIRE' => 'Gestionnaire',
                        'ROLE_ADMIN' => 'Administrateur'
                    ),
                    'multiple' => true,
                    'expanded' => true))
                ->add('valider', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Utilisateur'
        ));
    }

}
