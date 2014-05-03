<?php

namespace Store\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class VariantType extends AbstractType
{

    protected $options;

    protected $variant;

    public function __construct($options = array(), $variant)
    {
        $this->options = $options;
        $this->variant = $variant;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('is_master', null, array(
                'label' => 'Définie comme variante maître ? :'
            ))
            ->add('sku')
            ->add('price', 'number', array(
                'label' => 'Prix :'
            ))
            ->add('product', null, array(
                'label' => 'Produit : ',
            ))
        ;
        foreach ($this->options as $option) {
            $builder->add($option->getName(), 'entity', array(
                'label' => ucfirst($option->getName()).' :',
                'class' => 'StoreProductBundle:OptionValue',
                'property' => 'name',
                'mapped' => false,
                'query_builder' => function(EntityRepository $er) use($option) {
                    return $er->createQueryBuilder('v')
                        ->where('v.option = :id')
                        ->setParameter('id', $option);
                },
                'data' => $this->variant->getValFor($option->getName())
            ));

        }

        $builder->add('out_of_stock');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Store\ProductBundle\Entity\Variant'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'store_productbundle_variant';
    }
}
