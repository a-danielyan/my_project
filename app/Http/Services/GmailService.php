<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Helpers\EmailBuilder;
use App\Http\Repositories\EmailRepository;
use App\Http\Services\Oauth2\GoogleOauth2ServiceBase;
use App\Models\Email;
use App\Models\EmailToEntityAssociation;
use App\Models\OauthToken;
use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google_Service_Gmail_Message;

class GmailService extends GoogleOauth2ServiceBase
{
    private const GMAIL_SEND_SCOPE = 'https://www.googleapis.com/auth/gmail.send';
    private const GMAIL_READ_ONLY_SCOPE = 'https://www.googleapis.com/auth/gmail.readonly';
    private const GMAIL_MODIFY_SCOPE = 'https://www.googleapis.com/auth/gmail.modify';

    public Gmail $gmail;

    private EmailRepository $emailRepository;

    public function __construct(GoogleClient $googleClient, EmailRepository $emailRepository)
    {
        parent::__construct($googleClient);

        $this->googleClient->setApplicationName('Gmail');
        $this->googleClient->setPrompt('select_account consent');
        $this->gmail = new Gmail($this->googleClient);
        $this->emailRepository = $emailRepository;
    }

    protected function getCredentials(): array
    {
        return [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
        ];
    }

    protected function getOauth2RedirectUrl(): string
    {
        return config('services.google.redirect');
    }

    protected function getScopes(): array
    {
        return [
            self::GMAIL_SEND_SCOPE,
            self::GMAIL_READ_ONLY_SCOPE,
            self::GMAIL_MODIFY_SCOPE,
        ];
    }


    public function getEmails($maxResults = 100): array
    {
        $result = [];
        $nexPageToken = '';
        do {
            $emails = $this->gmail->users_messages->listUsersMessages(
                'me',
                ['pageToken' => $nexPageToken, 'maxResults' => $maxResults],
            );
            $nexPageToken = $emails->getNextPageToken();
            $result = $this->getEmailDetails($emails, $result);
        } while (count($result) < 100);

        return $result;
    }

    private function parseHeaders(array $headers): array
    {
        $result = [
            'from' => '',
            'to' => '',
            'subject' => '',

        ];
        foreach ($headers as $element) {
            switch ($element->name) {
                case 'To':
                    $toMessages = mailparse_rfc822_parse_addresses($element->value);
                    $messageList = [];
                    foreach ($toMessages as $message) {
                        $messageList[] = $message['address'];
                    }
                    $result['to'] = $messageList;
                    break;

                case 'Subject':
                    $result['subject'] = $element->value;
                    break;

                case 'From':
                    $result['from'] = mailparse_rfc822_parse_addresses($element->value)[0]['address'];
                    break;

                default:
                    break;
            }
        }

        return $result;
    }

    private function searchPartWithContent(array $parts): string
    {
        foreach ($parts as $part) {
            if (!empty($part->getParts())) {
                return $this->searchPartWithContent($part->getParts());
            }

            if ($part->getMimeType() === 'text/html') {
                return $part->getBody()->getData();
            }
        }

        return '';
    }

    private function getEmailDetails($emails, array $result): array
    {
        foreach ($emails as $email) {
            $existedEmail = $this->emailRepository->getCount(where: ['email_id' => $email->getId()]);
            if ($existedEmail > 0) {
                continue;
            }

            $emailDetailed = $this->gmail->users_messages->get('me', $email->getId());
            $messageContent = $emailDetailed->getPayload()->getBody()->getData();
            if (empty($messageContent)) {
                $parts = $emailDetailed->getPayload()->getParts();
                $messageContent = $this->searchPartWithContent($parts);

                if (empty($messageContent)) {
                    $messageContent = $parts[0]->getBody()->getData();
                }
            }

            $receivedDate = (int)$emailDetailed->getInternalDate();

            $parsedHeaders = $this->parseHeaders($emailDetailed->getPayload()->getHeaders());
            $result[] = [
                'email_id' => $email->getId(),
                'received_date' => date('Y-m-d H:i:s', $receivedDate / 1000),
                'from' => $parsedHeaders['from'],
                'to' => $parsedHeaders['to'],
                'subject' => $parsedHeaders['subject'],
                'content' => $messageContent,
            ];
        }

        return $result;
    }

    /**
     * @param array $data
     * @param OauthToken $oauthToken
     * @return Email
     * @throws CustomErrorException
     */
    public function sendEmail(array $data, OauthToken $oauthToken): Email
    {
        $emailFrom = !empty($oauthToken->user_name) ? $oauthToken->user_name : '';

        $message = $this->createMessage(
            $emailFrom,
            $data['sendTo'],
            $data['subject'],
            $data['message'],
            $data['attachments'],
        );

        $emails = $this->gmail->users_messages->send('me', $message);

        /** @var Email $emailEntity */
        $emailEntity = $this->emailRepository->create([
            'email_id' => $emails->getId(),
            'token_id' => $data['tokenId'],
            'received_date' => now(),
            'from' => !empty($oauthToken->user_name) ? $oauthToken->user_name : 'me',
            'to' => $data['sendTo'],
            'subject' => $data['subject'],
            'content' => $data['message'],
        ]);
        if (!empty($data['relatedToId']) && !empty($data['relatedToEntity'])) {
            EmailToEntityAssociation::query()->create([
                'email_id' => $emailEntity->getKey(),
                'entity_id' => $data['relatedToId'],
                'entity' => 'App\Models\\' . $data['relatedToEntity'],
            ]);
        }

        return $emailEntity;
    }

    /**
     * @param string $fromEmail
     * @param array $toEmails
     * @param string $subject
     * @param string $messageText
     * @param array $attachments
     * @return Google_Service_Gmail_Message
     * @throws CustomErrorException
     */
    private function createMessage(
        string $fromEmail,
        array $toEmails,
        string $subject,
        string $messageText,
        array $attachments = [],
    ): Google_Service_Gmail_Message {
        $emailBuilder = new EmailBuilder();
        $emailBuilder->setMailFrom($fromEmail);
        $emailBuilder->setMailTo(array_shift($toEmails));
        $emailBuilder->setMailCC($toEmails);
        $emailBuilder->setSubject($subject);
        $emailBuilder->setEmailBody($messageText);
        $emailBuilder->setMessageContentType('text/html');

        foreach ($attachments as $attachment) {
            $emailBuilder->addAttachment($attachment['link'], $attachment['fileName'], $attachment['mime']);
        }

        $rawMessageString = $emailBuilder->prepareEmail();

        $message = new Google_Service_Gmail_Message();
        $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $message->setRaw($rawMessage);

        return $message;
    }
}
