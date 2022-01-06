<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Form\AddToCartType;
use App\Form\ProductType;
use App\Form\SearchType;
use App\Manager\CartManager;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET", "POST"})
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dataProduit = $form->getData()['produit'];
            $dataType = $form->getData()['type'];
            
            if (strlen($dataProduit) > 0 && $dataType == 'Catégorie') {
                $productName = '%'.$dataProduit.'%';
                $products = $productRepository->searchByName($productName);
            }else if(is_bool($dataType)){
                $products = $productRepository->searchByType($dataType);
            }else{
                $products = $productRepository->findAll();
            };
                
            return $this->render('product/index.html.twig', [
                'products' => $products,
                'form' => $form->createView(),
            ]);

        }else{

            return $this->render('product/index.html.twig', [
                'products' => $productRepository->findAll(),
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/new/{id}", name="product_new", methods={"GET","POST"})
     */
    public function new(Client $client ,Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        //dd($request);
        $product->setClient($client);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('client_show', ['id' => $product->getClient()->getId()]);

        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET","POST"})
     */
    public function show(Product $product, Request $request, CartManager $cartManager): Response
    {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            
            if ($cart->getBuyer() == null) {
                $cart->setBuyer($this->getUser()->getBuyer());
            }

            $cart->addItem($item)->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/show.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $id = $this->getUser()->getClient()->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_show', ['id' => $id]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        $id = $product->getClient()->getId();

        return $this->redirectToRoute('client_show', ['id' => $id]);
    }
}