<?php
/**
 * Author: Jasmin Stern
 * Date: 03.01.2017
 * Time: 21:05
 */

namespace App\Helper\Mail;


class Mail
{

    /**
     * Subject of the mail
     *
     * @var
     */
    private $subject;

    /**
     * Message of the mail.
     *
     * @var
     */
    private $message;

    /**
     * E-Mail-Address for the mail.
     *
     * @var
     */
    private $email;

    /**
     * Send the mail.
     */
    public function sendMail()
    {
        $content = "\n--- E-Mail ---\n";
        $content .= "\nE-Mail: " . $this->getEmail() . "";
        $content .= "\nBetreff: " . $this->getSubject() . "\n\n";
        $content .= $this->getMessage();
        $content .= "\n--- Ende der E-Mail ---\n\n";

        file_put_contents('php://stderr', $content);
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


}