<?php

namespace App\Messaging;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Mailing\Factory\MailerFactory;
use App\Web\Exceptions\MissingFieldsException;
use JetBrains\PhpStorm\Pure;
use League\Plates\Engine;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface;

class FormMessageHandler
{
    private ServerRequestInterface $request;
    private Engine $engine;

    /**
     * FormMessageHandler constructor.
     * @param ServerRequestInterface $request
     * @param Engine $engine
     */
    public function __construct(ServerRequestInterface $request, Engine $engine)
    {
        $this->request = $request;
        $this->engine = $engine;
    }

    /**
     * Handles the form post and creates
     *
     * @param Form $form
     * @param array $body
     * @throws MissingFieldsException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exception
     */
    public function handleFormPost(Form $form, array $body): void
    {
        $formValues = [];
        $missingFields = [];
        $subject = 'New message for form ' . $form->title;
        $isSpam = false;
        $fromAddress = 'Some person';
        foreach ($form->getItems() as $item) {
            /** @var $item FormItem */
            $value = $body[$item->id];
            if ($item->isRequired && !isset($value)) {
                $missingFields[] = $item->label;
            }

            if ($item->isSubject) {
                $subject = $value ?? 'New Message';
            }

            if ($item->isFromAddress) {
                $fromAddress = $value ?? 'Some person';
            }

            $formValues[$item->label] = $value ?? null;
            $spamFilter = $item->spamFilter;
            $isSpam |= $this->isSpam($value, $spamFilter);
        }

        if (!$isSpam) {
            $mailer = MailerFactory::getMailer();
            if ($fromAddress !== 'Some person') {
                $mailer->addReplyTo($fromAddress);
            }
            $mailer->addAddress($form->toAddress);
            $mailer->setFrom(getenv('MAILER_FROM'));
            $mailer->Subject = $subject;
            $mailer->Body = $this->renderTemplate($formValues, $subject);
        }

        if (!empty($missingFields)) {
            throw new MissingFieldsException($this->request, $missingFields);
        }
    }

    #[Pure] private function isSpam(string $value, array $spamValues): bool
    {
        if (empty($spamValues)) {
            return false;
        }

        foreach ($spamValues as $spamValue) {
            if (stripos($value, $spamValue) !== false) {
                return true;
            }
        }

        return false;
    }

    public function renderTemplate(array $data, string $title): string
    {
        $this->engine->addFolder('messaging', __DIR__ . '/Templates');

        return $this->engine->render('messaging::new', ['data' => $data, 'title' => $title]);
    }
}