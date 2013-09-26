<?php

namespace Family\TreeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class personSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fname','text',array('label'=>'نام','required'=>false))
            ->add('lname','text',array('label'=>'نام خانوادگی','required'=>false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Family\TreeBundle\Entity\person'
        ));
    }

    public function getName()
    {
        return 'family_treebundle_persontype';
    }
}
