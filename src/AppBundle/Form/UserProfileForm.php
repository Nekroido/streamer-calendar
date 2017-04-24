<?php
/**
 * Created by PhpStorm.
 * User: nekro
 * Date: 22-Jan-17
 * Time: 15:15
 */

namespace AppBundle\Form;


use Presta\ImageBundle\Form\Type\ImageType;
use Presta\ImageBundle\Model\AspectRatio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarFile', ImageType::class, [
                'required' => false,
                'max_width' => 600,
                'max_height' => 600,
                'aspect_ratios' => [
                    new AspectRatio(1, '1:1', true)
                ]
            ])
            ->add('pseudonyms', TextType::class, [
                'required' => false
            ])
            ->add('likes', TextType::class, [
                'required' => false
            ])
            ->add('preferredPlatforms', TextType::class, [
                'required' => false
            ])
            ->add('about', TextareaType::class, [
                'required' => false
            ])
            ->add('motto', TextType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\UserProfileType',
        ]);
    }
}