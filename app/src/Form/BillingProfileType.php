<?php

namespace App\Form;

use App\Entity\Billing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class BillingProfileType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your name'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your name cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your firstname'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your firstname cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your address'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your address cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your phone number'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your phone number cannot be more than {{ limit }} characters long.')
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
            ->add('siret', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your SIRET'),
                    ]),
                    new Assert\Length([
                        'max' => 14,
                        'maxMessage' => $this->translator->trans('Your SIRET cannot be more than {{ limit }} characters long.'),
                        'min' => 14,
                        "minMessage" => $this->translator->trans('Your SIRET must be at least {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('vat', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your VAT'),
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => $this->translator->trans('Your VAT cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('bank', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your bank'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your bank cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('bic', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your BIC'),
                    ]),
                    new Assert\Length([
                        'max' => 11,
                        'maxMessage' => $this->translator->trans('Your BIC cannot be more than {{ limit }} characters long.'),
                        'min' => 8,
                        "minMessage" => $this->translator->trans('Your SIRET must be at least {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('iban', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your IBAN'),
                    ]),
                    new Assert\Length([
                        'max' => 34,
                        'maxMessage' => $this->translator->trans('Your IBAN cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Billing::class,
        ]);
    }
}
