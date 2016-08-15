<?php
// src/AppBundle/Form/UserType.php
namespace AppBundle\Form;

use AppBundle\Form\ProfileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\User;
use AppBundle\Entity\Profile;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("profile", ProfileType::class, array(
                'label' => ''
            ))

            ->add('email', EmailType::class, array(
                'label' => 'Enter email'

            ))
            ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                )
            )
            ->add('username', 'hidden', array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'security.login.email'
            ))
            ->add('fullName', 'hidden', array())
//            ->add('profileName', 'hidden');
        ;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {

            $data = $event->getData();

            $data['username'] = $data['profile']['firstname'] . ' ' . $data['profile']['lastname'];
            $data['fullName'] = $data['profile']['firstname'] . ' ' . $data['profile']['lastname'];
//            $data['profileName'] = $data['profile']['firstname'] . '-' . $data['profile']['lastname'];

            $event->setData($data);


        });

    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'app_user_type';
    }
}