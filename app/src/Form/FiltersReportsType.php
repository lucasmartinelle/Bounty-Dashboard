<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class FiltersReportsType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('program', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => $this->translator->trans('Program'),
                'choices'  => $options['programsName']
            ])
            ->add('platform', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => $this->translator->trans('Platform'),
                'choices'  => $options['platformsName']
            ])
            ->add('status', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => 'Status',
                'choices'  => [
                    $this->translator->trans('New') => 'New',
                    $this->translator->trans('Accepted') => 'Accepted',
                    $this->translator->trans('Resolved') => 'Resolved',
                    $this->translator->trans('NA') => 'NA',
                    $this->translator->trans('OOS') => 'OOS',
                    $this->translator->trans('Informative') => 'Informative',
                ]
            ])
            ->add('severity_min', NumberType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('severity_max', NumberType::class, [
                'label' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
        $resolver->setRequired('programsName');
        $resolver->setRequired('platformsName');
    }
}
