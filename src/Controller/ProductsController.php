<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

/**
 * Controller for handling products
 */
class ProductsController extends AbstractController
{

    /**
     * @Route("/products", methods={"GET"}, name="products")
     *
     * @param ProductRepository $repo Database Product repository
     *
     * @return Response
     */
    public function get_products(ProductRepository $repo): Response
    {
        $bikes = $repo->findBy([]);

        return $this->render('products.html.twig', [
            'bikes' => $bikes
        ]);
    }

    /**
     * @Route("/product/{id}", methods={"GET","POST"}, name="product")
     *
     * @param string            $id      Product Primary Key
     * @param Request           $request Request class
     * @param ProductRepository $repo    Database Product repository
     * @param SessionInterface  $session Session storage entitiy
     *
     * @return Response
     */
    public function get_product($id, Request $request, ProductRepository $repo, SessionInterface $session): Response
    {
        $bike = $repo->find($id);
        if ($bike === NULL)
        {
            throw $this->createNotFoundException('Product does not exist.');
        }

        $basket = $session->get('basket', []);
        
        if($request->isMethod('POST'))
        {
            $basket[$bike->getId()] = $bike;
            $session->set('basket', $basket);
        }

        $is_in_basket = array_key_exists($bike->getId(), $basket);

        return $this->render('product.html.twig', [
            'bike' => $bike,
            'inBasket' => $is_in_basket
        ]);
    }

}
