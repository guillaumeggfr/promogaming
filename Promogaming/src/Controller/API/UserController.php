<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/api/users", name="api_users_")
 * 
 */
class UserController extends AbstractController
{
    private $hasher;
    private $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->hasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/getdata", name="getdata", methods={"POST"})
     * 
     */
    public function getUserData(UserInterface $user): Response
    {
        //If method didnt find user, return error
        if (empty($user)) {
            return $this->json("L'utilisateur n'existe pas", Response::HTTP_UNAUTHORIZED, [], []);
        } else {
            // We send User object containing his relation with tag
            //I return object User and status code
            return $this->json($user, Response::HTTP_OK, [], [
                'groups' => ['api_user_login'],
            ]);
        }
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * 
     */
    public function create(Request $request, TagRepository $repository): Response
    {
        $errors = [];
        // We instantiate the User object to put inside informations needed to create user
        $user = new User();

        $entityManager = $this->getDoctrine()->getManager();
        // I check if i get headers then i get content
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
        }

        // I check if username already exist
        if ($this->userRepository->findOneByUsername($data['username'])) {
            //return error
            array_push($errors, "Le pseudo existe déjà, veuillez en choisir un autre");
        } else {
            $user->setUsername($data['username']);
            $entityManager->persist($user);
        }

        // I check if email already exist
        if ($this->userRepository->findOneByEmail($data['email'])) {
            //return error
            array_push($errors, "L'email existe déjà, veuillez en choisir un autre");
        } else {
            $user->setEmail($data['email']);
            $entityManager->persist($user);
        }

        // Check password
        if (!isset($data['password'])) {
            array_push($errors, "Veuillez choisir un mot de passe");
        } else {
            if (strlen($data['password']) >= 3) {
                $user->setPassword($this->hasher->hashPassword($user, $data['password']));
                $entityManager->persist($user);
            } else {
                array_push($errors, "Votre mot de passe doit faire au minimum 3 caractères");
            }
        }


        $user->setRoles($user->getRoles());
        $user->setCreatedAt(new \DateTime());
        $entityManager->persist($user);
        //Tag insertion
        $i = 0;
        if (isset($data['tags'])) {
            foreach ($data['tags'] as $tags){
                if ($tags['isChecked'] !== false){
                    $i++;
                }
            }
            if ($i >= 2) {
                foreach ($data['tags'] as $tags) {
                    if ($tags['isChecked'] !== false) {
                        $tagClass = $repository->find($tags['id']);
                        if ($tagClass !== null) {
                            $user->addTag($tagClass);
                            $entityManager->persist($user);
                        }
                    }
                }
            } else {
                array_push($errors, "Veuillez choisir au minimum 2 tags");
            }
        }

        if (count($errors) < 1) {

            $entityManager->flush();
            // We send User object containing his relation with tag
            //I return object User and status code
            return $this->json([$user], Response::HTTP_CREATED, [], [
                'groups' => ['api_user_login'],
            ]);
        } else {
            return $this->json([$errors], Response::HTTP_CREATED, [], []);
        }
    }


    /**
     * @Route("/edit", name="edit", methods={"PATCH"})
     *
     */
    public function update(Request $request, TagRepository $repository): Response
    {
        // I check if i get headers then i get content

        $data = json_decode($request->getContent(), true);


        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $errors = [];

        //Checking if name already exist in DB and is different than actual
        if (isset($data['username']) && !empty(($data['username']))) {
            if ($user->getUsername() !== $data['username']) {
                if ($this->userRepository->findOneByUsername($data['username'])) {
                    array_push($errors, ["Le pseudo existe déjà, veuillez en choisir un autre"]);
                } else {
                    $user->setUsername($data['username']);
                    $entityManager->persist($user);
                }
            }
        }
        //Checking if email already exist in DB and is different than actual
        if (isset($data['email']) && !empty($data['email'])) {
            if ($user->getEmail() !== $data['email']) {
                if ($this->userRepository->findOneBy(['email' => $data['email']])) {
                    array_push($errors, ["L'email existe déjà, veuillez en choisir un autre"]);
                } else {
                    $user->setEmail($data['email']);
                    $entityManager->persist($user);
                }
            }
        }
        //Checking verifications for password
        if (isset($data['password']) && !empty($data['password'])) {
            if (!empty($data['password_repeat'])) {
                if (!empty($data['password_old']) && $this->hasher->isPasswordValid($user, $data['password_old'])) {
                    if ($data['password'] === $data['password_repeat']) {
                        $user->setPassword($this->hasher->hashPassword($user, $data['password']));
                        $entityManager->persist($user);
                    } else {
                        array_push($errors, ["Le mot de passe ne correspond pas au mot de passe répété"]);
                    }
                } else {
                    array_push($errors, ["L'ancien mot de passe ne correspond pas."]);
                }
            } else {
                array_push($errors, ["Vous devez rentrer deux fois votre nouveau mot de passe pour le valider"]);
            }
        }


        //if user update tags
        if (isset($data['tags'])) {
            //we list tags
            foreach ($data['tags'] as $tags) {
                // if there is tag checked, we add them in database, if they already exist, it will ignore
                if ($tags['isChecked'] == true) {
                    $tagClass = $repository->find($tags['id']);
                    if ($tagClass !== null) {
                        $user->addTag($tagClass);
                        $entityManager->persist($user);
                    }
                }
                if ($tags['isChecked'] == false) {
                    // If tags are not checked, we remove them
                    $tag = $repository->findOneBySomeField($tags['id']);

                    if ($tag !== null) {
                        if (count($user->getTags()) >= 2) {
                            $user->removeTag($tag);
                            $entityManager->persist($user);
                        } else {
                            array_push($errors, ["Le tag " . $tag->getName() . " n'a pas pu être supprimé. Vous devez en garder au minimum un"]);
                        }
                    }
                }
            }
        }

        $entityManager->flush();
        // We send User object containing his relation with tag
        // I return object User and status code
        return $this->json([$user, $errors], Response::HTTP_CREATED, [], [
            'groups' => ['api_user_login'],
        ]);
        //}
    }

    /**
     * @Route("/deleteaccount", name="userDeleteaccount", methods={"DELETE"})
     *
     */
    public function deleteAccount(UserInterface $user): Response
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);

        $em->flush();

        return $this->json('Votre compte a bien été supprimé', Response::HTTP_OK,);
    }
}
