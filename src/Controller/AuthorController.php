<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends AbstractController
{
     /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    public function __construct(AuthorRepository $authorRepository,
     EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->authorRepository = $authorRepository;
        
    }
    
    //  ********  liste de tous des athors ******** ///
    public function getAllAuthors()
    {
        /* cette commande permet de rechercher dans la BD tous les auteurs 
        et renvoi un tableau contient tout les auteurs */
        $authors = $this->authorRepository->findAll();
        return $this->render('author/index.html.twig', ['authorList'=>$authors]);
    }

    /* ajouter un auteur cette methode n'a pas un lien dans la site,
     mais vous pourrez  ecriver manuellement le route ******* */

    public function addAuthor(Request $request):JsonResponse
    {
        $newAuthor = new Author();
        $newAuthor->setName($request->query->get('name'));
        $newAuthor->setDescription($request->query->get('description'));
       
        /* cette commande permet d'ajouter dans la BD l'objet newAuther ****** */
        $this->entityManager->persist($newAuthor);
        $this->entityManager->flush();
        /* ****** */
        return $this->json($newAuthor,201,[],[
            'skip_null_values' => true,
        ]);
    }


}
