<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;
use Com\Nairus\CoreBundle\Entity\ContactMessage;

/**
 * Test of ContactController.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactControllerTest extends BaseWebTestCase {

    /**
     * Load trait to clean test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void {
        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $this->cleanDatas($this->getClient()->getContainer(), [ContactMessage::class]);

        parent::tearDown();
    }

    /**
     * Test the form action in FR.
     *
     * @return void
     */
    public function testFormActionFr(): void {
        // 1. go the form page.
        $client = $this->getClient();
        $crawler = $client->request("GET", "/");
        $crawler = $client->click($crawler->filter('#navbar > ul')->selectLink('Contact')->link());

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code excepted is not ok.");
        $this->assertEquals("/contact", $client->getRequest()->getRequestUri(), "1.2 The request uri excepted is not ok");
        $this->assertContains("Formulaire de contact", $crawler->filter("html > head > title")->text(), "1.3 The page title expected is not ok.");
        $this->assertContains("A propos de moi", $crawler->filter("#contact-form h1")->text(), "1.4 The h1 expected is not ok");
        $this->assertContains("Si voulez en savoir plus sur moi, contactez-moi via ce formulaire", $crawler->filter("#contact-form em")->text(),
                "1.4 The h1 expected is not ok");
        // Get the form.
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_corebundle_contactmessage"]');
        $formElements = $form->filter(".form-group");
        $this->assertEquals(5, $formElements->count(), "1.5 five form group elements are expected");
        $this->assertContains("Nom / Prénom", $formElements->eq(0)->text(), "1.6 The name label is not ok");
        $this->assertContains("Téléphone", $formElements->eq(1)->text(), "1.7 The phone label is not ok");
        $this->assertContains("Email", $formElements->eq(2)->text(), "1.8 The email label is not ok");
        $this->assertContains("Votre message (50 caractères min.)", $formElements->eq(3)->text(), "1.9 The massage label is not ok");
        $this->assertContains("* Si vous n'êtes pas un bot, veuillez cliquer sur le bouton bleu pour envoyer votre message.",
                $formElements->eq(4)->filter("em")->text(), "1.10 The emphasis text expected is not ok");
        $ctaButtons = $formElements->eq(4)->filter("button");
        $this->assertEquals(2, $ctaButtons->count(), "1.11 Two cta buttons are expected");
        $this->assertContains("Ne pas envoyer *", $ctaButtons->eq(0)->text(), "1.12 The bad button label is not ok.");
        $this->assertContains("23ab771164ed4db4a5b0641fc35e7fb2", $ctaButtons->eq(0)->attr("value"), "1.13 The bad button value is not ok.");
        $this->assertContains("Envoyer", $ctaButtons->eq(1)->text(), "1.14 The good button label is not ok.");
        $this->assertContains("12fc7bb9b8d24566b51a90e748eb35d9", $ctaButtons->eq(1)->attr("value"), "1.15 The good button value is not ok.");
        $this->assertContains('<i class="far fa-envelope"></i>', $ctaButtons->eq(1)->html(), "1.16 The good button icon is not ok.");
        $this->assertContains("Contact", $crawler->filter("#navbar > ul > li.active")->text(), "1.17 The active tab is not ok.");

        // 2. try to send a message with all errors
        $submitForm = $form->selectButton("Ne pas envoyer *")->form([
            "com_nairus_corebundle_contactmessage[name]" => " ",
            "com_nairus_corebundle_contactmessage[message]" => "Message too short"
        ]);
        $crawler = $client->submit($submitForm);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code excepted is not ok.");
        $this->assertEquals("/contact", $client->getRequest()->getRequestUri(), "2.2 The request uri excepted is not ok");
        $this->assertCount(1, $crawler->filter('.alert-danger'), "2.3 One global error is expected.");
        $this->assertContains("Ouups ! Spam détecté ! Êtes-vous sûr de ne pas être un bot ?", $crawler->filter('.alert-danger')->eq(0)->text(),
                "2.4 The global error message is not ok");
        $invalidInputs = $crawler->filter(".is-invalid");
        $this->assertCount(3, $invalidInputs, "2.5 Three inputs has to be marked as invalid");
        $this->assertEquals("com_nairus_corebundle_contactmessage_name", $invalidInputs->eq(0)->attr("id"), "2.6 The name input has to be marked as invalid");
        $this->assertEquals("com_nairus_corebundle_contactmessage_phone", $invalidInputs->eq(1)->attr("id"), "2.7 The name phone has to be marked as invalid");
        $this->assertEquals("com_nairus_corebundle_contactmessage_message", $invalidInputs->eq(2)->attr("id"), "2.8 The name message has to be marked as invalid");

        $invalidFeedbacks = $crawler->filter(".invalid-feedback");
        $this->assertCount(3, $invalidFeedbacks, "2.5 Three error message has to be displayed");
        $this->assertContains("Cette valeur ne doit pas être vide.", $invalidFeedbacks->eq(0)->text(),
                "2.9 The name input has to be marked as invalid");
        $this->assertContains("Le téléphone ou l'email doivent être renseigné.", $invalidFeedbacks->eq(1)->text(),
                "2.10 The name phone has to be marked as invalid");
        $this->assertContains("Cette chaîne est trop courte. Elle doit avoir au minimum 50 caractères.", $invalidFeedbacks->eq(2)->text(),
                "2.11 The name message has to be marked as invalid");

        // 3. send a message
        $submitForm = $form->selectButton("Envoyer")->form([
            "com_nairus_corebundle_contactmessage[name]" => "Son Goku",
            "com_nairus_corebundle_contactmessage[message]" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
            "com_nairus_corebundle_contactmessage[email]" => "son.goku@dragonball.super",
            "com_nairus_corebundle_contactmessage[phone]" => "+33 701 020 304"
        ]);

        // enables the profiler for the next request (it does nothing if the profiler is not available)
        $client->enableProfiler();
        $client->submit($submitForm);

        // Test if the email has been sent
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount(), "3.1 One email sent is expected");

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message, "3.2 The message type expected is not ok.");
        $this->assertSame('New Contact message', $message->getSubject(), "3.3 The email subject expected is not ok");
        $this->assertSame('nairus.from@test.com', key($message->getFrom()), "3.4 The from email expected is not ok");
        $this->assertSame('nairus.to@test.com', key($message->getTo()), "3.5 The to email expected is not ok");
        $this->assertSame("A contact message has been sent.\nName: Son Goku, contact: son.goku@dragonball.super", $message->getBody(),
                "3.6 The email message expected is not ok.");

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3.7 The status code excepted is not ok.");
        $this->assertEquals("/", $client->getRequest()->getRequestUri(), "3.8 The request uri excepted is not ok");
        $this->assertEquals(1, $crawler->filter('.alert-success')->count(), "3.9 The success message is missing");
        $this->assertContains("Merci ! Votre message a bien été envoyé.", $crawler->filter('.alert-success')->text(), "3.10 The success message is not ok");

        // find the message.
        /* @var $contactMessage ContactMessage */
        $contactMessage = $this->getEntityManager()->getRepository(ContactMessage::class)->findOneByEmail("son.goku@dragonball.super");
        $this->assertNotNull($contactMessage, "3.11 The message not exists in the database");
        $this->assertEquals("Son Goku", $contactMessage->getName(), "3.12 The name of the contact is not ok");
        $this->assertEquals("son.goku@dragonball.super", $contactMessage->getEmail(), "3.13 The email of the contact is not ok");
        $this->assertEquals("+33 701 020 304", $contactMessage->getPhone(), "3.14 The phone of the contact is not ok");
        $this->assertEquals("Lorem ipsum dolor sit amet, consectetur cras amet.", $contactMessage->getMessage(),
                "3.15 The message of the contact is not ok");

        // 4. try to resend a message (antiflood constraint error)
        $crawler = $client->click($crawler->filter('#navbar > ul')->selectLink('Contact')->link());
        $submitForm = $form->selectButton("Envoyer")->form([
            "com_nairus_corebundle_contactmessage[name]" => "Vegeta Suppa Sayan Blue",
            "com_nairus_corebundle_contactmessage[message]" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
            "com_nairus_corebundle_contactmessage[email]" => "vegeta.ssb@dragonball.super",
            "com_nairus_corebundle_contactmessage[phone]" => "+33 701 020 305"
        ]);
        $crawler = $client->submit($submitForm);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "4.1 The status code excepted is not ok.");
        $this->assertEquals("/contact", $client->getRequest()->getRequestUri(), "4.2 The request uri excepted is not ok");
        $this->assertCount(1, $crawler->filter('.alert-danger'), "4.3 One global error is expected.");
        $this->assertContains("Votre message ne peut pas être envoyé. Essayez plus tard s'il vous plait !", $crawler->filter('.alert-danger')->eq(0)->text(),
                "4.4 The global error message is not ok");
    }

    /**
     * Test the form action in FR.
     *
     * @return void
     */
    public function testFormActionEn(): void {
        // 1. go the form page.
        $client = $this->getClient();
        $crawler = $client->request("GET", "/en/");
        $crawler = $client->click($crawler->filter('#navbar > ul')->selectLink('Contact')->link());

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code excepted is not ok.");
        $this->assertEquals("/en/contact", $client->getRequest()->getRequestUri(), "1.2 The request uri excepted is not ok");
        $this->assertContains("Contact form", $crawler->filter("html > head > title")->text(), "1.3 The page title expected is not ok.");
        $this->assertContains("About me", $crawler->filter("#contact-form h1")->text(), "1.4 The h1 expected is not ok");
        $this->assertContains("If you want to know more about me, please contact me via this form", $crawler->filter("#contact-form em")->text(),
                "1.4 The h1 expected is not ok");
        // Get the form.
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_corebundle_contactmessage"]');
        $formElements = $form->filter(".form-group");
        $this->assertEquals(5, $formElements->count(), "1.5 five form group elements are expected");
        $this->assertContains("Last name / First name", $formElements->eq(0)->text(), "1.6 The name label is not ok");
        $this->assertContains("Phone", $formElements->eq(1)->text(), "1.7 The phone label is not ok");
        $this->assertContains("Email", $formElements->eq(2)->text(), "1.8 The email label is not ok");
        $this->assertContains("Your message (50 characters min.)", $formElements->eq(3)->text(), "1.9 The massage label is not ok");
        $this->assertContains("* If you are not a bot, please click on the blue button to send your message.",
                $formElements->eq(4)->filter("em")->text(), "1.10 The emphasis text expected is not ok");
        $ctaButtons = $formElements->eq(4)->filter("button");
        $this->assertEquals(2, $ctaButtons->count(), "1.11 Two cta buttons are expected");
        $this->assertContains("Do not send *", $ctaButtons->eq(0)->text(), "1.12 The bad button label is not ok.");
        $this->assertContains("23ab771164ed4db4a5b0641fc35e7fb2", $ctaButtons->eq(0)->attr("value"), "1.13 The bad button value is not ok.");
        $this->assertContains("Send", $ctaButtons->eq(1)->text(), "1.14 The good button label is not ok.");
        $this->assertContains("12fc7bb9b8d24566b51a90e748eb35d9", $ctaButtons->eq(1)->attr("value"), "1.15 The good button value is not ok.");
        $this->assertContains('<i class="far fa-envelope"></i>', $ctaButtons->eq(1)->html(), "1.16 The good button icon is not ok.");
        $this->assertContains("Contact", $crawler->filter("#navbar > ul > li.active")->text(), "1.17 The active tab is not ok.");

        // 2. try to send a message with all errors
        $submitForm = $form->selectButton("Do not send *")->form([
            "com_nairus_corebundle_contactmessage[name]" => " ",
            "com_nairus_corebundle_contactmessage[message]" => "Message too short"
        ]);
        $crawler = $client->submit($submitForm);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code excepted is not ok.");
        $this->assertEquals("/en/contact", $client->getRequest()->getRequestUri(), "2.2 The request uri excepted is not ok");
        $this->assertCount(1, $crawler->filter('.alert-danger'), "2.3 One global error is expected.");
        $this->assertContains("Ooops! Spam detected! Are you sure that your are not a bot?", $crawler->filter('.alert-danger')->eq(0)->text(),
                "2.4 The global error message is not ok");
        $invalidInputs = $crawler->filter(".is-invalid");
        $this->assertCount(3, $invalidInputs, "2.5 Three inputs has to be marked as invalid");
        $this->assertEquals("com_nairus_corebundle_contactmessage_name", $invalidInputs->eq(0)->attr("id"), "2.6 The name input has to be marked as invalid");
        $this->assertEquals("com_nairus_corebundle_contactmessage_phone", $invalidInputs->eq(1)->attr("id"), "2.7 The name phone has to be marked as invalid");
        $this->assertEquals("com_nairus_corebundle_contactmessage_message", $invalidInputs->eq(2)->attr("id"), "2.8 The name message has to be marked as invalid");

        $invalidFeedbacks = $crawler->filter(".invalid-feedback");
        $this->assertCount(3, $invalidFeedbacks, "2.5 Three error message has to be displayed");
        $this->assertContains("This value should not be blank.", $invalidFeedbacks->eq(0)->text(),
                "2.9 The name input has to be marked as invalid");
        $this->assertContains("The phone or email must be filled in.", $invalidFeedbacks->eq(1)->text(),
                "2.10 The name phone has to be marked as invalid");
        $this->assertContains("This value is too short. It should have 50 characters or more.", $invalidFeedbacks->eq(2)->text(),
                "2.11 The name message has to be marked as invalid");

        // 3. send a message
        $submitForm = $form->selectButton("Send")->form([
            "com_nairus_corebundle_contactmessage[name]" => "Son Goku",
            "com_nairus_corebundle_contactmessage[message]" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
            "com_nairus_corebundle_contactmessage[email]" => "son.goku@dragonball.super",
            "com_nairus_corebundle_contactmessage[phone]" => "+33 701 020 304"
        ]);

        // enables the profiler for the next request (it does nothing if the profiler is not available)

        $client->enableProfiler();
        $client->submit($submitForm);

        // Test if the email has been sent
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount(), "3.1 One email sent is expected");

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message, "3.2 The message type expected is not ok.");
        $this->assertSame('New Contact message', $message->getSubject(), "3.3 The email subject expected is not ok");
        $this->assertSame('nairus.from@test.com', key($message->getFrom()), "3.4 The from email expected is not ok");
        $this->assertSame('nairus.to@test.com', key($message->getTo()), "3.5 The to email expected is not ok");
        $this->assertSame("A contact message has been sent.\nName: Son Goku, contact: son.goku@dragonball.super", $message->getBody(),
                "3.6 The email message expected is not ok.");

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3.7 The status code excepted is not ok.");
        $this->assertEquals("/en/", $client->getRequest()->getRequestUri(), "3.8 The request uri excepted is not ok");
        $this->assertEquals(1, $crawler->filter('.alert-success')->count(), "3.9 The success message is missing");
        $this->assertContains("Thank you! Your message has been sent successfully.", $crawler->filter('.alert-success')->text(), "3.10 The success message is not ok");

        // find the message.
        /* @var $contactMessage ContactMessage */
        $contactMessage = $this->getEntityManager()->getRepository(ContactMessage::class)->findOneByEmail("son.goku@dragonball.super");
        $this->assertNotNull($contactMessage, "3.11 The message not exists in the database");
        $this->assertEquals("Son Goku", $contactMessage->getName(), "3.12 The name of the contact is not ok");
        $this->assertEquals("son.goku@dragonball.super", $contactMessage->getEmail(), "3.13 The email of the contact is not ok");
        $this->assertEquals("+33 701 020 304", $contactMessage->getPhone(), "3.14 The phone of the contact is not ok");
        $this->assertEquals("Lorem ipsum dolor sit amet, consectetur cras amet.", $contactMessage->getMessage(),
                "3.15 The message of the contact is not ok");

        // 4. try to resend a message (antiflood constraint error)
        $crawler = $client->click($crawler->filter('#navbar > ul')->selectLink('Contact')->link());
        $submitForm = $form->selectButton("Send")->form([
            "com_nairus_corebundle_contactmessage[name]" => "Vegeta Suppa Sayan Blue",
            "com_nairus_corebundle_contactmessage[message]" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
            "com_nairus_corebundle_contactmessage[email]" => "vegeta.ssb@dragonball.super",
            "com_nairus_corebundle_contactmessage[phone]" => "+33 701 020 305"
        ]);
        $crawler = $client->submit($submitForm);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "4.1 The status code excepted is not ok.");
        $this->assertEquals("/en/contact", $client->getRequest()->getRequestUri(), "4.2 The request uri excepted is not ok");
        $this->assertCount(1, $crawler->filter('.alert-danger'), "4.3 One global error is expected.");
        $this->assertContains("Your message cannot be sent. Please try later!", $crawler->filter('.alert-danger')->eq(0)->text(),
                "4.4 The global error message is not ok");
    }

}
