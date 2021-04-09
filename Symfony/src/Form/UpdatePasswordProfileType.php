<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdatePasswordProfileType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => $this->translator->trans('Please enter a password'),
                        ]),
                        new Assert\Length([
                            'min' => 6,
                            'minMessage' => $this->translator->trans('Your password should be at least {{ limit }} characters'),
                            'max' => 255,
                            'maxMessage' => $this->translator->trans('Your password cannot be more than {{ limit }} characters long.')
                        ]),
                    ],
                    'label' => false
                ],
                'second_options' => [
                    'label' => false
                ],
                'invalid_message' => $this->translator->trans('The password fields must match.'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
