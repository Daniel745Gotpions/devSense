<?php

namespace UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * friends
 *
 * @ORM\Table(name="friends")
 * @ORM\Entity(repositoryClass="UsersBundle\Repository\friendsRepository")
 */
class friends
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
     * @ORM\Column(name="myId", type="integer")
     */
    private $myId;

    /**
     * @var int
     *
     * @ORM\Column(name="friendId", type="integer")
     */
    private $friendId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

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
     * Set myId
     *
     * @param integer $myId
     *
     * @return friends
     */
    public function setMyId($myId)
    {
        $this->myId = $myId;

        return $this;
    }

    /**
     * Get myId
     *
     * @return int
     */
    public function getMyId()
    {
        return $this->myId;
    }

    /**
     * Set friendId
     *
     * @param integer $friendId
     *
     * @return friends
     */
    public function setFriendId($friendId)
    {
        $this->friendId = $friendId;

        return $this;
    }

    /**
     * Get friendId
     *
     * @return int
     */
    public function getFriendId()
    {
        return $this->friendId;
    }
    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return blackList
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}

