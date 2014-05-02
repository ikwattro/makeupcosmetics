<?php

namespace Store\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    protected $edition;

    public function __construct($edition = false)
    {
        $this->edition = $edition;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Nom : ',
                'attr'  =>  array(
                    'placeholder' => 'Entrez ici le nom du produit ...'
                )
            ))
            ->add('description', null, array(
                'label' => 'Déscription : '
            ))
            ->add('available', null, array(
                'label' =>  'Disponible : ',
                'attr'  =>  array(
                    'checked'   =>  'checked'
                )
            ))
            ->add('sku', null, array(
                'label' =>  'SKU : '
            ))
            ->add('file', null, array(
                'label' =>  'Fichier : ',
            ))
            ->add('meta_description', null, array(
                'label' =>  'Déscription méta : '
            ))
            ->add('meta_keywords', null, array(
                'label' =>  'Mots clés méta : '
            ))
            ->add('categories', null, array(
                'class' => 'StoreProductBundle:Category',
                'multiple' => true,
                'expanded'  => true,
                'label'     =>      'Catégories : '
            ))
            ->add('options', null, array(
                'required' => false,
                //'label' =>  'Options : ',
                'expanded'  =>  true
            ))
        ;

        if (false === $this->edition) {
            $builder->add('price', 'integer', array(
                'mapped' => false,
                'label' => 'Prix : '
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Store\ProductBundle\Entity\Product'
        ));
    }

    public function getName()
    {
        return 'store_productbundle_producttype';
    }
}
