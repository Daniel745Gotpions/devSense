<?php

namespace UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UsersBundle\Entity\hobbies;
use UsersBundle\Entity\friends;
use Symfony\Component\VarDumper\VarDumper;
/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UsersBundle\Repository\UsersRepository")
 */
class Users
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
     * @ORM\Column(name="userName", type="string", length=50)
     */
    private $userName;

     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=50)
     */
    private $password;

    /**
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    protected $birthday;

    protected $conn;
    
    public function getConn(){
        return $this->conn;
    }

    public function setConn($conn){
        $this->conn = $conn;

        return $this;
    }

    function __construct($conn = null) {
        $this->conn = $conn;
    }

    public function getAllUsers(){

        if( is_null($this->id) || empty($this->id))
            return [];

        $query = "SELECT * FROM users WHERE id != ".$this->id;
        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    public function getHobbies(){
        if( is_null($this->id) || empty($this->id))
            return [];

        $query = "SELECT * FROM hobbies WHERE userId = ".$this->id;
        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    public function addFriend($friendId){
        if( is_null($this->id) || empty($this->id))
            return false;
        $em = $this->getConn();
        $newFriend = new friends();
        $newFriend->setFriendId($friendId);
        $newFriend->setMyId($this->id);
        $em->persist($newFriend);
        $em->flush();
        return true;
    }

    /**
     * Get the password of the user.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Set the value of password.
     * @param string $password
     * @return \Entity\User
     */
    public function setPassword($password)
    {
        $this->password = strtolower($password);

        return $this;
    }


    /**
     * Get the userName of the user.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->password;
    }
    /**
     * Set the value of userName.
     * @param string $userName
     * @return \Entity\User
     */
    public function setUserName($userName)
    {
        $this->userName = strtolower($userName);

        return $this;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

     /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the name of the user.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Set the value of firstName.
     * @param string $name
     * @return \Entity\User
     */
    public function setName($firstName)
    {
        $this->setName = ucwords(strtolower($firstName));

        return $this;
    }
}

