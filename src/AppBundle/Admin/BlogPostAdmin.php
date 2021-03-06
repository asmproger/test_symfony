<?php
/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 2/9/18
 * Time: 4:17 PM
 */

namespace AppBundle\Admin;


use AppBundle\Entity\BlogPost;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;

class BlogPostAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Post')
            ->with('Content', ['class' => 'col-md-3'])
            ->add('title', 'text')
            ->add('body', 'textarea')
            ->end()
            ->end()
            ->tab('Options')
            ->with('Meta', ['class' => 'col-md-3'])
            ->add('category', ModelType::class, [])
            ->end()
            ->with('Options', ['class' => 'col-md-3'])
            ->add('draft', 'checkbox', [
                'required' => false
            ])
            ->end()
            ->end();
        /*->add('category', 'sonata_type_model', [
                'class' => 'AppBundle\Entity\Category',
                //'choice_label' => 'name'
                'property' => 'name'
            ])*/
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('category', null, [], 'entity', [
                    'class' => 'AppBundle\Entity\Category',
                    'choice_label' => 'name'
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('category.name')
            ->add('draft');
    }

    public function toString($object)
    {
        return $object instanceof BlogPost ?
            $object->getTitle() : 'Blog Post';
    }
}