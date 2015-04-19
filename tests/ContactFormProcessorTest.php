<?php


use Ardi\ContactFormProcessor;

class ContactFormProcessorTest extends PHPUnit_Framework_TestCase
{
    private $defaultValues = array(
        'name' => 'John Doe',
        'email' => 'john.doe@find.it',
        'message' => 'I want to contact you',
    );

    public function testCorrectValues()
    {
        $values = $this->defaultValues;
        $form = new ContactFormProcessor($values);
        $this->assertEquals($values['name'], $form->getSenderName());
        $this->assertEquals($values['email'], $form->getSenderEmail());
        $this->assertEquals($values['message'], $form->getMessage());
        // The following values come from config/app.ini
        $this->assertEquals('fake_email@localhost', $form->getRecipientEmail());
        $this->assertEquals('Email generated during unit tests', $form->getSubject());
        // Mock the host and submit the email
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertTrue($form->submit());
    }

    public function testSanitizedValues()
    {
        $values = array(
            'name' => 'John "Doe"',
            'email' => '<a>john.doe@find.it</a>',
            'message' => 'I want to <b>contact</b> <em style="color:red">you</em>',
        );
        $form = new ContactFormProcessor($values);
        $this->assertEquals('John &quot;Doe&quot;', $form->getSenderName());
        $this->assertEquals($this->defaultValues['email'], $form->getSenderEmail());
        $this->assertEquals($this->defaultValues['message'], $form->getMessage());
    }
}
