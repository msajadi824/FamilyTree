<?php

namespace Family\TreeBundle\Form;

use Doctrine\ORM\EntityRepository;
use Family\TreeBundle\Entity\person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class permissionType extends AbstractType
{
    private $user;

    public function __construct(person $_user)
    {
        $this->user = $_user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;
        $builder
            ->add('person',null,array(
                    'label'=>'شخص',
                    'query_builder' => function(EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where("u != :user")
                                ->andWhere("u NOT IN (".
                                    $er->createQueryBuilder("users")
                                        ->innerJoin("users.permissionPerson","permission")
                                        ->where("permission.user = :user")
                                        ->getDQL()
                                    .")")->setParameter("user",$user)
                                ;
                        },
                ))
            ->add('access','choice',array(
                    'label'=>'مجوز دیدن مشخصات شما',
                    'choices' => array(true=>"دارد",false=>"ندارد")
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Family\TreeBundle\Entity\permission'
        ));
    }

    public function getName()
    {
        return 'family_treebundle_permissiontype';
    }
}
