<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for handling Baskets
 */
class BasketController extends AbstractController
{

    /**
     * @Route("/basket", methods={"GET","POST"}, name="basket")
     *
     * @param string            $id      Product Primary Key
     * @param Request           $request Request class
     * @param ProductRepository $repo    Database Product repository
     * @param SessionInterface  $session Session storage entitiy
     *
     * @return Response
     */
    public function get_basket(Request $request, SessionInterface $session): Response
    {
        $basket = $session->get('basket', []);
        
        if($request->isMethod('POST'))
        {
            unset($basket[$request->request->get('id')]);
            $session->set('basket', $basket);
        }

        $total = array_sum(array_map(function($product) {return $product->getPrice();}, $basket ));

        return $this->render('basket.html.twig', [
            'basketItems' => $basket,
            'total' => $total
        ]);
    }

}
