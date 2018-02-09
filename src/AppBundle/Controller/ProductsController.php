<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends Controller
{



    /**
     * @Route("/products", name="products_index")
     */
    public function indexAction(Request $request)
    {
        $isAdded = $request->get('added', 0);
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        if (!$products) {
            /*throw $this->createNotFoundException(
                'No product found for id'
            );*/
        }

        /*$em = $this->getDoctrine()->getManager();
        $product = new Product();
        $product->setDescription('Lorem ipsum');
        $product->setName('fish_text');
        $product->setPrice(128);
        $em->persist($product);
        $em->flush();
        $id = $product->getId();*/
        // replace this example code with whatever you need
        return $this->render('products/index.html.twig', [
            'isAdded' => $isAdded,
            'products' => $products,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/products/add", name="products_add")
     */
    public function addAction(Request $request)
    {
        $form = $this->getProductForm(new Product());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $product = $form->getData();

            $em->getConnection()->beginTransaction();
            try {
                $em->persist($product);
                $em->flush();
                $em->getConnection()->commit();

                return $this->redirectToRoute('products_index', ['added' => 1]);
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                throw new \Exception('Error while adding product');
            }
        }

        return $this->render('products/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/products/edit/{id}", name="products_edit", requirements={"id"="\d+"})
     */
    public function editAction($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $form = $this->getProductForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $em->persist($product);
                $em->flush();
                $em->getConnection()->commit();

                return $this->redirectToRoute('products_index', []);
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                throw new \Exception('Error while product editing');
            }
        }

        return $this->render('products/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/products/remove/{id}", name="products_remove", requirements={"id"="\d+"})
     */
    public function removeAction($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product) {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $em->remove($product);
                $em->flush();
                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rallback();
                throw new \Exception('Error while removing product');
            }

        }
        return $this->redirectToRoute('products_index', []);
    }

    private function getProductForm(Product $product)
    {
        $formBuilder = $this->createFormBuilder($product);
        $formBuilder
            ->add('name', TextType::class, [
                'label' => 'Name:',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:',
                'required' => false
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price:',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => $product->getId() ? 'Edit' : 'Add',
            ]);
        return $formBuilder->getForm();
    }
}
