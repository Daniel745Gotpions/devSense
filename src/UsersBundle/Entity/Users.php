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

    public function getUsersBetweenDates(){
        if( is_null($this->id) || empty($this->id))
            return [];
        $yearEnd = date('Y-m-d', strtotime('Dec 31'));
        $now = date('Y-m-d H:i:s');
        $query = "SELECT * FROM users WHERE id != ".$this->id." AND birthday BETWEEN '{$now}' AND '{$yearEnd} 00:00:00' ";
        
        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAll();

    }

    public function getHobbies($userId=null,$sqlWhere = array()){
        if( is_null($this->id) || empty($this->id))
            return [];

        $userId = (!is_null($userId))? $userId:$this->id;
        $query = "SELECT * FROM users 
                    INNER JOIN usersHobbies ON users.id = usersHobbies.userId
                    INNER JOIN hobbies ON usersHobbies.hobbyId = hobbies.ID
                   
                  WHERE usersHobbies.userId = ".$userId;
        
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
            return ['status'=>false,'deleted'=>0,'friendId'=>0];
        $em = $this->getConn();

        $friends = $this->getFriends();

        // Delete the first in first out friend
        if( count($friends) ==  5 ){
            $query = " DELETE FROM friends 
                       WHERE friendId =".$friends[0]['friendId']." AND myId=".$this->id;
            $statement = $this->conn->getConnection()->prepare($query);
            $statement->execute();
            $respond = ['status'=>true,'deleted'=>1,'friendId'=>$friends[0]['friendId']];
        }else{
            $respond = ['status'=>true,'deleted'=>0,'friendId'=>0];
        }

        $newFriend = new friends();
        $newFriend->setFriendId($friendId);
        $newFriend->setMyId($this->id);
        $newFriend->setDateCreated(new \DateTime('now'));
        $em->persist($newFriend);
        $em->flush();
        return $respond;
    }

    public function getFriends($customId = null,$sqlWhere = array()){
        if( is_null($this->id) || empty($this->id))
            return [];
          // Get Friends 
        $userId = (!is_null($customId))? $customId:$this->id;
        $query = "SELECT friendId,users.name,users.birthday
                  FROM friends 
                  INNER JOIN users on users.id = friends.friendId
                  WHERE myId = ".$userId;
        if(count($sqlWhere)){
            $query.= " AND ".implode(' AND ',$sqlWhere);
        }


        $statement = $this->conn->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAll();             
    }

    public function getPotentialUser(){
        
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

                echo "No hobbies";
            }

            $tempHobbies = [];
            foreach ($myHobbies AS $key => $value) {
                $tempHobbies[] = $value['hobbyId'];
            }

            $sqlWhere = [];

            if( count($tempHobbies)){
                
                for ($i=0;$i < count($friends);$i++) {


                    $sqlWhere[0] = " usersHobbies.hobbyId in ( '".implode("','", $tempHobbies)."' )";
                    
                    
                    $hobbies = $this->getHobbies($friends[$i]['friendId'],$sqlWhere);

                 
                    if( !count($hobbies) )
                        unset($friends[$i]);
                }
              
            }

        }
       
        return $friends;

    }   

    public function getFriendsByNearBirth($id,&$childs){
        $childs = [];
        //$minus2Week = date('Y-m-d H:i:s',strtotime('-2 weeks'));
        $plus2Week = date('Y-m-d H:i:s',strtotime('+2 weeks'));
       
        $sqlWhere[] = " users.birthday >= now()"; 
        $sqlWhere[] = " users.birthday <= '{$plus2Week}'"; 
        
        $childres = $this->getFriends($id,$sqlWhere);
        
        if( count($childres)){
            foreach ( $childres AS $key => $value) {
                
                $childs[$value['friendId']] = array('name'=>$value['name'],
                    'birthday'=>$value['birthday'],
                'userId'=>$value['friendId']);

                $tree = $this->getFriends($value['friendId'],$sqlWhere);

                if( count($tree)){
                    foreach ( $tree AS $k => $v) {
                        if( $id != $v['friendId']){
                            $childs[$v['friendId']] = array(
                                'name'=>$v['name'],
                                'birthday'=>$v['birthday'],
                                'userId'=>$v['friendId']);    
                        }            
                    }
                }
            }    
        }

        return $this->sortByDate($childs);
    }
    public function sortByDate( $childs = array() ){
        if(!count($childs))
            return $childs;
        
        // Sorting result
        foreach ($childs AS $key => $value) {
            $temp[] = $value;    
        }

        for($i=0; $i < count($temp); $i++){
            for($j=$i+1 ; $j < count($temp) ; $j++){
                
                if( strtotime($temp[$i]['birthday']) > strtotime($temp[$j]['birthday'])){
                        $temporary = $temp[$i];
                        $temp[$i] = $temp[$j];
                        $temp[$j] = $temporary ; 
                }
            }
        }
        
        $childs = $temp;
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

