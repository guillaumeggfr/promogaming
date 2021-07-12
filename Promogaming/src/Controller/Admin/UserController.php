<?php

namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\EditType;
use App\Form\DashboardType;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class UserController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/admin/addUser", name="addUser")
     */
    public function addUser(Request $request, UserPasswordHasherInterface $hasher, TagRepository $tag): Response
    {  

        $this->requestStack->getSession();
        // I instantiate the use class
        $user = new User();  
        // I create the form object and I associate it with the user entity
        $form = $this->createForm(DashboardType::class, $user);
        // we associate the form with the request
        $form->handleRequest($request);
        // I check that my form is sent and if it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            
            // As the form is valid, we will encode the password then put it in $user
            $newPassword = $form->get('password')->getData();

            if ($newPassword != null) {
                $encodedPassword = $hasher->hashpassword($user, $newPassword);
                $user->setPassword($encodedPassword);
            }
            
            $entityManager = $this->getDoctrine()->getManager();

            // I retrieve the data from my role field of the form
            $userRole = $form->get('roles')->getData();
            // I check if it is empty
            if ($userRole == []) {
            // if this is the case I fill it with the USER role
                $user->setRoles(['ROLE_USER']);
            }
            
            // I will recover 3 tags to persist them when creating a user
            // I am using the findAll() method of my tagRepository
            $tags=$tag->findAll();
            for ($i =0;$i < 3;$i ++){
            // I loop over it in order to retrieve the first 3
            // I add them using the addTag() function of my user entity
                $user->addTag($tags[$i]);}

            $entityManager->persist($user);
            
            $entityManager->flush();
            
            return $this->redirectToRoute('admin_user_index');
        }
        
        return $this->render('admin/addUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
   /**
     * @Route("/admin/browse", name="admin_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response

    {
        $user = $this->getUser();
      
        $session= $this->requestStack->getSession('user', $user);
        $session->set('user', $user);
        
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    /**
     * @Route("/admin/show/{id}", name="ShowUser", methods={"GET"},requirements={"id"="\d+"})
     */
    public function show(User $user): Response
    {
        $this->requestStack->getSession();
        
        return $this->render('admin/ShowUser.html.twig', ['user' =>$user]);
    }


    /**
     * @Route("/admin/delete/{id}", name="admin_user_delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(User $user): Response
    {
        $this->requestStack->getSession('user');
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        
        $em->flush();

        return $this->redirectToRoute('admin_user_index');
    }

     /**
     * @Route("/admin/edit/{id}", name="user_edit", requirements={"id"="\d+"})
     */
    public function editUser(User $user,Request $request): Response
    {
        $this->requestStack->getSession();
    
    // I create the form object and I associate it with the user entity
     $form = $this->createForm(EditType::class, $user);
     
    // we associate the form with the request
     $form->handleRequest($request);
    // I check that my form is sent and if it is valid
     if ($form->isSubmitted() && $form->isValid()) {

    //I get the entity manager
         $this->getDoctrine()->getManager()->flush();
         $this->addFlash('success', 'User édité!');

         return $this->redirectToRoute('admin_user_index');
     }
        return $this->render('admin/editUser.html.twig', ['form' => $form->createView()]);
   
    }
}
