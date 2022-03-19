<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


/* l gestion de des utilisateurs ajout suppresion  */
class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    
    public function __construct(UserRepository $userRepository,
     EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
       
        
    }
    
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function getAllUsers():JsonResponse
    {
        $users = $this->userRepository->findAll();
        return $this->json($users,200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['__isInitialized__'],
        ]);
    }

    /* inserer un utlisateur dans la bd */
    public function addUser(Request $request)
    {
        /* prepation de l objet User */
        $newUser = new User();
        $newUser->setUsername($request->request->get('username'));
        $newUser->setEmail($request->request->get('email'));
        $newUser->setPassword($request->request->get('password'));
        //$newUser->setPassword($this->encoder->encodePassword($newUser, $request->request->get('password')));

       
        /* charger l objet  */
        $this->entityManager->persist($newUser);
        /* executer la requete */
        $this->entityManager->flush();
        /* apres l' ajout l utlisateur redirigé vers la vue de connexion pour faire un login au site */
        return $this->render('connexion/index.html.twig');
        
    }

    /* cette methode permet de supprimer un utilisateur */
    public function  deleteUser ($id): JsonResponse
    {
        /* chercher l user a supprimer a travers son id passé en argument de URL et le charge dans un
        objet  */
        $userRemove = $this->userRepository->find($id);


        $this->entityManager->remove($userRemove);
        /* executer la requete de suppression */
        $this->entityManager->flush();
        return $this->json($this->userRepository->findAll(), 200, [], [
            'skip_null_values' => true,
        ]);
    }

    /* cette methode de faire un simple login just de recuperer les infos de user et le renvoyer
    avec le vue */
    public function  login(Request $request)
    {
        
        $user = $this->userRepository->findBy(['username'=> $request->request->get('username')]);
        
            return $this->redirectToRoute('home',['user'=>$user]);
        
        
    }
}
