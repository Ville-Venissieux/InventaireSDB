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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;




class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, array('disabled'=>true))
            ->add('nom', TextType::class, array('max_length' => 100))
            ->add('categorie', EntityType::class, array('label' => 'Catégorie',
                    'class' => 'Venissieux\InventaireSDB\FrontBundle\Entity\Categorie',
                    'choice_label' => 'libelle',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')-> orderBy('u.libelle','ASC');},
                    'required' => true))
            ->add('dateAchat',DateType::class, array('label'=>'Date d\'achat',
                'input'=>'datetime',
                'widget'=>'choice',
                //Il est nécessaire de fournir une plage d'année sinon elle est de +5/-5 ans par défaut
                'years' => range(date('Y') - 40, date('Y') + 10),
                'format'=> 'dd-MMMM-yyyy',
                'invalid_message' => 'La date d\'achat n\'est pas valide'))
            ->add('prixAchat', MoneyType::class, array('required' => false, 'invalid_message' => 'Le prix d\'achat n\'est pas valide'))
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
