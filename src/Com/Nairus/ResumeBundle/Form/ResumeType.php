<?php

namespace Com\Nairus\ResumeBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Resume form.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('anonymous', CheckboxType::class, [
                    'label' => 'resume.form.anonymous',
                    'required' => false,
                    'translation_domain' => 'NSResumeBundle',
                    'label_attr' => ['class' => 'checkbox-custom']
                ])
                ->add('translations', TranslationsType::class, [
                    'label' => 'form.labels.translations',
                    'translation_domain' => 'NSResumeBundle',
                    'fields' => [
                        'title' => [
                            'locale_options' => [
                                'fr' => ['label' => 'form.labels.title'],
                                'en' => ['label' => 'form.labels.title'],
                            ]
                        ]
                    ],
                    'excluded_fields' => ['slug']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Com\Nairus\ResumeBundle\Entity\Resume'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'com_nairus_resumebundle_resume';
    }

}
