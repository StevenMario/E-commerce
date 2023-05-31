<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Produit;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;



class ProductController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/nos_produit", name="app_product")
     */
    public function index(Request $request): Response
    {
        

        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Produit::class)->findWithSearch($search);
        }else{
            $products = $this->entityManager->getRepository(Produit::class)->findAll();
        }

        return $this->render('product/index.html.twig',[
            'products' => $products,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug): Response
    {

        $product = $this->entityManager->getRepository(Produit::class)->findOneBy((['slug' => $slug]));
        if(!$product) {
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/show.html.twig',[
            'product' => $product
        ]);
    }
}
