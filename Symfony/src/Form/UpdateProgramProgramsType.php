<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateProgramProgramsType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scope', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('scope_hidden', HiddenType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your scope cannot be more than {{ limit }} characters long.')
                    ])
                ],
                'required' => false
            ])
            ->add('date', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('status', ChoiceType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans('Choose a status'),
                'choices'  => [
                    $this->translator->trans('Open') => 'Open',
                    $this->translator->trans('Close') => 'Close'
                ],
                'required' => false
            ])
            ->add('tags', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('tags_hidden', HiddenType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('platforms', ChoiceType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans('Choose a Platform'),
                'choices'  => $options['platformsName'],
                'required' => false
            ])
            ->add('programId', HiddenType::class, [
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
        $resolver->setRequired('platformsName');
    }
}
