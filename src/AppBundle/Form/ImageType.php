<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;
use AppBundle\Entity\File;

class ImageType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', VichImageType::class, array(
            'constraints' => array(
                new Image(
                    array(
                    'mimeTypes' => array(
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'image/gif',
                        'image/svg+xml'
                    ))
                ),
            ),
            'label' => false,
            'required' => false,
            'allow_delete' => false,
            'download_link' => false,
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => File::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_image';
    }
}
