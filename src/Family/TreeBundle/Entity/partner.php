<?php

namespace Family\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * partner
 *
 * 
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="Unnamed_Index", columns={"person_id2","person_id"})})
 */
class partner
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="date", nullable=true)
     */
    private $divorceday;

    /** 
     * @ORM\Column(type="date", nullable=true)
     */
    private $marriageday;

    /** 
     * @ORM\ManyToOne(targetEntity="Family\TreeBundle\Entity\person", inversedBy="partners2")
     * @ORM\JoinColumn(name="person_id2", referencedColumnName="id", nullable=false)
     */
    private $Person2;

    /** 
     * @ORM\ManyToOne(targetEntity="Family\TreeBundle\Entity\person", inversedBy="partners")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     */
    private $Person;


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
     * Set divorceday
     *
     * @param \DateTime $divorceday
     * @return partner
     */
    public function setDivorceday($divorceday)
    {
        $this->divorceday = $divorceday;
    
        return $this;
    }

    /**
     * Get divorceday
     *
     * @return \DateTime 
     */
    public function getDivorceday()
    {
        return $this->divorceday;
    }

    /**
     * Set marriageday
     *
     * @param \DateTime $marriageday
     * @return partner
     */
    public function setMarriageday($marriageday)
    {
        $this->marriageday = $marriageday;
    
        return $this;
    }

    /**
     * Get marriageday
     *
     * @return \DateTime 
     */
    public function getMarriageday()
    {
        return $this->marriageday;
    }

    /**
     * Set Person2
     *
     * @param \Family\TreeBundle\Entity\person $person2
     * @return partner
     */
    public function setPerson2(\Family\TreeBundle\Entity\person $person2)
    {
        $this->Person2 = $person2;
    
        return $this;
    }

    /**
     * Get Person2
     *
     * @return \Family\TreeBundle\Entity\person 
     */
    public function getPerson2()
    {
        return $this->Person2;
    }

    /**
     * Set Person
     *
     * @param \Family\TreeBundle\Entity\person $person
     * @return partner
     */
    public function setPerson(\Family\TreeBundle\Entity\person $person)
    {
        $this->Person = $person;
    
        return $this;
    }

    /**
     * Get Person
     *
     * @return \Family\TreeBundle\Entity\person 
     */
    public function getPerson()
    {
        return $this->Person;
    }
}