<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use App\Helpers\CommonHelper as Help;
use Psr\Log\LogLevel;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     */
    protected $levels = [
        TokenBlacklistedException::class => LogLevel::INFO,
    ];

    /**
     * A list of the exception types that are not reported.
     *
     */
    protected $dontReport = [
        AuthorizationException::class,
        AuthenticationException::class,
        ModelNotFoundException::class,
        PostTooLargeException::class,
        ValidationException::class,
        UnauthorizedException::class,
        NotFoundHttpException::class,
        UnauthorizedHttpException::class,
        CustomErrorException::class,
        CredentialInvalidErrorException::class,
        AccountLockedErrorException::class,
        FileNotFoundException::class,
        ConvertLeadErrorException::class,
        ChangedAddressErrorException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        switch (true) {
            case ($e instanceof AuthenticationException):
                if (Arr::first($e->guards()) === 'device') {
                    $responseArray['message'] = 'Unauthorized device! Please connect.';
                } else {
                    $responseArray['message'] = 'Unauthorized user! Please login.';
                }

                return response()->json($responseArray, 401);
            case ($e instanceof UnauthorizedHttpException):
                $responseArray['message'] = 'Access denied';

                return response()->json($responseArray, 401);
            case ($e instanceof AuthorizationException
                || $e instanceof UnauthorizedException):
                $responseArray = [];

                $responseArray['message'] = 'Access denied';

                return response()->json($responseArray, 403);
            case ($e instanceof ConvertLeadErrorException):
            case ($e instanceof ChangedAddressErrorException):
            case ($e instanceof CustomErrorException):
                $responseArray = [];

                $responseArray['exception'] = class_basename($e);

                if ($e->getMessage()) {
                    $responseArray['message'] = $e->getMessage();
                }

                if (method_exists($e, 'getData')) {
                    $responseArray['data'] = $e->getData();
                }

                return response()->json($responseArray, $e->getCode() ?? 400);

            case ($e instanceof NotFoundHttpException):
                return response()->json(['message' => 'Resource not found!'], 404);

            case ($e instanceof ModelNotFoundException):
                $modelName = Help::modelName($e->getModel());

                return response()->json(['message' => 'Requested ' . $modelName . ' not found'], 404);

            case ($e instanceof ValidationException):
                return response()->json(
                    [
                        'errors' => $e->errors(),
                        'message' => 'The given data was invalid.',
                    ],
                    $e->status,
                );

            case ($e instanceof PostTooLargeException):
                return response()->json(
                    [
                        'message' => 'Exceeded max allowed upload data',
                    ],
                    413,
                );

            default:
                break;
        }

        return parent::render($request, $e);
    }
}
