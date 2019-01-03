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


        if( empty( $this->get('session')->get('userId'))  ){
            
            return $this->redirect('/');
        }
        
        $userId = (int) $this->get('session')->get('userId');
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UsersBundle:Users')->findOneById($userId);
        
        $user->setConn($em);

        $hobbies = [];
        $allUsers = [];
        
        return $this->render('UsersBundle:users:profile.html.twig',
            array(
                'user'=>$user,
                'hobbies'=> $user->getHobbies(),
                'allUsers'=>$user->getAllUsers()
            )
        );
        die('profile');
    }

    /**
     * @Route("/users/delete/{id}",name="delete_user")
     * @Method("GET")
     */
    public function userDeleteAction($id){
    	$id = (int) $id;
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('UsersBundle:Users')->findOneById($id);

    	if(is_null($user))
    		return $this->redirect($this->generateUrl('show_table', array('deleted' =>0)), 301);
    			
    	$em->remove($user);
    	$em->flush();
    	return $this->redirect($this->generateUrl('show_table', array('deleted' =>1)), 301);
    }
    
    /**
     * @Route("/users/edit/{id}",name="edit_user")
     * @Method("GET")
     */
    public function userEditAction($id){
    	$id = (int) $id;
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('UsersBundle:Users')->findOneById($id);

    	if(is_null($user)){
    		throw new NotFoundHttpException("Error Can't Find User");
    		die();
    	}
    	
    	$form = $this->createFormBuilder($user)
    		->setAction($this->generateUrl('user_upadte'))
    		->setMethod('POST')
    		->add('id', HiddenType::class)
    		->add('name', TextType::class)
    		->add('rotationPeriod', TextType::class)
    		->add('orbitalPeriod', TextType::class)
    		->add('diameter', TextType::class)
    		->add('climate', TextType::class)
    		->add('gravity', TextType::class)
    		->add('terrain', TextType::class)
    		->add('surfaceWater', TextType::class)
    		->add('population', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Update'))
            ->getForm();

        return $this->render('UsersBundle:Users:edit.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/users/update",name="user_upadte")
     */
    public function userUpdateAction(Request $request){
    	
    	$em = $this->getDoctrine()->getManager();
    	$data  = $request->request->all();

    	$data = $data['form'];
    	$userId = (int) $data['id'];

    	$user = $em->getRepository('UsersBundle:Users')->findOneById($userId);
    	if(is_null($user)){
    		throw new NotFoundHttpException("Error Can't Find User");
    		die();
    	}

    	$user->setName($data['name']);
        $user->setRotationPeriod($data['rotationPeriod']);
        $user->setOrbitalPeriod($data['orbitalPeriod']);
        $user->setDiameter($data['diameter']);
        $user->setClimate($data['climate']);
        $user->setGravity($data['gravity']);
        $user->setTerrain($data['terrain']);
        $user->setSurfaceWater($data['surfaceWater']);
        $user->setPopulation($data['population']);
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('show_table'));
            	
    	//return $this->redirect($this->generateUrl('show_table', array('deleted' =>1)), 301);
    }

}
