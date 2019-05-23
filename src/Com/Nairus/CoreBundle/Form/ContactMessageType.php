<?php

namespace Com\Nairus\CoreBundle\Form;

use Com\Nairus\CoreBundle\Form\AntispamType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * ContactMessage form.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactMessageType extends AbstractType {

    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

    /**
     * The constructor.
     *
     * @param LoggerInterface     $logger     The logger service.
     * @param TranslatorInterface $translator The translation service.
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator) {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name')
                ->add('phone')
                ->add('email')
                ->add('message');

        // antispam button + validation
        $builder->add('antispam', AntispamType::class, ['mapped' => false, "data" => "far fa-envelope"])
                ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                    $form = $event->getForm();
                    $data = $event->getData();
                    $choices = $form->get("antispam")->getConfig()->getType()->getInnerType()->getChoices();
                    if (!$form->has('antispam') || $data['antispam'] !== $choices['good']) {
                        $this->logger->debug("Antispam violation detected!");
                        $form->addError(new FormError($this->getTranslation('ns_core.antispam', [], 'validators')));
                    }
                });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Com\Nairus\CoreBundle\Entity\ContactMessage',
            'allow_extra_fields' => true,
            'csrf_token_id' => 'contact_message_form',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'com_nairus_corebundle_contactmessage';
    }

}
