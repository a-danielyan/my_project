<?php

namespace App\Console\Commands;

use App\Jobs\SendSequenceEmail;
use App\Models\Sequence\Sequence;
use App\Models\Sequence\SequenceEntityAssociation;
use App\Models\Sequence\SequenceTemplateAssociation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandlerSequence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-email-sequence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle email sequence logic';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sequenceToWork = Sequence::query()->where('is_active', true)
            ->where('start_date', '<', now()->format('Y-m-d'))->get();

        foreach ($sequenceToWork as $sequence) {
            $templatesToWork = $sequence->templatesAssociation;

            $previousTemplate = null;

            /** @var SequenceTemplateAssociation $templateAssociation */
            foreach ($templatesToWork as $templateAssociation) {
                //  $userToSend = $sequence->entityRelation->whereDoesntHave('logRecord');
                $userToSend = SequenceEntityAssociation::query()->where('sequence_id', $sequence->getKey())
                    ->whereDoesntHave('logRecord')->get();

                foreach ($userToSend as $user) {
                    if (empty($previousTemplate)) {
                        //we can send
                        SendSequenceEmail::dispatch($user, $templateAssociation);
                    } else {
                        //Get date sent previous email
                        $previousSentLog = $user->logRecord()->where('email_template_id', $previousTemplate->getKey())
                            ->first();

                        if ($templateAssociation->send_after_unit == Sequence::SEND_AFTER_UNIT_DAY) {
                            $emailCanSentAt = $previousSentLog->sent_at->addDays($templateAssociation->send_after);
                        } elseif ($templateAssociation->send_after_unit == Sequence::SEND_AFTER_UNIT_MONTH) {
                            $emailCanSentAt = $previousSentLog->sent_at->addMonths($templateAssociation->send_after);
                        } else {
                            Log::error('Unknown send after unit ' . $templateAssociation->send_after_unit, [
                                'sequenceId' => $sequence->getKey(),
                                'templateAssociationId' => $templateAssociation->getKey(),
                            ]);
                            continue;
                        }

                        if (now() > $emailCanSentAt) {
                            SendSequenceEmail::dispatch($user, $templateAssociation);
                        }
                    }
                }
                $previousTemplate = $templateAssociation->template;
            }
        }
    }
}
