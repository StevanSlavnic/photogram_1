<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 12.8.16.
 * Time: 15.34
 */

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder->add('username', array(
//            'required' => true
//        ))
    }
}