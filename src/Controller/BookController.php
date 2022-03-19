<?php

namespace App\Controller;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/* c'est la  classe la plus important contient l element de base c'est le livre et contient tous les operation 
de gestion des livres*/
class BookController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var BookRepository
     */
    private $bookRepository;
    
    

   

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        
        
    }
    
    /* cette methode permet de renoyer la liste des tous les livres existants dans la BD */
    public function getAllBooks()
    {
        /* la methode de la FindAll implementé dans la classe bookRepository permet d'envoyer 
        une requete sql pour renvoyer un une liste de livre existant */
        $books = $this->bookRepository->findAll();       
        return $this->render('book/index.html.twig', ['bookList'=>$books,'type'=>'all']); 
    }

    
    /* cette methode permet de renvoyer la liste des livres pour une collection donneé envoyer 
    par l URL par la methode GET  
     */
    public function getCollectionBooks(Request $request)
    {
        /* la methode findBy de classe bookRepository permet de chercher une collection 
        par un critère donnée : ici le nom de la collection exemple أدب et renvoi un tableau contient 
        le resutltat de la requete sql */
        $books = $this->bookRepository->findBy(['collection'=> $request->query->get('collection')]);
        /* renvoyer la vue de de la collection de livre. le variable type just pour indiquer que la recherche
        de collection et pas auteurs par exp  */
        return $this->render('book/index.html.twig', ['bookList'=>$books,'type'=>'collection']);
    }
    /* cette methode permet de renvoyer la liste des livres d'un auteur passé en argument
    en appliquant le meme principe de la methode precedente */
    public function getAuthorBooks(Request $request)
    {     
        $books = $this->bookRepository->findBy(['author'=> $request->query->get('author')]);
        return $this->render('book/index.html.twig', ['bookList'=>$books,'type'=>'author']);
    }

   

   /* cette methode permet d'ajouter un livre dans la base de donné, l ajout d'un livre est accessible
   par postman pour le moment n existe pas un route dans le site permet d'ajouter */
    public function addBook(Request $request):JsonResponse
    {
        /* preparation d'un onjet Book  */
        $newBook = new Book();
        /*  ces instructions permet de affecter les valeur de la requete passe par la methode POST */
        $newBook->setTitle($request->request->get('title'));
        $newBook->setAuthor($request->request->get('author'));
        $newBook->setCollection($request->request->get('collection'));
        $newBook->setDescription($request->request->get('description'));
        $newBook->setNbpage($request->request->get('nbpage'));
        //$newBook->setDateexpd($request->request->get('dateexpd'));

        /* cette instruction permet d'extracter l url de l'image*/
        $imageFile = $request->files->get('image');

        /* le variable result c est d'affecter un nouveau nom a l'image qui constitue la date de depot */
        $result = new DateTime();
        $result=$result->format('Y-m-d-H-i-s');
        /* ce le nom en concatenant avec l extension */
        $newFilename=$result.'.'.$imageFile->guessExtension();
        /* cette instruction permet de poser l image dans le dossier book_image pour l avoir dispo 
        pour le vue */
        $imageFile->move(
            $this->getParameter('book_directory'),$newFilename
        );
        $imageurl=$newFilename;
        
        $newBook->setImage($imageurl);

        /* c est le meme principe pour le fichier pdf de la livre que l 'image */
        $pathFile = $request->files->get('path');
        $newFilename=$result.'.'.$pathFile->guessExtension();
        $pathFile->move(
            $this->getParameter('file_directory'),$newFilename
        );
        $fileurl=$newFilename;
        $newBook->setPath($fileurl);
 
    
        /* exectuer la requte qui insere le livre dans la base */
        $this->entityManager->persist($newBook);
        $this->entityManager->flush();
        return $this->json($newBook,201,[],[
            'skip_null_values' => true,
        ]);
    }

    /* permet de effacer un livre de la la BD */
    public function  deleteBook ($id): JsonResponse
    {
        $bookRemove = $this->bookRepository->find($id);
        /* executer une requete sql "delete"  */
        $this->entityManager->remove($bookRemove);
        $this->entityManager->flush();
        return $this->json($this->bookRepository->findAll(), 200, [], [
            'skip_null_values' => true,
        ]);
    }
    

   

}
