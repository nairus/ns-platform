<?php

namespace Com\Nairus\ResumeBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $currentYear = date('Y');
        $years = range($currentYear - 50, $currentYear);
        $yearChoices = [
            "----" => ""
        ];

        foreach (array_reverse($years) as $year) {
            $yearChoices[$year] = $year;
        }

        $monthChoices = [
            "monthes.choose-label" => ""
        ];
        for ($i = 0; $i < 12; $i++) {
            $monthNumber = $i + 1;
            $monthKey = "monthes." . $monthNumber;
            $monthChoices[$monthKey] = $monthNumber;
        }

        $builder->add('company', TextType::class, [
                    'label' => 'experience.labels.company',
                    'translation_domain' => 'NSResumeBundle'
                ])
                ->add('location', TextType::class, [
                    'label' => 'experience.labels.location',
                    'translation_domain' => 'NSResumeBundle'
                ])
                ->add('startMonth', ChoiceType::class, [
                    'label' => 'experience.labels.start-month',
                    'translation_domain' => 'NSResumeBundle',
                    'choices' => $monthChoices,
                    'choice_translation_domain' => 'messages'
                ])
                ->add('endMonth', ChoiceType::class, [
                    'label' => 'experience.labels.end-month',
                    'translation_domain' => 'NSResumeBundle',
                    'choices' => $monthChoices,
                    'choice_translation_domain' => 'messages',
                    'required' => false
                ])
                ->add('startYear', ChoiceType::class, [
                    'label' => 'form.labels.start-year',
                    'translation_domain' => 'NSResumeBundle',
                    'choices' => $yearChoices,
                    'choice_translation_domain' => false,
                ])
                ->add('endYear', ChoiceType::class, [
                    'label' => 'form.labels.end-year',
                    'translation_domain' => 'NSResumeBundle',
                    'choices' => $yearChoices,
                    'choice_translation_domain' => false,
                    'required' => false
                ])
                ->add('currentJob', CheckboxType::class, [
                    'label' => 'experience.labels.current-job',
                    'required' => false,
                    'translation_domain' => 'NSResumeBundle',
                    'label_attr' => ['class' => 'checkbox-custom']
                ])
                ->add('translations', TranslationsType::class, [
                    'label' => 'form.labels.translations',
                    'translation_domain' => 'NSResumeBundle',
                    'fields' => [
                        'description' => [
                            'locale_options' => [
                                'fr' => ['label' => 'form.labels.description'],
                                'en' => ['label' => 'form.labels.description'],
                            ]
                        ],
                    ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Com\Nairus\ResumeBundle\Entity\Experience'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'com_nairus_resumebundle_experience';
    }

}
