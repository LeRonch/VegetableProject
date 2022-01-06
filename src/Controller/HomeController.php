<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Services\CartManager;
use App\Services\CartStorage;
use App\Service\CallApiService;
use Container4kqxjHE\getUserRepositoryService;
use App\Form\AddToCartType;
use App\Repository\BuyerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $productRepository, ClientRepository $clientRepository): Response
    {
        $client = $clientRepository->findOneBy(['user'=>$this->getUser()]);
        
        if ($client !== null && $client->getUser()->getSeller() == true){
            $products = $productRepository->findAllYours($client);
        }else{
            $products = $productRepository->findAll();
        }
      
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
        ]);

    }
}
