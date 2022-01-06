<?php

namespace App\Controller;

use App\Entity\Buyer;
use App\Form\BuyerType;
use App\Repository\BuyerRepository;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/buyer")
 */
class BuyerController extends AbstractController
{
    /**
     * @Route("/", name="buyer_index", methods={"GET"})
     */
    public function index(BuyerRepository $buyerRepository): Response
    {
        return $this->render('buyer/index.html.twig', [
            'buyers' => $buyerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="buyer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $buyer = new Buyer();
        $form = $this->createForm(BuyerType::class, $buyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($buyer);
            $entityManager->flush();

            return $this->redirectToRoute('buyer_index');
        }

        return $this->render('buyer/new.html.twig', [
            'buyer' => $buyer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="buyer_show", methods={"GET"})
     */
    public function show(Buyer $buyer): Response
    {
        return $this->render('buyer/show.html.twig', [
            'buyer' => $buyer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="buyer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Buyer $buyer): Response
    {
        $form = $this->createForm(BuyerType::class, $buyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($buyer->getName()           !== null &&
                $buyer->getSurname()        !== null &&
                $buyer->getNumber()         !== null)
            {
                $buyer->setCanBuy(true);
            }
            else {
                $buyer->setCanBuy(false);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($buyer);
            $entityManager->flush();

            return $this->redirectToRoute('buyer_show', ['id' => $buyer->getId()]);
        }

        return $this->render('buyer/edit.html.twig', [
            'buyer' => $buyer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="buyer_delete", methods={"POST"})
     */
    public function delete(Request $request, Buyer $buyer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$buyer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($buyer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('buyer_index');
    }
}
