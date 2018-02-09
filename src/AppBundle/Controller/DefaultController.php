<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $page = $request->get('page',1 );
        $ipp = 2;

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

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'currentPage' => $page,
            'pages' => $pages,
            'paginator' => $paginator->getIterator(),
            'productsEmpty' => $productsEmpty,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
