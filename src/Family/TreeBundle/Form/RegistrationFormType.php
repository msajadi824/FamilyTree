<?php
namespace Family\TreeBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('fname','text',array('label'=>'نام'))
            ->add('lname','text',array('label'=>'نام خانوادگی'))
            ->add('birthday','text',array('label'=>'تاریخ تولد','attr'=>array('class'=>'pdate')))
            ->add('nationalcode','text',array('label'=>'کد ملی'))
            ->add('gender','choice',array('label'=>'جنسیت','choices' => array('m' => 'مرد', 'f' => 'زن')))
            ->add('file','file',array('label'=>'عکس'))
        ;
    }

    public function getName()
    {
        return 'family_tree_registration';
    }
}
