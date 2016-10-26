<?php

namespace AppBundle\MessageBundle\Form;

use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Message form type for starting a new conversation
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormType extends AbstractType
{
//    private $tokenStorage;
//
//    public function __construct(TokenStorageInterface $tokenStorage)
//    {
//        $this->tokenStorage = $tokenStorage;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipient', 'search')
            ->add('subject', 'text', array(
                'label' => ' '
            ))
            ->add('body', 'textarea');

        // grab the user, do a quick sanity check that one exists
//        $user = $this->tokenStorage->getToken()->getUser();
//        if (!$user) {
//            throw new \LogicException(
//                'The FriendMessageFormType cannot be used without an authenticated user!'
//            );
//        }
//
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) use ($user) {
//                $form = $event->getForm();
//
//                $formOptions = array(
//                    'class' => 'AppBundle\Entity\User',
//                    'property' => 'userName',
//                    'query_builder' => function (UserRepository $er) use ($user) {
//                        // build a custom query
//                         return $er->createQueryBuilder('u')->addOrderBy('userName', 'DESC');
//
//                        // or call a method on your repository that returns the query builder
//                        // the $er is an instance of your UserRepository
//                        // return $er->createOrderByFullNameQueryBuilder();
//                    },
//                );
//
//                // create the field, this is similar the $builder->add()
//                // field name, field type, data, options
//                $form->add('follower', EntityType::class, $formOptions);
//            }
//        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'message',
        ));
    }

    public function getName()
    {
        return 'fos_message_new_thread';
    }


}
