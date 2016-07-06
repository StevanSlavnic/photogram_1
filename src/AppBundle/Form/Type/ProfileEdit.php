<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;


class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, array(
                'required'      => true,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'constraints' => array(
                    new NotBlank(array(
                        "message" => "Upload profile image"
                    ))
                )
            ))
//            ->add('title', null, array(
//                'attr' => array('autofocus' => true),
//                'label' => 'label.title',
//            ))
//            ->add('summary', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array('label' => 'label.summary'))
            ->add('content', null, array(
                'attr' => array('rows' => 1),
                'label' => 'label.content',
            ))
            ->add('authorEmail', HiddenType::class, array(
                'label' => 'label.author_email',

            ))
            ->add('publishedAt', 'AppBundle\Form\Type\DateTimePickerType', array(
                'label' => 'label.published_at',
            ))
//            ->add('updatedAt', 'AppBundle\Form\Type\DateTimePickerType', array(
//                'label' => 'label.published_at',
//            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post',
        ));
    }
}
