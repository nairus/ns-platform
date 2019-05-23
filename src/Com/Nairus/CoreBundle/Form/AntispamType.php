<?php

namespace Com\Nairus\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Antispam field.
 *
 * @link https://symfonic.fr/fr/2012/09/alternative-captcha-symfony2/ Original tips
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AntispamType extends AbstractType {

    /**
     * The antispam option's choices.
     *
     * @var array
     */
    protected $choices;

    /**
     * The constructor.
     *
     * @param array $choices The option's choices.
     */
    public function __construct(array $choices) {
        $this->choices = $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'choices' => [
                'antispam.options.bad' => $this->choices['bad'],
                'antispam.options.good' => $this->choices['good']
            ],
            'choices_as_values' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);
        $icon = "far fa-comment-dots";
        if (array_key_exists("data", $options)) {
            $icon = $options['data'];
        }
        $view->vars['btn_classes'] = array('btn', 'btn btn-primary');
        $view->vars['label_prefix'] = array(null, '<i class="' . $icon . '"></i> ');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent() {
        return ChoiceType::class;
    }

    /**
     * Return the name of the template.
     *
     * @return string
     */
    public function getName() {
        return 'antispam';
    }

    /**
     * Return the choices's list.
     *
     * @return array
     */
    public function getChoices(): array {
        return $this->choices;
    }

}
