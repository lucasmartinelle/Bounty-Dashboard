<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddUserSettingsType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a username'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your username cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter an email address'),
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => $this->translator->trans('Your email address should have {{ limit }} characters or more.'),
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your email address cannot be more than {{ limit }} characters long.')
                    ]),
                    new Assert\Email([
                        'message' => $this->translator->trans('Enter a valid email address.')
                    ])
                ]
            ])
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
            ->add('admin', CheckboxType::class, [
                'label'    => 'Admin',
                'required' => false,
                'mapped' => false
            ])
            ->add('hunter', CheckboxType::class, [
                'label'    => 'Hunter',
                'required' => false,
                'mapped' => false
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
