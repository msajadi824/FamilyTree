<?php

namespace Family\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * permission
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class permission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="access", type="boolean")
     */
    private $access;

    /**
     * @ORM\ManyToOne(targetEntity="Family\TreeBundle\Entity\person", inversedBy="permissionUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Family\TreeBundle\Entity\person", inversedBy="permissionPerson")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     */
    private $person;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set access
     *
     * @param boolean $access
     * @return permission
     */
    public function setAccess($access)
    {
        $this->access = $access;
    
        return $this;
    }

    /**
     * Get access
     *
     * @return boolean 
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set user
     *
     * @param person $user
     * @return permission
     */
    public function setUser(person $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return person
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set person
     *
     * @param person $person
     * @return permission
     */
    public function setPerson(person $person)
    {
        $this->person = $person;
    
        return $this;
    }

    /**
     * Get person
     *
     * @return person
     */
    public function getPerson()
    {
        return $this->person;
    }
}