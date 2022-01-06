<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Form\ClientType;
use App\Form\ProductType;
use App\Repository\BuyerRepository;
use App\Repository\ClientRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/", name="client_index", methods={"GET"})
     */
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_show", methods={"GET", "POST"})
     */
    public function show(Client $client, BuyerRepository $buyerRepository, OrderRepository $orderRepository, OrderItemRepository $orderItemRepository, ProductRepository $productRepository, Request $request): Response
    {
        $product = new Product();
        $productRepository->findAll();
        $orderRepository->findAll();
        $buyerRepository->findAll();
        $orderitems = $orderItemRepository->findAllYours($client->getId());
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $product->setClient($client);
        //dd($orderitems);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $price = $request->request->get('product')['price'];
            $formatPrice = $price * 100;
            $product->setPrice($formatPrice);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('client_show', ['id' => $client->getId()]);
        }

        return $this->render('client/show.html.twig', [
            'client' => $client,
            'products' => $productRepository->findBy(['client' => $client->getId()]),
            'product' => $product,
            'orderItems' => $orderitems,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($client->getName()          !== null &&
                $client->getAdress()        !== null &&
                $client->getCountry()       !== null &&
                $client->getPhone()         !== null &&
                $client->getDays()          !== null &&
                $client->getPostal()        !== null &&
                $client->getCity()          !== null)
            {
                $client->setSeller(true);
            } else {
                $client->setSeller(false);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('client_show', ['id' => $client->getId()]);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_delete", methods={"POST"})
     */
    public function delete(Request $request, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_index');
    }
    
}