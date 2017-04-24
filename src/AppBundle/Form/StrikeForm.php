<?php
/**
 * Date: 03-Sep-16
 * Time: 17:34
 */

namespace AppBundle\Form;

use AppBundle\Entity\Strike;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StrikeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $severityOptions = [
            'Notice' => Strike::NOTICE,
            'Warning' => Strike::WARNING,
            'Dismissal' => Strike::DISMISSAL
        ];

        $builder
            ->add('streamer', EntityType::class, array(
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
            ))
            ->add('severity', ChoiceType::class, [
                'choices' => $severityOptions
            ])
            ->add('expires', DateTimeType::class, [
                'attr' => [
                    'class' => 'datetime-picker',
                ]
            ])
            ->add('reason', TextareaType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Strike',
        ]);
    }
}