<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AamcMethodType
 * @package Ilios\CoreBundle\Form\Type
 */
class AamcMethodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('description', null, ['empty_data' => null])
            ->add('sessionTypes', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
        ;
        $builder->get('description')->addViewTransformer(new RemoveMarkupTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\AamcMethod'
        ));
    }
}
