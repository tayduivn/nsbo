<?php

namespace AppBundle\Form;

use AppBundle\Entity\ReportingHeading;
use AppBundle\Repository\InterestCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\MapHeading;
use AppBundle\Form\ImageType;

class ReportingHeadingType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', TextType::class)
            ->add('enabled', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('objects', CollectionType::class, array(
                'entry_type' => ReportingObjectHeadingType::class,
                'by_reference' => true,
                'allow_add' => true,

            ))
            ->add('save', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReportingHeading::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_mapheading';
    }
}
