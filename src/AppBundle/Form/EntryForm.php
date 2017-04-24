<?php
/**
 * Date: 28-Aug-16
 * Time: 12:36
 */

namespace AppBundle\Form;

use AppBundle\Entity\StreamEntry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryForm extends AbstractType
{
    use ContainerAwareTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $statusOptions = [
            'Active' => StreamEntry::STATUS_ACTIVE,
            'Tentative' => StreamEntry::STATUS_TENTATIVE,
            'Cancelled' => StreamEntry::STATUS_CANCELLED,
        ];

        /*if ($options['data']->isApprovedStreamer) {
            $statusOptions['Approved'] = StreamEntry::STATUS_ACTIVE;
        } else {
            $statusOptions['Pending'] = StreamEntry::STATUS_PENDING;
        }*/

        $builder
            ->add('category', EntityType::class, array(
                'class' => 'AppBundle:Project',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.isActive = 1')
                        ->orderBy('p.title', 'ASC');
                },
                'choice_label' => 'title',
            ))
            ->add('title', TextType::class)
            ->add('dateStart', TextType::class, [
                'attr' => [
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd.mm.yyyy',
                    'data-date-today-btn' => 'linked',
                    'data-date-language' => 'ru',
                    'data-date-autoclose' => 'true',
                    'data-date-today-highlight' => 'true',
                ]
            ])
            ->add('dateEnd', TextType::class, [
                'attr' => [
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd.mm.yyyy',
                    'data-date-today-btn' => 'linked',
                    'data-date-language' => 'ru',
                    'data-date-autoclose' => 'true',
                    'data-date-today-highlight' => 'true',
                ]
            ])
            ->add('timeStart', TextType::class, [
                'attr' => [
                    'data-provide' => 'timepicker',
                    'data-template' => 'false',
                    'data-minute-step' => 15,
                    'data-show-meridian' => 'false',
                    'data-show-seconds' => 'false',
                    'data-snap-to-step' => 'true',
                ]
            ])
            ->add('timeEnd', TextType::class, [
                'attr' => [
                    'data-provide' => 'timepicker',
                    'data-template' => 'false',
                    'data-minute-step' => 15,
                    'data-show-meridian' => 'false',
                    'data-show-seconds' => 'false',
                    'data-snap-to-step' => 'true',
                ]
            ])
            ->add('allDay', CheckboxType::class, [
                'label' => 'Is all day event',
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-size' => 'mini',
                    'data-on' => 'Да',
                    'data-off' => 'Нет'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => $statusOptions
            ])
            ->add('comment', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);

        if ($options['data']->isApprovedStreamer) {
            $builder->add('isApproved', CheckboxType::class, [
                'label' => 'Is approved',
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-size' => 'mini',
                    'data-on' => 'Да',
                    'data-off' => 'Нет'
                ]
            ]);
        }
        else {
            $builder->add('isApproved', HiddenType::class, [
                'label' => 'Is approved',
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\EntryType',
        ]);
    }
}