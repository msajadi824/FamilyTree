<?php

namespace Family\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * person
 *
 * 
 * @ORM\Entity
 */
class person extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false, name="fname")
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false, name="lname")
     */
    private $lname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="string", nullable=false, name="birthday")
     */
    private $birthday;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="string", nullable=true, name="deathday")
     */
    private $deathday;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=false, name="nationalcode")
     */
    private $nationalcode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1, nullable=false, name="gender")
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=false, name="permission")
     */
    private $permission;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, name="deleted")
     */
    private $deleted;

    /**
     * @ORM\Column(name="fileName", type="string", length=5, nullable=true)
     */
    private $fileName;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity="Family\TreeBundle\Entity\partner", mappedBy="Person")
     */
    private $partners;

    /** 
     * @ORM\OneToMany(targetEntity="Family\TreeBundle\Entity\partner", mappedBy="Person2")
     */
    private $partners2;

    /** 
     * @ORM\OneToMany(targetEntity="Family\TreeBundle\Entity\person", mappedBy="Father")
     */
    private $ChildrenFromFather;

    /** 
     * @ORM\OneToMany(targetEntity="Family\TreeBundle\Entity\person", mappedBy="Mother")
     */
    private $ChildrenFromMother;

    /** 
     * @ORM\ManyToOne(targetEntity="Family\TreeBundle\Entity\person", inversedBy="ChildrenFromFather")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    private $Father;

    /** 
     * @ORM\ManyToOne(targetEntity="Family\TreeBundle\Entity\person", inversedBy="ChildrenFromMother")
     * @ORM\JoinColumn(name="person_id2", referencedColumnName="id", nullable=true)
     */
    private $Mother;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->partners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partners2 = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ChildrenFromFather = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ChildrenFromMother = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set fname
     *
     * @param string $fname
     * @return person
     */
    public function setFname($fname)
    {
        $this->fname = $fname;
    
        return $this;
    }

    /**
     * Get fname
     *
     * @return string 
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set lname
     *
     * @param string $lname
     * @return person
     */
    public function setLname($lname)
    {
        $this->lname = $lname;
    
        return $this;
    }

    /**
     * Get lname
     *
     * @return string 
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     * @return person
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set deathday
     *
     * @param string $deathday
     * @return person
     */
    public function setDeathday($deathday)
    {
        $this->deathday = $deathday;
    
        return $this;
    }

    /**
     * Get deathday
     *
     * @return string
     */
    public function getDeathday()
    {
        return $this->deathday;
    }

    /**
     * Set nationalcode
     *
     * @param string $nationalcode
     * @return person
     */
    public function setNationalcode($nationalcode)
    {
        $this->nationalcode = $nationalcode;
    
        return $this;
    }

    /**
     * Get nationalcode
     *
     * @return string 
     */
    public function getNationalcode()
    {
        return $this->nationalcode;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Add partners
     *
     * @param \Family\TreeBundle\Entity\partner $partners
     * @return person
     */
    public function addPartner(\Family\TreeBundle\Entity\partner $partners)
    {
        $this->partners[] = $partners;
    
        return $this;
    }

    /**
     * Remove partners
     *
     * @param \Family\TreeBundle\Entity\partner $partners
     */
    public function removePartner(\Family\TreeBundle\Entity\partner $partners)
    {
        $this->partners->removeElement($partners);
    }

    /**
     * Get partners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * Add partners2
     *
     * @param \Family\TreeBundle\Entity\partner $partners2
     * @return person
     */
    public function addPartners2(\Family\TreeBundle\Entity\partner $partners2)
    {
        $this->partners2[] = $partners2;
    
        return $this;
    }

    /**
     * Remove partners2
     *
     * @param \Family\TreeBundle\Entity\partner $partners2
     */
    public function removePartners2(\Family\TreeBundle\Entity\partner $partners2)
    {
        $this->partners2->removeElement($partners2);
    }

    /**
     * Get partners2
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartners2()
    {
        return $this->partners2;
    }

    /**
     * Add ChildrenFromFather
     *
     * @param \Family\TreeBundle\Entity\person $childrenFromFather
     * @return person
     */
    public function addChildrenFromFather(\Family\TreeBundle\Entity\person $childrenFromFather)
    {
        $this->ChildrenFromFather[] = $childrenFromFather;
    
        return $this;
    }

    /**
     * Remove ChildrenFromFather
     *
     * @param \Family\TreeBundle\Entity\person $childrenFromFather
     */
    public function removeChildrenFromFather(\Family\TreeBundle\Entity\person $childrenFromFather)
    {
        $this->ChildrenFromFather->removeElement($childrenFromFather);
    }

    /**
     * Get ChildrenFromFather
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenFromFather()
    {
        return $this->ChildrenFromFather;
    }

    /**
     * Add ChildrenFromMother
     *
     * @param \Family\TreeBundle\Entity\person $childrenFromMother
     * @return person
     */
    public function addChildrenFromMother(\Family\TreeBundle\Entity\person $childrenFromMother)
    {
        $this->ChildrenFromMother[] = $childrenFromMother;
    
        return $this;
    }

    /**
     * Remove ChildrenFromMother
     *
     * @param \Family\TreeBundle\Entity\person $childrenFromMother
     */
    public function removeChildrenFromMother(\Family\TreeBundle\Entity\person $childrenFromMother)
    {
        $this->ChildrenFromMother->removeElement($childrenFromMother);
    }

    /**
     * Get ChildrenFromMother
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenFromMother()
    {
        return $this->ChildrenFromMother;
    }

    /**
     * Set Father
     *
     * @param \Family\TreeBundle\Entity\person $father
     * @return person
     */
    public function setFather(\Family\TreeBundle\Entity\person $father=null)
    {
        $this->Father = $father;
    
        return $this;
    }

    /**
     * Get Father
     *
     * @return \Family\TreeBundle\Entity\person 
     */
    public function getFather()
    {
        return $this->Father;
    }

    /**
     * Set Mother
     *
     * @param \Family\TreeBundle\Entity\person $mother
     * @return person
     */
    public function setMother(\Family\TreeBundle\Entity\person $mother=null)
    {
        $this->Mother = $mother;

        return $this;
    }

    /**
     * Get Mother
     *
     * @return \Family\TreeBundle\Entity\person
     */
    public function getMother()
    {
        return $this->Mother;
    }

    /**
     * Set permission
     *
     * @param string $permission
     * @return person
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    
        return $this;
    }

    /**
     * Get permission
     *
     * @return string 
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return string
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->fileName
            ? null
            : $this->getUploadRootDir().'/'.$this->getId().'.'.$this->fileName;
    }

    public function getWebPath()
    {
        return null === $this->fileName
            ? null
            : $this->getUploadDir().'/'.$this->getId().'.'.$this->fileName;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to

        $this->fileName = $this->getFile()->getClientOriginalExtension();

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getId().'.'.$this->fileName
        );

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return person
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}