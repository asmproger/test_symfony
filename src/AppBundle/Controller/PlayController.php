<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlayController extends Controller
{
    /**
     * @Route("/play", name="play_index")
     */
    public function indexAction(Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find(1);
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        if (!$product) {
            /*throw $this->createNotFoundException(
                'No product found for id'
            );*/
        }

        // replace this example code with whatever you need
        return $this->render('play/index.html.twig', [
            'product' => $product,
            'products' => $products,
            'some_var' => 'ok',
            'arr_var' => ['test1', 'test2'],
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }
}
