<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Email\EmailGetRequest;
use App\Http\Requests\Email\EmailSentRequest;
use App\Http\RequestTransformers\Email\EmailGetSortTransformer;
use App\Http\RequestTransformers\Email\EmailSentTransformer;
use App\Http\Resource\EmailResource;
use App\Http\Services\EmailService;
use App\Jobs\UpdateEmailStatus;
use App\Models\Email;
use App\Models\MailgunNotificationRawData;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function __construct(private EmailService $service)
    {
    }

    /**
     * @param EmailGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function index(EmailGetRequest $request): JsonResponse
    {
        $this->authorize('getEmails', [Email::class]);

        return response()->json(
            $this->service->getAll(
                (new EmailGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Email $email
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Email $email): JsonResponse
    {
        $this->authorize('showEmail', [$email]);

        return response()->json($this->service->show($email));
    }

    /**
     * @param EmailSentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function send(EmailSentRequest $request): JsonResponse
    {
        $this->authorize('sendEmail', [Email::class]);

        return response()->json(
            new EmailResource(
                $this->service->send((new EmailSentTransformer())->map($request->validated()), $this->getUser())
            ),
        );
    }

    public function mailgunWebhook(Request $request): JsonResponse
    {
        $notification = MailgunNotificationRawData::query()->create([
            'raw_data' => $request->get('event-data'),
        ]);
        UpdateEmailStatus::dispatch($notification);

        return response()->json();
    }
}
