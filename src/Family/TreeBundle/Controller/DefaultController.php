<?php

namespace Family\TreeBundle\Controller;

use Family\TreeBundle\Entity\partner;
use Family\TreeBundle\Entity\permission;
use Family\TreeBundle\Entity\person;
use Family\TreeBundle\Form\permissionType;
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

        if($entity->getPermission() == 'pro')
        {
            if($this->getUser()==null)
                throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');
            else
                $this->checkPermission($entity);
        }

        if($entity->getPermission() == 'pri')
        {
            if($this->getUser()==null)
                throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');
            else
                if(!$this->checkPermission($entity) && $this->checkHasRelationAction($entity)->getContent()=='none')
                    throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');
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

    public function permissionAction(Request $request)
    {
        $user = $this->getUser();

        $permission = new permission();
        $permission->setUser($user);
        $form = $this->createForm(new permissionType($user),$permission);

        if($request->isMethod("post"))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($permission);
                $em->flush();
            }
        }

        $permissions = $user->getPermissionUser();

        return $this->render("FamilyTreeBundle:Default:permission.html.twig",array(
                'permissions'=> $permissions,
                'form' => $form->createView()
            ));
    }

    public function permissionDeleteAction($id)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $permission = $em->getRepository("FamilyTreeBundle:permission")->find($id);

        if (!$permission) {
            throw $this->createNotFoundException('این مجوز وجود ندارد');
        }

        if ($permission->getUser() != $user) {
            throw $this->createNotFoundException('این مجوز از شما نیست');
        }

        $em->remove($permission);
        $em->flush();

        return $this->redirect($this->generateUrl("family_tree_register_permission"));
    }
//    -------------------------------

    public function checkHasRelationAction(person $person)
    {
        $user = $this->getUser();
        if($user == $person)
            return new Response('myself');

        if($user->getFather() == $person)
            return new Response('father');

        if ($user->getMother() == $person)
            return new Response('mother');

        if($person->getFather() == $user || $person->getMother() == $user)
            return new Response('child');

        $em= $this->getDoctrine()->getManager();

        if(    ($user->getFather() != null && $user->getFather() == $person->getFather())
            || ($user->getMother() != null && $user->getMother() == $person->getMother())  )
            return new Response('sibling');

        $findpartner1 = $em->getRepository('FamilyTreeBundle:partner')->findOneBy(array('Person'=>$person,'Person2'=>$user));
        $findpartner2 = $em->getRepository('FamilyTreeBundle:partner')->findOneBy(array('Person'=>$user,'Person2'=>$person));
        if(count($findpartner1)>0 || count($findpartner2)>0)
            return new Response('partner');

        if($user->getFather()!= null && $user->getFather()->getFather()== $person)
            return new Response('paternal grandfather');

        if($user->getFather()!= null && $user->getFather()->getMother()== $person)
            return new Response('paternal grandmother');

        if($user->getMother()!= null && $user->getMother()->getFather()== $person)
            return new Response('maternal grandfather');

        if($user->getMother()!= null && $user->getMother()->getMother()== $person)
            return new Response('maternal grandmother');

        if($user->getFather()!= null && $person->getFather()!= null && $user->getFather()->getFather() == $person->getFather())
            return new Response('father sibling');

        if($user->getMother()!= null && $person->getFather()!= null && $user->getMother()->getFather() == $person->getFather())
            return new Response('mother sibling');

        return  new Response('none');
    }

    public function setRelation($personid, $relation)
    {
        $em= $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $person = $em->getRepository('FamilyTreeBundle:person')->find($personid);

        $beforeRelation = $this->checkHasRelationAction($person)->getContent();
        $this->removeRelation($person, $beforeRelation);

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
                $person->setMother($user->getMother());
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

            case 'paternal grandfather':
                $user->getFather()->setFather($person); break;

            case 'paternal grandmother':
                $user->getFather()->setMother($person); break;

            case 'maternal grandfather':
                $user->getMother()->setFather($person); break;

            case 'maternal grandmother':
                $user->getMother()->setMother($person); break;

            case 'father sibling':
                $person->setFather($user->getFather()->getFather());
                $person->setMother($user->getFather()->getMother());
                break;

            case 'mother sibling':
                $person->setFather($user->getMother()->getFather());
                $person->setMother($user->getMother()->getMother());
                break;

            case 'none':
                break;
        }
    }

    public function removeRelation(person $person, $relation)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $this->getUser();

        switch ($relation)
        {
            case 'father':
                $user->setFather(null); break;

            case 'mother':
                $user->setMother(null); break;

            case 'child':
                if($user->getGender()=='m')
                    $person->setFather(null);
                else
                    $person->setMother(null);
                break;

            case 'sibling':
                $person->setFather(null);
                $person->setMother(null);
                break;

            case 'partner':
                $em->createQueryBuilder()
                    ->delete('FamilyTreeBundle:partner', 'p')
                    ->orWhere('p.Person = :pa and p.Person2 = :pb')
                    ->orWhere('p.Person = :pb and p.Person2 = :pa')
                    ->setParameter('pa', $person)->setParameter('pb', $user)
                    ->getQuery()->execute();
                break;

            case 'paternal grandfather':
                $user->getFather()->setFather(null); break;

            case 'paternal grandmother':
                $user->getFather()->setMother(null); break;

            case 'maternal grandfather':
                $user->getMother()->setFather(null); break;

            case 'maternal grandmother':
                $user->getMother()->setMother(null); break;

            case 'father sibling':
                $person->setFather(null);
                $person->setMother(null);
                break;

            case 'mother sibling':
                $person->setFather(null);
                $person->setMother(null);
                break;
        }
    }

    public function checkPermission(person $person)
    {
        $em = $this->getDoctrine()->getManager();
        $permission = $em->getRepository("FamilyTreeBundle:permission")->findOneBy(array("person"=>$this->getUser(),"user"=>$person));

        if($permission && !$permission->getAccess())
            throw new \Exception('شما مجاز به مشاهده این صفحه نیستید');

        return $permission?true:false;
    }
}
