<?php

namespace App\Messaging;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Web\Exceptions\MissingFieldsException;
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function handleFormPost(Form $form, array $body): void
    {
        $formValues = [];
        $missingFields = [];
        $fromAddress = getenv('MAILER_FROM');
        $subject = 'New message for form ' . $form->title;
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
        }

        // TODO: Implement mail based form handling

        if (!empty($missingFields)) {
            throw new MissingFieldsException($this->request, $missingFields);
        }
    }

    public function renderTemplate(array $data, string $title): string
    {
        $this->engine->addFolder('messaging', __DIR__ . '/Templates');

        return $this->engine->render('messaging::new', ['data' => $data, 'title' => $title]);
    }
}