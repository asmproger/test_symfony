<?php

namespace AppBundle\Controller;


use AppBundle\Service\CustomUploader;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// first admin (before SonataAdminBundle )
class ProductsController extends Controller
{


    /**
     * @Route("/products", name="products_index")
     *
     * products list for admin
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
     *
     * adding of new product
     */
    public function addAction(Request $request, CustomUploader $cU)
    {
        $newProduct = new Product();
        $form = $this->getProductForm($newProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile $file
             */
            // gilr uploading with custom service
            $file = $newProduct->getPic();
            $newName = $cU->upload($file);
            /*$newName = md5(time()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('images_directory'),
                $newName
            );*/

            $em = $this->getDoctrine()->getManager();
            $product = $form->getData();
            $product->setPic($newName);

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
     *
     * product editing
     */
    public function editAction($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $form = $this->getProductForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // file processed without services
            $file = $product->getPic();
            $newName = md5(time()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $newName);
            $product = $form->getData();
            $product->setPic($newName);
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

    // here we can get product form for creation or editing
    // it will check, if product empty or no, and get form
    private function getProductForm(Product $product)
    {
        $formBuilder = $this->createFormBuilder($product);
        $formBuilder
            ->add('name', TextType::class, [
                'label' => 'Name:',
                'required' => 1
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:',
                'required' => 1
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price:',
                'required' => 1
            ])
            ->add('pic', FileType::class, [
                'label' => 'Image:',
                'required' => 1,
                'data_class' => null
            ])
            ->add('submit', SubmitType::class, [
                'label' => $product->getId() ? 'Edit' : 'Add',
            ]);
        return $formBuilder->getForm();
    }
}
