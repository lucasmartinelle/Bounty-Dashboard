<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterInvoicesType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('month', ChoiceType::class, [
                'required' => false,
                'placeholder' => $this->translator->trans('Month'),
                'choices'  => [
                    $this->translator->trans("January") => '01',
                    $this->translator->trans("February") => '02',
                    $this->translator->trans("March") => '03',
                    $this->translator->trans("April") => '04',
                    $this->translator->trans("May") => '05',
                    $this->translator->trans("June") => '06',
                    $this->translator->trans("July") => '07',
                    $this->translator->trans("August") => '08',
                    $this->translator->trans("October") => '09',
                    $this->translator->trans("September") => '10',
                    $this->translator->trans("November") => '11',
                    $this->translator->trans("December") => '12',
                ]
            ])
            ->add('platform', ChoiceType::class, [
                'required' => false,
                'placeholder' => $this->translator->trans('Platform'),
                'choices'  => $options['platformsName']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
        $resolver->setRequired('platformsName');
    }
}