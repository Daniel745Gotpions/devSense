<?php

namespace UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * hobbies
 *
 * @ORM\Table(name="hobbies")
 * @ORM\Entity(repositoryClass="UsersBundle\Repository\hobbiesRepository")
 */
class hobbies
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hobby", type="string", length=1120)
     */
    private $hobby;

    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hobby
     *
     * @param string $hobby
     *
     * @return hobbies
     */
    public function setHobby($hobby)
    {
        $this->hobby = $hobby;

        return $this;
    }

    /**
     * Get hobby
     *
     * @return string
     */
    public function getHobby()
    {
        return $this->hobby;
    }
}

