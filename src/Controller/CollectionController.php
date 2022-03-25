<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Collection;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
/* cette classe permet de gerer les collections des livres */ 
class CollectionController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CollectionRepository
     */
    private $collectionRepository;
    public function __construct(CollectionRepository $collectionRepository,
     EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->collectionRepository = $collectionRepository;
        
    }
    
/* methode permet de renovyer la liste de collection existants dans la BD */
    public function getAllCollections()
    {
        /* renvouyer la liste de collection a travers la methode findAll() */
        $collections = $this->collectionRepository->findAll();
        /* renvoyer le vue avec la liste pour l afficher */
        return $this->render('collection/index.html.twig', [
            'collectionList' => $collections,
        ]);
    }

    /* cette methode permet d'ajouter une collection dans la table collection just id, le nom de la collection
    et le nbre de livre(optionnel) */
    public function addCollection(Request $request):JsonResponse
    {
        /* initialiser un objet collection et les attributs */
        $newCollection = new Collection();
        $newCollection->setNbbooks($request->query->get('nbBooks'));
        $newCollection->setName($request->query->get('name'));

       
        /* charger la l objet collection */ 
        $this->entityManager->persist($newCollection);
        /* executer la requte d'jout */
        $this->entityManager->flush();
        return $this->json($newCollection,201,[],[
            'skip_null_values' => true,
        ]);
    }
}
