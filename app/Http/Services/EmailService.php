<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\EmailRepository;
use App\Http\Resource\EmailResource;
use App\Jobs\SendEmail;
use App\Mail\RawEmail;
use App\Models\Email;
use App\Models\EmailToEntityAssociation;
use App\Models\OauthToken;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class EmailService extends BaseService
{
    public function __construct(
        EmailRepository $emailRepository,
    ) {
        $this->repository = $emailRepository;
    }

    public function resource(): string
    {
        return EmailResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        $userToken = OauthToken::query()->where('user_id', $user->getKey())
            ->where('service', OauthToken::SERVICE_NAME_GOOGLE_MAIL)->first();
        if (!$userToken) {
            throw new CustomErrorException('Gmail token not found', 422);
        }
        $params['token_id'] = $userToken->getKey();

        return $this->paginate($this->repository->get($user, $params));
    }

    /**
     * @param array $data
     * @param Authenticatable|User $user
     * @return Email
     * @throws CustomErrorException
     */
    public function send(array $data, Authenticatable|User $user): Email
    {
        /** @var OauthToken $oauthToken */
        $oauthToken = OauthToken::query()->where('user_id', $user->getKey())
            ->where('service', OauthToken::SERVICE_NAME_GOOGLE_MAIL)->first();
        if (!$oauthToken) {
            throw new CustomErrorException('Gmail token not found', 422);
        }
        $data['tokenId'] = $oauthToken->getKey();
        if (!empty($user->user_signature)) {
            $data['message'] = $data['message'] . '<br>' . $user->user_signature;
        }
        if (isset($data['send_at'])) {
            return $this->saveScheduledEmail($data, $user);
        }

        $emailFrom = !empty($oauthToken->user_name) ? $oauthToken->user_name : $user->email;

        $attachmentLinks = $this->saveAttachmentsToStorage($data, $user);
        $scheduleDetails = ['data' => Arr::only($data, ['cc', 'bcc']), 'attachments' => $attachmentLinks];

        /** @var Email $emailEntity */
        $emailEntity = $this->repository->create([
            'email_id' => null,
            'token_id' => $data['tokenId'],
            'received_date' => now(),
            'from' => $emailFrom,
            'to' => $data['sendTo'],
            'subject' => $data['subject'],
            'content' => $data['message'],
            'status' => Email::STATUS_NEW,
            'schedule_details' => $scheduleDetails,
        ]);
        if (!empty($data['relatedToId']) && !empty($data['relatedToEntity'])) {
            EmailToEntityAssociation::query()->create([
                'email_id' => $emailEntity->getKey(),
                'entity_id' => $data['relatedToId'],
                'entity' => 'App\Models\\' . $data['relatedToEntity'],
            ]);
        }

        SendEmail::dispatch($emailEntity);

        return $emailEntity;
    }


    private function saveScheduledEmail(array $data, Authenticatable|User $user): Email
    {
        $emailFrom = !empty($oauthToken->user_name) ? $oauthToken->user_name : $user->email;

        $attachmentLinks = $this->saveAttachmentsToStorage($data, $user);
        $scheduleDetails = ['data' => Arr::only($data, ['cc', 'bcc']), 'attachments' => $attachmentLinks];

        /** @var Email $emailEntity */
        $emailEntity = $this->repository->create([
            'email_id' => null,
            'token_id' => $data['tokenId'],
            'received_date' => now(),
            'from' => $emailFrom,
            'to' => $data['sendTo'],
            'subject' => $data['subject'],
            'content' => $data['message'],
            'status' => Email::STATUS_SCHEDULED,
            'send_at' => $data['send_at'],
            'schedule_details' => $scheduleDetails,
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
     * @param Email $emailRecord
     * @return Email
     * @throws CustomErrorException
     */
    public function sendEmail(Email $emailRecord): Email
    {
        $attachmentLinks = [];

        if (isset($emailRecord->schedule_details['attachments'])) {
            foreach ($emailRecord->schedule_details['attachments'] as $attachment) {
                $attachmentLinks[] = Attachment::fromStorageDisk(
                    's3',
                    $attachment['path'],
                )->as($attachment['fileName'])
                    ->withMime($attachment['mimeType']);
            }
        }

        $rawMailer = new RawEmail($emailRecord->content, $emailRecord->subject, $emailRecord->from, $attachmentLinks);
        $mail = Mail::to($emailRecord->to);
        if (!empty($emailRecord->schedule_details['attachments']['cc'])) {
            $mail = $mail->cc($emailRecord->schedule_details['attachments']['cc']);
        }

        if (!empty($emailRecord->schedule_details['attachments']['bcc'])) {
            $mail = $mail->bcc($emailRecord->schedule_details['attachments']['bcc']);
        }

        $result = $mail->send($rawMailer);
        if (!$result) {
            throw new CustomErrorException('Cant send message', 422);
        }

        $messageId = $result->getMessageId();
        $messageId = ltrim($messageId, '<');
        $messageId = rtrim($messageId, '>');
        $emailRecord->email_id = $messageId;
        $emailRecord->status = Email::STATUS_SENT;
        $emailRecord->save();

        return $emailRecord;
    }

    /**
     * @param array $data
     * @param User|Authenticatable $user
     * @return array
     */
    protected function saveAttachmentsToStorage(array $data, User|Authenticatable $user): array
    {
        $attachmentLinks = [];
        if (isset($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                $savePath = '/email_attachment/' . $user->getKey() . '/';
                $savedFile = StorageHelper::storeFile($attachment, $savePath);

                $attachmentLinks[] = [
                    'path' => $savedFile,
                    'fileName' => $attachment->getClientOriginalName(),
                    'mimeType' => $attachment->getMimeType(),
                ];
            }
        }

        return $attachmentLinks;
    }
}
