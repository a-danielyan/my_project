<?php

namespace App\Jobs;

use App\Helpers\CustomFieldValuesHelper;
use App\Mail\RawEmail;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Sequence\SequenceEntityAssociation;
use App\Models\Sequence\SequenceEntityLog;
use App\Models\Sequence\SequenceTemplateAssociation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendSequenceEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SequenceEntityAssociation $sequenceEntityAssociation,
        public SequenceTemplateAssociation $sequenceTemplateAssociation,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var SequenceEntityLog $entityLog */
        $entityLog = SequenceEntityLog::query()->create([
            'entity_id' => $this->sequenceEntityAssociation->id,
            'email_template_id' => $this->sequenceTemplateAssociation->template_id,
            'status' => SequenceEntityLog::STATUS_NEW,
            'sequence_id' => $this->sequenceTemplateAssociation->sequence_id,
        ]);

        $this->sequenceEntityAssociation->count_emails_sent = SequenceEntityLog::query()->where(
            'sequence_id',
            $this->sequenceTemplateAssociation->sequence_id,
        )->where('entity_id', $this->sequenceEntityAssociation->id)->count();
        $this->sequenceEntityAssociation->save();


        $template = $this->sequenceTemplateAssociation->template;
        $entity = $this->sequenceEntityAssociation->entity;

        $customFields = CustomFieldValuesHelper::getCustomFieldValues(
            $entity,
            ['lead-owner', 'email', 'contact-owner'],
        );


        if (!isset($customFields['email'])) {
            Log::error(
                'Email not found for ' . $this->sequenceEntityAssociation->entity_type . ' with id ' .
                $this->sequenceEntityAssociation->entity_id,
                ['sequenceId' => $this->sequenceEntityAssociation->sequence_id],
            );
            $entityLog->status = SequenceEntityLog::STATUS_ERROR;
            $entityLog->save();

            return;
        }
        $owner = null;
        if ($entity instanceof Lead) {
            $owner = $customFields['lead-owner'] ?? null;
        } elseif ($entity instanceof Contact) {
            $owner = $customFields['contact-owner'] ?? null;
        }

        if (empty($owner)) {
            Log::error(
                'Owner not found for ' . $this->sequenceEntityAssociation->entity_type . ' with id ' .
                $this->sequenceEntityAssociation->entity_id,
                ['sequenceId' => $this->sequenceEntityAssociation->sequence_id],
            );
            $entityLog->status = SequenceEntityLog::STATUS_ERROR;
            $entityLog->save();

            return;
        }
        try {
            $rawMailer = new RawEmail($template->template, $template->name ?? '', $owner['email']);
            $mail = Mail::to($customFields['email']);

            $result = $mail->send($rawMailer);
            if (!$result) {
                $entityLog->status = SequenceEntityLog::STATUS_ERROR;
            } else {
                $entityLog->status = SequenceEntityLog::STATUS_SENT;
                $entityLog->sent_at = now();
            }
            $entityLog->save();
        } catch (Throwable $e) {
            Log::error(
                'Cant send message ' . $e->getMessage(),
                ['sequenceId' => $this->sequenceEntityAssociation->sequence_id],
            );
            $entityLog->status = SequenceEntityLog::STATUS_ERROR;
            $entityLog->save();
        }
    }
}
