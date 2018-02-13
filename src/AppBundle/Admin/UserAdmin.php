<?php
/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 2/9/18
 * Time: 4:17 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        //$collection->add('index', $this->getRouterIdParameter() . '/index');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text')
            ->add('email', 'text')
            ->add('age', 'number');

        if ($this->getSubject()->isNew()) {
            $formMapper->add('password', 'password', [
                'required' => false
            ]);
        }
        /*->add('category', 'sonata_type_model', [
                'class' => 'AppBundle\Entity\Category',
                //'choice_label' => 'name'
                'property' => 'name'
            ])*/
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('email')/*->add('category', null, [], 'entity', [
                    'class' => 'AppBundle\Entity\Category',
                    'choice_label' => 'name'
                ]
            )*/
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('email')
            ->add('age')
            /*->add('_action', null, [
                'actions' => [
                    'index' => [
                        'template' => '@App/admin/settings/list__action_index.html.twig'
                    ]
                ]
            ])*/
        ;
    }

    public function toString($object)
    {
        return $object instanceof User ?
            $object->getName() : 'User';
    }
}