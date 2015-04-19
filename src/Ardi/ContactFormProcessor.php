<?php

namespace Ardi;


/**
 * Provides an easy way to handle contact for submissions
 *
 * @package Ardi
 */
class ContactFormProcessor
{
    private $recipientEmail;
    private $senderName;
    private $senderEmail;
    private $subject;
    private $message;

    /**
     * The HTML form must have the following inputs: name, email and message.
     * In addition, inside the "contact" section of app.ini an email must be provided, as well as a subject.
     *
     * @param array $values Can be $_POST, the values will be sanitized
     */
    public function __construct($values)
    {
        // Populate the values received from the form
        $this->senderName = Utils::sanitizeString($values['name']);
        $this->senderEmail = Utils::sanitizeString($values['email']);
        $this->message = Utils::sanitizeString($values['message']);

        // Populate the rest of values from the configuration
        $appConfig = ConfigReader::getReader('app');
        $this->recipientEmail = $appConfig->get('contact.email');
        $this->subject = $appConfig->get('contact.subject');
    }

    /**
     * Sends the form contents
     *
     * @return bool Whether the form was successfully submitted
     */
    public function submit()
    {
        $fromEmail = 'contact-form@' . Utils::sanitizeString($_SERVER['HTTP_HOST']);
        $headers = "From: Contact Form <$fromEmail>" . "\r\n" .
            "Reply-To: $this->senderEmail" . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        return mail($this->recipientEmail, $this->subject, $this->message, $headers);
    }

    /**
     * What will be sent as the email contents (inquiry inputted in the contact form)
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Email address to send the email to
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * Person submitting the form
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Email address of the person submitting the form
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Subject of the actual email that will be sent
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
