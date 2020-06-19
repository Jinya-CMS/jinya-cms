<?php

namespace App\Messaging;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Database\Message;
use App\Web\Exceptions\MissingFieldsException;
use DateTime;
use League\Plates\Engine;
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
     */
    public function handleFormPost(Form $form, array $body): void
    {
        $formValues = [];
        $missingFields = [];
        $fromAddress = '';
        $subject = 'New message for form ' . $form->title;
        $isSpam = false;
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

        $message = new Message();
        $message->fromAddress = $fromAddress;
        $message->subject = $subject;
        $message->formId = $form->id;
        $message->sendAt = new DateTime();
        $message->targetAddress = $form->toAddress;
        $message->content = $this->renderTemplate($formValues, $form->title);
        $message->spam = $isSpam;
        $message->create();

        if (!empty($missingFields)) {
            throw new MissingFieldsException($this->request, $missingFields);
        }
    }

    private function isSpam(string $value, array $spamValues): bool
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