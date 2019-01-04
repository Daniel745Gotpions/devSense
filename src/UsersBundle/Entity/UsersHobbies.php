<?php

namespace UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersHobbies
 *
 * @ORM\Table(name="usersHobbies")
 * @ORM\Entity(repositoryClass="UsersBundle\Repository\UsersHobbiesRepository")
 */
class UsersHobbies
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
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="hobbyId", type="integer")
     */
    private $hobbyId;


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
     * Set userId
     *
     * @param integer $userId
     *
     * @return UsersHobbies
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set hobbyId
     *
     * @param integer $hobbyId
     *
     * @return UsersHobbies
     */
    public function setHobbyId($hobbyId)
    {
        $this->hobbyId = $hobbyId;

        return $this;
    }

    /**
     * Get hobbyId
     *
     * @return int
     */
    public function getHobbyId()
    {
        return $this->hobbyId;
    }
}

