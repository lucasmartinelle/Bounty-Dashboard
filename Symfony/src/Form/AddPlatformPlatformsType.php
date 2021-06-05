<?php

namespace App\Form;

use App\Entity\Platforms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AddPlatformPlatformsType extends AbstractType
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
            ->add('client', TextType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your client cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('btw', TextType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your client cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your address cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
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
            ->add('date', TextType::class, [
                'label' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Platforms::class,
        ]);
    }
}