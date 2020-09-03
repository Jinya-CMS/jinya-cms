<?php

namespace Jinya\Command;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\Message;
use Jinya\Services\Form\FormServiceInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendMessagingCenterNotifications extends Command
{
    private FormServiceInterface$formService;

    private EntityManagerInterface $entityManager;

    private MailerInterface $mailer;

    private string $mailerSender;

    /**
     * SendMessagingCenterNotifications constructor.
     */
    public function __construct(
        FormServiceInterface $formService,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        string $mailerSender
    ) {
        parent::__construct();
        $this->formService = $formService;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->mailerSender = $mailerSender;
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('jinya:messages:notify')
            ->setDescription('Notifies the toAddress in all forms about the new message count')
            ->addArgument('instanceName', InputArgument::REQUIRED, 'The Jinya instance name this command is executed on')
            ->addArgument('instanceHost', InputArgument::REQUIRED, 'The Jinya instance server this command is executed on');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $forms = $this->formService->getAll();
        foreach ($forms as $form) {
            /** @var Form $form */
            $spamMessages = $this->entityManager
                ->createQueryBuilder()
                ->select('COUNT(message)')
                ->from(Message::class, 'message')
                ->where('message.spam = 1')
                ->andWhere('message.isRead = 0')
                ->andWhere('message.form = :form')
                ->setParameter('form', $form)
                ->getQuery()
                ->getSingleScalarResult();
            $unreadMessages = $this->entityManager
                ->createQueryBuilder()
                ->select('COUNT(message)')
                ->from(Message::class, 'message')
                ->where('message.isRead = 0')
                ->andWhere('message.form = :form')
                ->setParameter('form', $form)
                ->getQuery()
                ->getSingleScalarResult();

            $output->writeln(sprintf('Form "%s" has %d spam messages', $form->getSlug(), $spamMessages));
            $output->writeln(sprintf('Form "%s" has %d unread messages', $form->getSlug(), $unreadMessages));

            $message = new TemplatedEmail();
            $message->to($form->getToAddress());
            $message->subject('Your messages overview for ' . $form->getName());
            $message->htmlTemplate('@Jinya\Email\messageReminder.html.twig');
            $message->from($this->mailerSender);
            $message->context([
                'form' => $form,
                'unreadCount' => $unreadMessages,
                'spamCount' => $spamMessages,
                'instanceName' => $input->getArgument('instanceName'),
                'instanceHost' => $input->getArgument('instanceHost'),
            ]);
            $this->mailer->send($message);
        }

        return 0;
    }
}
