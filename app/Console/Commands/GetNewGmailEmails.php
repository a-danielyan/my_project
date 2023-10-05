<?php

namespace App\Console\Commands;

use App\Http\Repositories\EmailRepository;
use App\Http\Services\Oauth2\Oauth2ServiceFactory;
use App\Models\OauthToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetNewGmailEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-new-gmail-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $emailRepository = resolve(EmailRepository::class);
        $oauthTokens = OauthToken::query()->where('service', OauthToken::SERVICE_NAME_GOOGLE_MAIL)->get();
        foreach ($oauthTokens as $oauthToken) {
            $gmailService = Oauth2ServiceFactory::createServiceFromToken($oauthToken, null);
            $tokenId = $oauthToken->getKey();
            $emails = $gmailService->getEmails();

            foreach ($emails as $email) {
                $email = array_merge($email, ['token_id' => $tokenId]);
                try {
                    $emailRepository->updateOrCreate(
                        ['email_id' => $email['email_id'], 'token_id' => $tokenId],
                        $email,
                    );
                } catch (Throwable $e) {
                    Log::error($e->getMessage());
                }
            }
        }
    }
}
