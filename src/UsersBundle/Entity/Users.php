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

        // Get Friends 
        
        $res = $this->getFriends();             
        $frineds = [];
        foreach ($res AS $k => $val) {
            $frineds[] = $val['friendId'];
        }

        // Get all users        
        $query = "SELECT * FROM users WHERE id != ".$this->id;
        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        
        if( count($result) ){
            
            foreach ($result AS $key => $value) {
               $value['isFriend'] =  (in_array($value['id'],$frineds))? true:false ;
               $result[$key] =  $value;

            }
        }
        
        return $result;
    }

    public function getHobbies($userId=null,$sqlWhere = array()){
        if( is_null($this->id) || empty($this->id))
            return [];

        $userId = (!is_null($userId))? $userId:$this->id;
        $query = "SELECT * FROM hobbies 
                  WHERE userId = ".$userId;
        
        if(count($sqlWhere)){
            $query .=" AND ".implode(' AND', $sqlWhere);
            
        }


        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();

        return $result;
    }

    public function addFriend($friendId){
        if( is_null($this->id) || empty($this->id))
            return false;
        $em = $this->getConn();

        $friends = $this->getFriends();

        // Delete the first in first out friend
        if( count($friends) ==  5 ){
            $query = " DELETE FROM friends 
                       WHERE friendId =".$friends[0]['friendId']." AND myId=".$this->id;
            $statement = $this->conn->getConnection()->prepare($query);
            $statement->execute();
        }

        $newFriend = new friends();
        $newFriend->setFriendId($friendId);
        $newFriend->setMyId($this->id);
        $newFriend->setDateCreated(new \DateTime('now'));
        $em->persist($newFriend);
        $em->flush();
        return true;
    }

    public function getFriends($customId = null,$sqlWhere = array()){
        if( is_null($this->id) || empty($this->id))
            return [];
          // Get Friends 
        $userId = (!is_null($customId))? $customId:$this->id;
        $query = "SELECT friendId,users.name
                  FROM friends 
                  INNER JOIN users on users.id = friends.friendId
                  WHERE myId = ".$userId;
        if(count($sqlWhere))
            $query.= " AND ".implode(' AND ',$sqlWhere);

        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAll();             
    }

    public function getPotentialUser(){
        die('dfjnkn');
        $birthday = $this->getBirthday();
        $friends = [];
        $plus5Days = date('Y-m-d H:i:s',strtotime($birthday->format('Y-m-d').' + 5 days'));
        $minus5Days = date('Y-m-d H:i:s',strtotime($birthday->format('Y-m-d').' - 5 days'));
        $sqlWhere[] = " users.birthday >= '{$minus5Days}'"; 
        $sqlWhere[] = " users.birthday <= '{$plus5Days}'"; 
        $friends = $this->getFriends(null,$sqlWhere);

        if(count($friends)){
            
            $myHobbies = $this->getHobbies();
            if( !count($myHobbies)){

                echo "";
            }
            $tempHobbies = [];
            foreach ($myHobbies as $key => $value) {
                $tempHobbies[] = $value['hobby'];
            }

            $sqlWhere = [];
            for ($i=0;count($friends);$i++) {

                $sqlWhere[] = " hobbies.hobby in ( '".implode("','", $tempHobbies)."' )";
                
                
                $hobbies = $this->getHobbies($friends[$i]['friendId'],$sqlWhere);

                if( !count($hobbies) )
                    unset($friends[$i]);

                unset($sqlWhere[0]);
                
            }

        }
       
        return $friends;

    }

    public function getFriendsByNearBirth($id,&$childs){

        $minus2Week = date('Y-m-d H:i:s',strtotime('-2 weeks'));
        $plus2Week = date('Y-m-d H:i:s',strtotime('+2 weeks'));
        $sqlWhere[] = " users.birthday >= '{$minus2Week}'"; 
        $sqlWhere[] = " users.birthday <= '{$plus2Week}'"; 
        
        $childres = $this->getFriends($id,$sqlWhere);

        if(count($childres) > 0){
            
            foreach ($childres AS $key => $value) {

                $childs[] = array('id'=>$id,'childs'=>$childres);
            }
        }

        return $childs;

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

