<?php

namespace UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;
use UsersBundle\Entity\Users;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UsersBundle\Form\UsersType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Session\Session;
use UsersBundle\Entity\blackList;

class UsersController extends Controller
{
    /**
     * @Route("/", name="")
     */
    public function indexAction(){
			
    	$em = $this->getDoctrine()->getManager();
    	$dataResult = [];
        
        if( !empty( $this->get('session')->get('userId'))  ){
            return $this->redirect('/profile');
        }

        return $this->render('UsersBundle:users:login.html.twig',
        	array()
        );
    }

    /**
     * @Route("/login-logic", name="loginLogic")
     */
    public function loginLogicAction(Request $request){
       
        $requiredFields = ['username','password'];
        $data = $request->request->all(); 
        $json = ['status'=>true,'message'=>''];
        $session = new Session();
        foreach ($requiredFields AS $key => $value) {
            
            if(empty($data[$value])){
                $json['message'] = $value.' Is REquired Field';
                $json['status'] = false;
                die(json_encode($json));
            }
        }

        $em = $this->getDoctrine()->getManager();

        if( !$this->container->get('session')->isStarted() ){
            $session->start();
            $session->set('loginFail', 0);
        }

        $query = "SELECT * FROM users WHERE username='".$data['username']."' and password= '".$data['password']."'";
        $statement = $em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        
        
        if( !$this->container->get('session')->isStarted() ){
            $session->start();
            $session->set('fail', 0);
        }

        if( !count($result)){
            $session->set('fail', $session->get('fail')+1);
            $json['message'] = 'Not Exist';
            $json['status'] = false;
            
            if( $session->get('fail') >= 5){
                // InsertLog
                $log = new blackList();
                $log->setIp($log->getUserIP());
                $log->setDateCreated(new \DateTime('now'));
                $em->persist($log);
                $em->flush();
                $session->set('fail', 0);       
            }  

        }else{
            
            $this->get('session')->set('userId', $result[0]['id']);
            $json['status'] = true;
        }
        

        die(json_encode($json));
        
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profileAction(Request $request){

        if( empty( $this->get('session')->get('userId') ) || is_null($this->get('session')->get('userId'))  ){
            echo "need to return to login page";
            //return $this->redirect('/');
        }
        
        $userId = (int) $this->get('session')->get('userId');
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UsersBundle:Users')->findOneById($userId);

        $user->setConn($em);

        $hobbies = $user->getHobbies();

        $allUsers = $user->getAllUsers();

        return $this->render('UsersBundle:users:profile.html.twig',
            array(
                'user'=>$user,
                'hobbies'=> $hobbies,
                'allUsers'=>$allUsers
            )
        );
    }

    /**
     * @Route("/get-my-Friends", name="get_my_Friends")
     */
    public function getMyFriendsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userId = (int) $this->get('session')->get('userId');
        $user = $em->getRepository('UsersBundle:Users')->findOneById($userId);

        $user->setConn($em);
        $friends = $user->getFriends();
        $html = '';
        if(count($friends)){
            $html.='<ul>';
            foreach ( $friends as $key => $value) {
                $html.='<li>'.$value['name'].'</li>';
            }
            $html.='</ul>';
        }

        die($html);   
    }

    /**
     * @Route("/get-birthday-pepole", name="get_birthday_pepole")
     */
    public function getBirthdayPepoleAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userId = (int) $this->get('session')->get('userId');
        $user = $em->getRepository('UsersBundle:Users')->findOneById($userId);
        $tree = [];
        $user->setConn($em);
        $user->getFriendsByNearBirth($userId,$tree);
        VarDumper::dump($tree);
        die('in');
        // $html = '';
        // if(count($friends)){
        //     $html.='<ul>';
        //     foreach ( $friends as $key => $value) {
        //         $html.='<li>'.$value['name'].'</li>';
        //     }
        //     $html.='</ul>';
        // }

        // die($html);   
    }

    /**
     * @Route("/show-potential", name="show_potential")
     */
    public function showPotentialAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $userId = (int) $this->get('session')->get('userId');
        $user = $em->getRepository('UsersBundle:Users')->findOneById($userId);
        $tree = [];
        $user->setConn($em);
        $friends =$user->getPotentialUser();
    }

    /**
     * @Route("/Add-friend", name="AddFriend")
     */
    public function AddFriendAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $requiredFields = ['friendId'];
        $data = $request->request->all(); 
        $json = ['status'=>true,'message'=>'','friendId'=>'','myUserId'=>''];
        foreach ($requiredFields AS $key => $value) {
            
            if(empty($data[$value])){
                $json['message'] = 'Missing Fields';
                $json['status'] = false;
                die(json_encode($json));
            }
        }

        $userId = (int) $this->get('session')->get('userId');
        $user = $em->getRepository('UsersBundle:Users')->findOneById($userId);
        $user->setConn($em);
        $answer = $user->AddFriend((int) $data['friendId']);
        $json['status'] = $answer;
        $json['friendId'] = (int) $data['friendId'];
        $json['myUserId'] = (int) $userId;
        
        die(json_encode($json));
    }
}
