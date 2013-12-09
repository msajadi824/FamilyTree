<?php
namespace Family\TreeBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('fname','text',array('label'=>'نام'))
            ->add('lname','text',array('label'=>'نام خانوادگی'))
            ->add('birthday','text',array('label'=>'تاریخ تولد','attr'=>array('class'=> 'pdate')))
            ->add('deathday','text',array('required'=>false,'label'=>'تاریخ وفات','attr'=>array('class'=> 'pdate')))
            ->add('nationalcode','text',array('label'=>'کد ملی'))
            ->add('gender','choice',array('label'=>'جنسیت','choices' => array('m' => 'مرد', 'f' => 'زن')))
            ->add('permission','choice',array('label'=>'سطح دسترسی','choices' => array('pub' => 'عمومی', 'pro' => 'اعضای سایت', 'pri' => 'اقوام درجه یک')))
            ->add('file','file',array('label'=>'عکس','required'=>false))
        ;
    }

    public function getName()
    {
        return 'family_tree_profile';
    }
}
