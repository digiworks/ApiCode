<?php

namespace code\mailer;

use code\applications\ApiAppFactory;
use Exception;
use Symfony\Component\Mime\Email;

class RenderMailer {

    /**
     * @var MailerInterface
     */
    private $mailer;
    private $subject;
    private $viewHtml;
    private $viewText;
    private $from;
    private $to;
    private $cc;
    private $bcc;
    private $replyTo;

    public function getSubject() {
        return $this->subject;
    }

    public function setSubject($subject): void {
        $this->subject = $subject;
    }

    public function getFrom() {
        return $this->from;
    }

    public function setFrom($from): void {
        $this->from = $from;
    }

    public function getView() {
        return $this->view;
    }

    public function getTo() {
        return $this->to;
    }

    public function getCc() {
        return $this->cc;
    }

    public function getBcc() {
        return $this->bcc;
    }

    public function getReplyTo() {
        return $this->replyTo;
    }

    public function setView($view): void {
        $this->view = $view;
    }

    public function setTo($to): void {
        $this->to = $to;
    }

    public function setCc($cc): void {
        $this->cc = $cc;
    }

    public function setBcc($bcc): void {
        $this->bcc = $bcc;
    }

    public function setReplyTo($replyTo): void {
        $this->replyTo = $replyTo;
    }

    public function __construct(MailerInterface $mailer, $from, $to, string $viewHtml = "", string $viewText = "") {
        $this->mailer = $mailer;
        $this->viewHtml = $viewHtml;
        $this->viewText = $viewText;
        $this->from = $from;
        $this->to = $to;
    }

    public function sendEmail(array $formData): void {
        // Validate form data
        $this->validate($formData);

        // Send email
        $email = (new Email())
                ->from($this->from)
                ->to($this->to)

                //->bcc('bcc@example.com')
                //->replyTo('john.doe@example.com')
                ->priority($this->mailer->getSettings()['priority']);
        if (!empty($this->cc)) {
            $email->cc($this->cc);
        }
        $email->subject('Time for Symfony Mailer!');
        $email->text('Sending emails is fun again!');
        $email->html($this->render($this->viewHtml, $formData));

        $this->mailer->send($email);
    }

    private function validate(array $data): void {
        
    }

    /**
     * 
     * @param string $view
     * @param array $formData
     * @return string
     * @throws Exception
     */
    protected function render(string $view, array $formData): string {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($formData, EXTR_OVERWRITE);
        try {
            require $view;
            return ob_get_clean();
        } catch (Exception $e) {
            ApiAppFactory::getApp()->getLogger()->error("error", $e->getTraceAsString());
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
        return "";
    }

}
