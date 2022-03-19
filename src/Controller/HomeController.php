<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use App\Entity\Collection;
use App\Entity\Author;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
/* cette classe permet de faire la gestion des fonctionalités de la page d'acceuil */
class HomeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var BookRepository
     */
    private $bookRepository;
    /**
     * @var CollectionRepository
     */
    private $collectionRepository;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager,
    CollectionRepository $collectionRepository,AuthorRepository $authorRepository)
    {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        $this->collectionRepository = $collectionRepository;
        $this->authorRepository = $authorRepository;
        
    }
    /* cette methode permet de rencoyer la vue index.home.twig */
     /**
     * @Route("/index", name="")
     */
    public function index()
    {
        /* ce variable permet de preciser le nombre de book et de collection a afficher 
        just le 3 premier de chaque resultat (book et collection) */
        $max=3;
        /* la liste de livres  */
        $books = $this->bookRepository->findAll();
        /* la liste collections */
        $collections = $this->collectionRepository->findAll(); 
        /* max est maximum de livres a afficher ,maxc nombre de collection a afficher */
        return $this->render('home/index.html.twig', ['bookList' => $books,
        'collectionList' => $collections,'max' => $max,'maxc' => $max,]);
    }
    /* cette methode permet de faire un rechercher dynamique dans la zone de recherche  */
    public function getSearch(Request $request)
    {
        /* verifier si la terme a chercher existe dans la table de book (si est un livre)  */
        $books = $this->bookRepository->findBy(['title'=> $request->query->get('search')]);
        /* si est le cas (un livre) le controlleur renvoi vue book accompagne la liste de resultat */
        if($books){
        return $this->render('book/index.html.twig', ['bookList'=>$books,'type'=>'search']);
        }
        /* si n'est pas un livre verifier si le terme a chercher est une collection ?  */
        $collections = $this->collectionRepository->findBy(['name'=> $request->query->get('search')]);
        /* si est une collection alors il renvoi la resultat de recuperé de la table collection */
        if($collections){
        return $this->render('collection/index.html.twig', ['collectionList'=>$collections,'type'=>'search']);
        }
        /* si n est pas une collection la recherche de fait au niveau des auteur */ 
        $authors = $this->authorRepository->findBy(['name'=> $request->query->get('search')]);
        /* si est un auteur il renvoi la vue de recherche de auteur avec la liste de resultat */
        if($authors){
            return $this->render('author/index.html.twig', ['authorList'=>$authors,'type'=>'search']);
            }
        

    }
}
