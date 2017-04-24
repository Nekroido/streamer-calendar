<?php
/**
 * Date: 20-Aug-16
 * Time: 17:32
 */

namespace AppBundle\Form;

use AppBundle\Services\TokenService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class RegistrationForm
 * @package AppBundle\Form
 *
 * @Assert\Callback(methods={"isTokenValid"})
 */
class RegistrationForm extends AbstractType
{
    private $inviteOnly = false;
    /**
     * @var TokenService
     */
    private $tokenService;

    public function setInviteOnly($inviteOnly)
    {
        $this->inviteOnly = $inviteOnly;
    }

    public function setTokeService($tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('name', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'Register'
            ]);

        if ($this->inviteOnly) {
            $builder->add('token', TextType::class, [
                'constraints' => new Assert\Callback([$this, 'isTokenValid'])
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
        ]);
    }

    /**
     * @param $token
     * @param ExecutionContextInterface $context
     */
    public function isTokenValid($token, ExecutionContextInterface $context)
    {
        if ($this->inviteOnly) {
            $token = $this->tokenService->isValid($token);
            if ($token == null) {
                $context->buildViolation('Token is invalid')
                    ->atPath('token')
                    ->addViolation();
            }
        }
    }
}