<?php

namespace Family\TreeBundle\Controller;

use Family\TreeBundle\Entity\partner;
use Family\TreeBundle\Entity\person;
use Family\TreeBundle\Form\personSearchType;
use Family\TreeBundle\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $form = $this->createForm(new personSearchType());

        return $this->render('FamilyTreeBundle:Default:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function searchAction(Request $request)
    {
        $form = $this->createForm(new personSearchType());
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder()
            ->select('p')
            ->from('FamilyTreeBundle:person', 'p');
        $form->handleRequest($request);
        $data = $form->getData();

        if ($data->getFname() != null) $qb->andWhere('p.fname LIKE :fn')->setParameter('fn', '%'.$data->getFname().'%');
        if ($data->getLname() != null) $qb->andWhere('p.lname LIKE :ln')->setParameter('ln', '%'.$data->getLname().'%');

        $persons = $qb->getQuery()->getResult();

        return $this->render('FamilyTreeBundle:Default:search.html.twig', array(
            'form' => $form->createView(),
            'persons' => $persons
        ));
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FamilyTreeBundle:person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('این فرد وجود ندارد');
        }

        if($entity->getPermission() == 'pro' && $this->getUser()==null)
        {
            throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');
        }

        if($entity->getPermission() == 'pri')
        {
            if($this->getUser()==null)
            {
                throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');
            }
            else
            {
                if($this->checkHasRelationAction($entity)->getContent()=='none')
                    throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');
            }
        }

        return $this->render('FamilyTreeBundle:Default:show.html.twig', array(
            'person' => $entity,
        ));
    }

    public function registerFamilyAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $personsearch = new person();
        $personsearch->setLname($user->getLname());
        $form = $this->createForm(new personSearchType(),$personsearch);

        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            $id = $request->get('id');
            $relation = $request->get('relation');
//            die('id:-'.$id.'- rel:-'.$relation.'-');
            if($id!="" && $user->getId()!=$id)
            {
                $this->setRelation($id,$relation);
                $em->flush();
            }
        }

        $qb = $em->createQueryBuilder()
            ->select('p')
            ->from('FamilyTreeBundle:person', 'p')
            ->where('p != :user')->setParameter('user', $user);
        if ($personsearch->getFname() != null) $qb->andWhere('p.fname LIKE :fn')->setParameter('fn', '%'.$personsearch->getFname().'%');
        if ($personsearch->getLname() != null) $qb->andWhere('p.lname LIKE :ln')->setParameter('ln', '%'.$personsearch->getLname().'%');
        $personssearch = $qb->getQuery()->getResult();

        return $this->render('FamilyTreeBundle:Default:RegisterFamily.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'persons' => $personssearch
        ));
    }

    public function registerNewFamilyAction(Request $request,$relation)
    {
        $user= $this->getUser();
        $person  = new person();
        if($relation=='father') $person->setGender('m');
        if($relation=='mother') $person->setGender('f');
        if($relation=='partner') $person->setGender($user->getGender()=='m'?'f':'m');

        $form = $this->createForm(new RegistrationFormType('Family\TreeBundle\Entity\person'), $person, array('validation_groups' => array('Registration')));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person->setPermission('pro');
            $person->setDeleted(0);
            $person->setEnabled(1);
            $person->setFileName($person->getFile()->getClientOriginalExtension());
            $em->persist($person);
            $em->flush();
            $person->upload();
            $this->setRelation($person->getId(),$relation);
            $em->flush();
            return $this->redirect($this->generateUrl('family_tree_register_family'));
        }

        return $this->render('FamilyTreeBundle:Default:RegisterNewFamily.html.twig', array(
            'entity' => $person,
            'form'   => $form->createView(),
            'relation' => $relation
        ));
    }

//    -------------------------------

    public function checkHasRelationAction(person $person)
    {
        $user = $this->getUser();
        if($user == $person)
            return new Response('myself');

        if($user->getFather() == $person)
            return new Response( 'father');

        if ($user->getMother() == $person)
            return new Response( 'mother');

        if($person->getFather() == $user || $person->getMother() == $user)
            return new Response( 'child');

        $em= $this->getDoctrine()->getManager();

        if(    ($user->getFather() != null && $user->getFather() == $person->getFather())
            || ($user->getMother() != null && $user->getMother() == $person->getMother())  )
            return new Response( 'sibling');

        $findpartner1 = $em->getRepository('FamilyTreeBundle:partner')->findOneBy(array('Person'=>$person,'Person2'=>$user));
        $findpartner2 = $em->getRepository('FamilyTreeBundle:partner')->findOneBy(array('Person'=>$user,'Person2'=>$person));
        if(count($findpartner1)>0 || count($findpartner2)>0)
            return new Response( 'partner');

        return  new Response('none');
    }

    public function setRelation($personid, $relation)
    {
        $em= $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $person = $em->getRepository('FamilyTreeBundle:person')->find($personid);

        $this->removeRelation($person);

        switch ($relation)
        {
            case 'father':
                $user->setFather($person);
                $person->addChildrenFromFather($user);
                break;

            case 'mother':
                $user->setMother($person);
                $person->addChildrenFromMother($user);
                break;

            case 'sibling':
                if($user->getFather()==null)
                {
                    $this->container->get('session')->getFlashBag()->add('error', 'لطفا ابتدا پدر خود را ثبت کنید');
                    return;
                }
                $person->setFather($user->getFather());
                break;

            case 'child':
                if($user->getGender()=='m')
                {
                    $person->setFather($user);
                    $user->addChildrenFromFather($person);
                }
                else
                {
                    $person->setMother($user);
                    $user->addChildrenFromMother($person);
                }
                break;

            case 'partner':
                $partner = new partner();
                $partner->setPerson($user);
                $partner->setPerson2($person);
                $em->persist($partner);
                $partner2 = new partner();
                $partner2->setPerson($person);
                $partner2->setPerson2($user);
                $em->persist($partner2);
                break;

            case 'none':
                break;
        }
    }

    public function removeRelation($person)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($person->getFather()==$user) $person->setFather(null);
        if($user->getFather()==$person) $user->setFather(null);

        if($person->getMother()==$user) $person->setMother(null);
        if($user->getMother()==$person) $user->setMother(null);

        if($user->getFather()==$person->getFather()) $person->setFather(null);

        $qb = $em->createQueryBuilder()
            ->delete('FamilyTreeBundle:partner', 'p')
            ->orWhere('p.Person = :pa and p.Person2 = :pb')
            ->orWhere('p.Person = :pb and p.Person2 = :pa')
            ->setParameter('pa', $person)->setParameter('pb', $user)
            ->getQuery()->execute();
    }
}
