<?php

namespace Com\Nairus\ResumeBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillLevelType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('translations', TranslationsType::class, [
            'label' => 'form.labels.translations',
            'translation_domain' => 'NSResumeBundle',
            'required_locales' => ['fr', 'en'], // Force default config for this form in particular
            'fields' => [
                'title' => [
                    'locale_options' => [
                        'fr' => ['label' => 'form.labels.title'],
                        'en' => ['label' => 'form.labels.title'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Com\Nairus\ResumeBundle\Entity\SkillLevel'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'com_nairus_resumebundle_skilllevel';
    }

}
