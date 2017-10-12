<?php

namespace Venissieux\InventaireSDB\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use \Doctrine\ORM\EntityRepository;
use Venissieux\InventaireSDB\FrontBundle\Entity\Etat;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;




class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('max_length' => 100))
            ->add('categorie', EntityType::class, array('label' => 'Catégorie',
                    'class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Categorie',
                    'choice_label' => 'libelle',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')-> orderBy('u.libelle','ASC');},
                    'required' => true))
            ->add('date_achat',DateType::class, array('label'=>'Date d\'achat','input'=>'datetime','widget'=>'choice', 'format'=> 'dd-MMMM-yyyy','invalid_message' => 'La date d\'achat n\'est pas valide'))
            ->add('prix_achat', MoneyType::class, array('required' => false, 'invalid_message' => 'Le prix d\'achat n\'est pas valide'))
            ->add('fournisseur', TextType::class, array('max_length' => 100, 'required' => false))
            ->add('etat', EntityType::class, array('label' => 'Etat',
                    'class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Etat',
                    'choice_label' => 'libelle',
                    'required' => true))
            ->add('commentaire', TextareaType::class, array('max_length' => 500, 'required' => false))
            ->add('valider', SubmitType::class, array('label' => 'Valider'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Article'
        ));
    }
}
