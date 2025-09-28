<?php

namespace Jinya\Cms\Messaging;

use Jinya\Cms\Database\Form;
use Jinya\Cms\Database\FormItem;
use Jinya\Cms\Mailing\Factory\MailerFactory;
use Jinya\Cms\Theming\Engine;
use Jinya\Plates\Engine as PlatesEngine;
use Jinya\Router\Extensions\Database\Exceptions\MissingFieldsException;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * The form message handler, handles the data from form posts and sends the mail to the configured to address
 */
readonly class FormMessageHandler
{
    private PlatesEngine $engine;

    /**
     * FormMessageHandler constructor.
     */
    public function __construct()
    {
        $this->engine = Engine::getPlatesEngine();
    }

    /**
     * Handles the form post and creates
     *
     * @param Form $form
     * @param array<int, string|bool> $body
     * @param ServerRequestInterface $request
     * @throws Exception
     * @throws Throwable
     */
    public function handleFormPost(Form $form, array $body, ServerRequestInterface $request): void
    {
        $formValues = [];
        $missingFields = [];
        $subject = 'New message for form ' . $form->title;
        $isSpam = false;
        $fromAddress = 'Some person';
        foreach ($form->getItems() as $item) {
            if ($item->isRequired && !array_key_exists($item->id, $body)) {
                $missingFields[] = $item->label;
                continue;
            }

            if ($item->type === 'checkbox') {
                $value = $body[$item->id] ?? false;
            } else {
                $value = $body[$item->id];
            }

            if ($item->isSubject) {
                $subject = $value ?: 'New Message';
            }

            if ($item->isFromAddress) {
                $fromAddress = $value ?: 'Some person';
            }

            $formValues[$item->label] = $value ?: null;
            $spamFilter = $item->spamFilter;
            $isSpam |= $this->isSpam((string)$value, $spamFilter);
            if ($isSpam) {
                break;
            }
        }

        if (!empty($missingFields)) {
            throw new MissingFieldsException($request, $missingFields);
        }

        if (!$isSpam) {
            $mailer = MailerFactory::getMailer();
            if ($fromAddress !== 'Some person' && !is_bool($fromAddress)) {
                $mailer->addReplyTo($fromAddress);
            }
            $mailer->addAddress($form->toAddress);
            $mailer->setFrom(getenv('MAILER_FROM') ?: '');
            $mailer->Subject = (string)$subject;
            $mailer->Body = $this->renderTemplate($formValues, $mailer->Subject);
            $mailer->isHTML();
            $mailer->send();
        }
    }

    /**
     * Checks if the provided value is inside the spam keyword list
     *
     * @param string $value
     * @param array<string> $spamValues
     * @return bool
     */
    public function isSpam(string $value, array $spamValues): bool
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
     * Renders the messaging template and returns the string
     *
     * @param array<string, mixed> $data
     * @param string $title
     * @return string
     * @throws Throwable
     */
    public function renderTemplate(array $data, string $title): string
    {
        $this->engine->addFolder('messaging', __DIR__ . '/Templates');

        return $this->engine->render('messaging::new', ['data' => $data, 'title' => $title]);
    }
}
