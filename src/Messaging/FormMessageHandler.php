<?php

namespace App\Messaging;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Mailing\Factory\MailerFactory;
use App\Web\Exceptions\MissingFieldsException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use League\Plates\Engine;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 *
 */
class FormMessageHandler
{
    private Engine $engine;

    /**
     * FormMessageHandler constructor.
     */
    public function __construct()
    {
        $this->engine = \App\Theming\Engine::getPlatesEngine();
    }

    /**
     * Handles the form post and creates
     *
     * @param Form $form
     * @param array $body
     * @param ServerRequestInterface $request
     * @throws Exception
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws MissingFieldsException
     * @throws Throwable
     * @throws UniqueFailedException
     */
    public function handleFormPost(Form $form, array $body, ServerRequestInterface $request): void
    {
        $formValues = [];
        $missingFields = [];
        $subject = 'New message for form ' . $form->title;
        $isSpam = false;
        $fromAddress = 'Some person';
        foreach ($form->getItems() as $item) {
            /** @var $item FormItem */
            if ($item->type === 'checkbox') {
                $value = $body[$item->id] ?? false;
            } else {
                $value = $body[$item->id];
            }
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
            $mailer->isHTML();
            $mailer->send();
        }

        if (!empty($missingFields)) {
            throw new MissingFieldsException($request, $missingFields);
        }
    }

    private function isSpam(string $value, array $spamValues): bool
    {
        $values = array_filter($spamValues);
        if (empty($values)) {
            return false;
        }

        foreach ($values as $spamValue) {
            if (stripos($value, $spamValue) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Throwable
     */
    public function renderTemplate(array $data, string $title): string
    {
        $this->engine->addFolder('messaging', __DIR__ . '/Templates');

        return $this->engine->render('messaging::new', ['data' => $data, 'title' => $title]);
    }
}