<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateEmailProfileType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
