<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatusReportsType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Status',
                'choices'  => [
                    'New' => 'New',
                    'Accepted' => 'Accepted',
                    'Resolved' => 'Resolved',
                    'NA' => 'NA',
                    'OOS' => 'OOS',
                    'Informative' => 'Informative',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a status.'),
                    ]),
                ]
            ])
            ->add('id', HiddenType::class, [
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}