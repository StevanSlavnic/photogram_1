<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProfileEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'constraints' => array(
//                    new NotBlank(array(
//                        "message" => "Please upload image"
//                    ))
                ),
            ))
            ->add('imageBackgroundFile', VichImageType::class, array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'constraints' => array(
//                    new NotBlank(array(
//                        "message" => "Please upload image"
//                    ))
                ),
            ))
            ->add('firstname', TextType::class, array(

                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('lastname', TextType::class, array(

                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('occupation', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                    ),

            ))
            ->add('about', TextareaType::class, array(

                'attr' => array(
                    'rows' => 2,
                    'class' => 'form-control'
                )
            ))

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Profile',
        ));
    }


}
