<?php

namespace Venissieux\InventaireSDB\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use \Doctrine\ORM\EntityRepository;


class RetoursSearchType extends AbstractType
{
    /**
     * Construit le formulaire de recherche
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usager', EntityType::class,array('label'=>'nom',
                'class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Usager',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')-> orderBy('u.nom','ASC');},
                'choice_label' => 'nomComplet',
                'expanded' => false,
                'required' => true))
            ->add('dateRetour',DateType::class, array('label'=>'Date du retour',
                'input'=>'datetime',
                'widget'=>'choice',
                'data'=>new \DateTime(),
                'format'=> 'dd-MMMM-yyyy',
                'invalid_message' => 'La date de retour n\'est pas valide'))
            ->add('valider',  SubmitType::class,array('label'=>'Valider'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
