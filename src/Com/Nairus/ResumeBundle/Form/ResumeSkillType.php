<?php

namespace Com\Nairus\ResumeBundle\Form;

use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ResumeSkill form.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeSkillType extends AbstractType {

    /**
     * Current locale.
     *
     * @var string
     */
    private $currentLocale;

    /**
     * Default locale.
     *
     * @var string
     */
    private $defaultLocale;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        // Get special options to access it in the closure.
        $this->currentLocale = $options['currentLocale'];
        $this->defaultLocale = $options['defaultLocale'];

        $builder->add('rank', IntegerType::class, [
                    'label' => 'resume-skill.labels.rank',
                    'translation_domain' => 'NSResumeBundle',
                ])
                ->add('skill', EntityType::class, [
                    'class' => Skill::class,
                    'label' => 'resume-skill.labels.skill',
                    'translation_domain' => 'NSResumeBundle',
                    'choice_label' => function(Skill $skill) {
                        return $skill->getTitle();
                    }
                ])
                ->add('skillLevel', EntityType::class, [
                    'class' => SkillLevel::class,
                    'label' => 'resume-skill.labels.skill-level',
                    'translation_domain' => 'NSResumeBundle',
                    'choice_label' => function (SkillLevel $skillLevel) {
                        return $skillLevel->getTitle() ? $skillLevel->getTitle($this->currentLocale) : $skillLevel->translate($this->defaultLocale)->getTitle();
                    }
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Com\Nairus\ResumeBundle\Entity\ResumeSkill',
            'currentLocale' => null,
            'defaultLocale' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'com_nairus_resumebundle_resumeskill';
    }

}
