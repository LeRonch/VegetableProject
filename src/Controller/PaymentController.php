<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Order;
use App\Form\PaymentType;
use App\Manager\CartManager;
use App\Repository\PaymentRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment")
 */
class PaymentController extends AbstractController
{
    /**
     * @Route("/", name="payment_index", methods={"GET"})
     */
    public function index(PaymentRepository $paymentRepository): Response
    {
        return $this->render('payment/index.html.twig', [
            'payments' => $paymentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="payment_new", methods={"GET","POST"})
     */
    public function new(Request $request, CartManager $cartManager, Order $order): Response
    {
        $payment = new Payment();
        
        $cart = $cartManager->getCurrentCart();
        $price = $cart->getTotal();
        $formattedPrice = $price + $price * 0.2;
        $priceToPay = round($formattedPrice, 0);
        $truePrice = number_format($formattedPrice/100, 2);
        
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            \Stripe\Stripe::setApiKey('sk_test_51IuZkVBMWIqO6CChzlilZ0X8aqK7e3UVOtejEYp9eX1aPIvGepkn2nUR9Yel6c045J2hLp7PzybdzqoFW8eF3fMD00gReZkLd6');
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $priceToPay,
                'currency' => 'eur'
            ]);
            $entityManager = $this->getDoctrine()->getManager();

            $payment->setAmount($formattedPrice);
            $payment->setCreatedAt(new DateTime());
            $payment->setUpdatedAt(new DateTime());

            $payment->setNbOrder($order);
            $order->setStatus('Payé');
            $entityManager->persist($order);
            $entityManager->flush();
            
            $entityManager->persist($payment);
            $entityManager->flush();
            
            return $this->redirectToRoute('confirmation');
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'cart' => $cart,
            'truePrice' => $truePrice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_show", methods={"GET"})
     */
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

}
