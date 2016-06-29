<?php

namespace AppBundle\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileUsernameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                "label"       => "form.first_name",
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
                "attr"        => array(
                    "size" => 16,
                    'maxlength' => 30
                ),
                'constraints' => array(
                    new NotBlank(),
                    new Length(array(
                        'max' => 30,
                        'min' => 2,
                        'maxMessage' => 'fos_user.first_name.long'
                    ))
                )
            ))
            ->add('lastname', TextType::class, array(
                "label"       => "form.last_name",
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
                "attr"        => array(
                    "size" => 16,
                    'maxlength' => 30
                ),
                'constraints' => array(
                    new NotBlank(),
                    new Length(array(
                        'max'        => 30,
                        'min'        => 2,
                        'maxMessage' => 'fos_user.last_name.long'
                    ))
                )
            ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $data = $event->getData();

            $data['firstname'] = trim($data['firstname']);
            $data['lastname'] = trim($data['lastname']);

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Profile',
        ));
    }

    public function getName()
    {
        return 'app_bundle_profile_username_type';
    }
}
