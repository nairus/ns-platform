<?php

namespace Com\Nairus\ResumeBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Education form type.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EducationType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $currentYear = date('Y');
        $years = range($currentYear - 50, $currentYear);
        $choices = [];
        foreach (array_reverse($years) as $year) {
            $choices[$year] = $year;
        }

        $builder->add('institution', TextType::class, [
                    'label' => 'education.labels.institution',
                    'translation_domain' => 'NSResumeBundle',
                ])
                ->add('diploma', TextType::class, [
                    'label' => 'education.labels.diploma',
                    'translation_domain' => 'NSResumeBundle',
                ])
                ->add('startYear', ChoiceType::class, [
                    'label' => 'education.labels.start-year',
                    'translation_domain' => 'NSResumeBundle',
                    'choices' => $choices,
                    'choice_translation_domain' => false,
                ])
                ->add('endYear', ChoiceType::class, [
                    'label' => 'education.labels.end-year',
                    'translation_domain' => 'NSResumeBundle',
                    'choices' => $choices,
                    'choice_translation_domain' => false,
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
                        'domain' => [
                            'locale_options' => [
                                'fr' => ['label' => 'education.labels.domain'],
                                'en' => ['label' => 'education.labels.domain'],
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
            'data_class' => 'Com\Nairus\ResumeBundle\Entity\Education'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'com_nairus_resumebundle_education';
    }

}
