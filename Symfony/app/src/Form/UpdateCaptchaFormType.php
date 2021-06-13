<?php

namespace App\Form;

use App\Entity\Captcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateCaptchaFormType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('private_key', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a private key'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your private key cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('public_key', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a public key'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your public key cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Captcha::class,
        ]);
    }
}
