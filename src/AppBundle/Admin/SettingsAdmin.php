<?php
/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 2/9/18
 * Time: 4:17 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Setting;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SettingsAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('skey', 'text')
            ->add('value', 'text')
            ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('skey')
            ->add('value')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('skey');
    }
    public function toString($obj) {
        if(is_object($obj) && $obj instanceof Setting) {
            $obj->getSkey();
        } else {
            return 'Setting';
        }
    }
}