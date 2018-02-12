<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

//just first days playground, pure CRUD operations with Symfony
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * test list action for products
     */
    public function indexAction(Request $request)
    {
        $page = $request->get('page', 1);
        $ipp = 10;

        /**
         * @var Doctrine\ORM\QueryBuilder $builder
         *
         */
        $query = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')->getQuery();
        $query->setFirstResult($ipp * ($page - 1));
        $query->setMaxResults($ipp);

        $paginator = new Paginator($query);

        $totalItems = $paginator->count();
        $pages = ceil($totalItems / $ipp);

        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $productsEmpty = empty($products);


        if ($request->isMethod('post')) {
            // if there is ajax request - return just data without full template
            $html = $this->renderView('paginator_page.html.twig', ['paginator' => $paginator->getIterator()]);
            return new JsonResponse(['html' => $html]);
        }


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'currentPage' => $page,
            'pages' => $pages,
            'paginator' => $paginator->getIterator(),
            'productsEmpty' => $productsEmpty,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }


    /**
     * @Route("/view-product/{id}", name="view_product", requirements={"id"="\d+"})
     *
     * test view action for my first entities
     */
    public function viewAction(Request $request)
    {
        $id = $request->get('id', 0);
        if ($request->isMethod('post')) {
            $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
            $html = $this->renderView('default/view.html.twig', ['product' => $product]);
            return new JsonResponse(['status' => true, 'html' => $html]);
        }
        return new JsonResponse(['status' => false]);
    }
}
