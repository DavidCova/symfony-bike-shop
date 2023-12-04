<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Order;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

/**
 * Controller for handling Checkouts
 */
class CheckoutController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}
    /**
     * @Route("/checkout", methods={"GET","POST"}, name="checkout")
     *
     * @param string            $id      Product Primary Key
     * @param Request           $request Request class
     * @param ProductRepository $repo    Database Product repository
     * @param SessionInterface  $session Session storage entitiy
     *
     * @return Response
     */
    public function checkout(Request $request, ProductRepository $repo, SessionInterface $session, MailerInterface $mailer): Response
    {
        $basket = $session->get('basket', []);
        $total = array_sum(array_map(function($product) {return $product->getPrice();}, $basket ));

        $order = new Order;

        $form = $this->createFormBuilder($order)
                     ->add('name', TextType::class)
                     ->add('email', TextType::class)
                     ->add('address', TextareaType::class)
                     ->add('save', SubmitType::class, ['label' => 'Confirm order'])
                     ->getForm();
                     $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $order = $form->getData();

            foreach ($basket as $product)
            {
                $order->getProducts()->add($repo->find($product->getId()));

            }
       
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($order);
            try {
                $entityManager->flush();
            } catch (\Exception $e) {
                dump($e);
                echo $e->getMessage();
            }

            $this->send_email_confirmation($order, $mailer);

            return $this->render('confirmation.html.twig');
        }
        else
        {
            return $this->render('checkout.html.twig', [
                'total' => $total,
                'form'  => $form->createView()
            ]);
        }

        
    }

    private function send_email_confirmation(Order $order, MailerInterface $mailer)
    {
        $email = (new TemplatedEmail())
                  ->from('zakarum-mage@hotmail.com')
                  ->to(new Address($order->getEmail(),$order->getName()))
                  ->subject('Order confirmation')
                  ->htmlTemplate('emails/order.html.twig')
                  ->context(['order'=>$order]);

        $mailer->send($email);
    }

}
