<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 6/15/16
 * Time: 11:37 PM
 */

namespace AppBundle\Form;


use AppBundle\Entity\User;
use AppBundle\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;;

class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                'label' => 'First name'
            ))
            ->add('lastname', TextType::class, array(
                'label' => 'Last name'
            ))
//            ->add('slug', HiddenType::class, array());
        ;
//        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
//
//            $data = $event->getData();
//            $data['slug'] = $data['profile']['firstname'] . '-' . $data['profile']['lastname'];
//
//            $event->setData($data);
//
//
//        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Profile',
        ));
    }


    public function getName()
    {
        return 'app_user_profile_type';
    }
}