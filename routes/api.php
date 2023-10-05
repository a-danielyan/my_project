<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountDemoController;
use App\Http\Controllers\AccountTrainingController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\EntityLogController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\Oauth2Controller;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentProfileController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\RemindersController;
use App\Http\Controllers\SubjectLineController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ReferenceTables\AccountPartnershipController;
use App\Http\Controllers\ReferenceTables\ContactAuthorityController;
use App\Http\Controllers\ReferenceTables\ContactTypeController;
use App\Http\Controllers\ReferenceTables\IndustryController;
use App\Http\Controllers\ReferenceTables\LeadSourceController;
use App\Http\Controllers\ReferenceTables\LeadStatusController;
use App\Http\Controllers\ReferenceTables\LeadTypeController;
use App\Http\Controllers\ReferenceTables\SolutionController;
use App\Http\Controllers\ReferenceTables\StageController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SalesTaxController;
use App\Http\Controllers\SolutionSetController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TermsAndConditionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZohoController;
use App\Http\Middleware\CheckMailgunWebhook;
use App\Http\Middleware\ValidateCMSApiKey;
use App\Models\OauthToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1/')
    ->group(static function () {
        Route::post('socialLogin', [AuthController::class, 'loginWithSocialUser']);

        Route::middleware([
            'jwt.auth',
            'userStatus',
        ])->group(static function () {
            Route::apiResource('user', UserController::class)
                ->names('User');
            Route::apiResource('role', RolesController::class)->names('Role');
            Route::prefix('role/{role}')
                ->group(static function () {
                    Route::get('permission', [PermissionController::class, 'index']);
                    Route::put('permission', [PermissionController::class, 'update']);
                });
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/me', [AuthController::class, 'update']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::delete('/lead/bulk', [LeadController::class, 'bulkDeletes'])->name('lead.bulkDelete');
            Route::put('/lead/bulk', [LeadController::class, 'bulkUpdate'])->name('lead.bulkUpdate');
            Route::apiResource('lead', LeadController::class)->names('Lead');
            Route::post('/lead/{lead}/convertToContactAccount', [LeadController::class, 'convertToContactAccount']);
            Route::post('/lead/{lead}/restore', [LeadController::class, 'restoreItem']);
            Route::post('/lead/{lead}/apollo', [LeadController::class, 'getDataFromApollo']);

            Route::post('lead/{lead}/attachment', [LeadController::class, 'addAttachment']);
            Route::put('lead/{lead}/attachment/{attachment}', [LeadController::class, 'updateAttachment']);
            Route::delete('lead/{lead}/attachment/{attachment}', [LeadController::class, 'deleteAttachment']);

            Route::delete('/contact/bulk', [ContactController::class, 'bulkDeletes'])->name('contact.bulkDelete');
            Route::put('/contact/bulk', [ContactController::class, 'bulkUpdate'])->name('contact.bulkUpdate');
            Route::apiResource('contact', ContactController::class)->names('Contact');
            Route::post('/contact/{contact}/restore', [ContactController::class, 'restoreItem']);
            Route::post('/contact/{contact}/apollo', [ContactController::class, 'getDataFromApollo']);

            Route::post('contact/{contact}/attachment', [ContactController::class, 'addAttachment']);
            Route::put('contact/{contact}/attachment/{attachment}', [ContactController::class, 'updateAttachment']);
            Route::delete('contact/{contact}/attachment/{attachment}', [ContactController::class, 'deleteAttachment']);

            Route::apiResource('contactAuthority', ContactAuthorityController::class)
                ->names('contactAuthority');
            Route::apiResource('contactType', ContactTypeController::class)
                ->names('contactType');

            Route::delete('account/bulk', [AccountController::class, 'bulkDeletes'])->name('account.bulkDelete');
            Route::put('/account/bulk', [AccountController::class, 'bulkUpdate'])->name('account.bulkUpdate');
            Route::apiResource('account', AccountController::class)->names('Account');
            Route::post('/account/{account}/restore', [AccountController::class, 'restoreItem']);
            Route::post('/account/{account}/apollo', [AccountController::class, 'getDataFromApollo']);

            Route::apiResource('accountPartnership', AccountPartnershipController::class)
                ->names('accountPartnership');
            Route::post('account/{account}/attachment', [AccountController::class, 'addAttachment']);
            Route::put('account/{account}/attachment/{attachment}', [AccountController::class, 'updateAttachment']);
            Route::delete('account/{account}/attachment/{attachment}', [AccountController::class, 'deleteAttachment']);

            Route::apiResource('account.demo', AccountDemoController::class);
            Route::apiResource('account.training', AccountTrainingController::class);

            Route::apiResource('customField', CustomFieldController::class)->names('customField');
            Route::post('customField/bulk', [CustomFieldController::class, 'bulkUpdate']);
            Route::get('customFields/settings', [CustomFieldController::class, 'getSettings']);
            Route::apiResource('industry', IndustryController::class)->names('industry');
            Route::apiResource('solution', SolutionController::class)->names('solution');
            Route::apiResource('leadType', LeadTypeController::class)->names('leadType');
            Route::apiResource('leadSource', LeadSourceController::class)->names('leadSource');
            Route::apiResource('leadStatus', LeadStatusController::class)->names('leadStatus');
            Route::apiResource('stage', StageController::class)->names('stage');

            Route::delete('opportunity/bulk', [OpportunityController::class, 'bulkDeletes'])
                ->name('opportunity.bulkDelete');
            Route::put('/opportunity/bulk', [OpportunityController::class, 'bulkUpdate'])->name(
                'opportunity.bulkUpdate',
            );
            Route::apiResource('opportunity', OpportunityController::class)->names('Opportunity');

            Route::post('opportunity/{opportunity}/attachment', [OpportunityController::class, 'addAttachment']);

            Route::put(
                'opportunity/{opportunity}/attachment/{attachment}',
                [OpportunityController::class, 'updateAttachment'],
            );
            Route::delete(
                'opportunity/{opportunity}/attachment/{attachment}',
                [OpportunityController::class, 'deleteAttachment'],
            );

            Route::delete('product/bulk', [ProductController::class, 'bulkDeletes'])
                ->name('product.bulkDelete');
            Route::put('/product/bulk', [ProductController::class, 'bulkUpdate'])->name('product.bulkUpdate');
            Route::apiResource('product', ProductController::class)->names('Product');
            Route::post('/product/{product}/restore', [ProductController::class, 'restoreItem']);

            Route::post('product/{product}/attachment', [ProductController::class, 'addAttachment']);
            Route::put('product/{product}/attachment/{attachment}', [ProductController::class, 'updateAttachment']);
            Route::delete('product/{product}/attachment/{attachment}', [ProductController::class, 'deleteAttachment']);

            Route::delete('estimate/bulk', [EstimateController::class, 'bulkDeletes'])
                ->name('estimate.bulkDelete');
            Route::put('/estimate/bulk', [EstimateController::class, 'bulkUpdate'])->name('estimate.bulkUpdate');
            Route::apiResource('estimate', EstimateController::class)->names('Estimate');
            Route::post('estimate/{estimate}/preview', [EstimateController::class, 'previewEstimate'])
                ->name('estimate.preview');
            Route::post('estimate/{estimate}/pdf', [EstimateController::class, 'generatePdf'])
                ->name('estimate.pdf');
            Route::post('estimate/{estimate}/invoice', [EstimateController::class, 'createInvoice'])
                ->name('estimate.invoice');

            Route::post('estimate/{estimate}/attachment', [EstimateController::class, 'addAttachment']);
            Route::put(
                'estimate/{estimate}/attachment/{attachment}',
                [EstimateController::class, 'updateAttachment'],
            );
            Route::delete(
                'estimate/{estimate}/attachment/{attachment}',
                [EstimateController::class, 'deleteAttachment'],
            );


            Route::apiResource('activity', ActivityController::class)->names('Activity');

            Route::apiResource('subscription', SubscriptionController::class)->names('Subscription')
                ->only(['index', 'show']);

            Route::apiResource('invoice', InvoiceController::class)->names('Invoice')
                ->except(['destroy']);

            Route::post('invoice/{invoice}/attachment', [InvoiceController::class, 'addAttachment']);
            Route::post('invoice/{invoice}/send', [InvoiceController::class, 'sendInvoice']);
            Route::put('invoice/{invoice}/attachment/{attachment}', [InvoiceController::class, 'updateAttachment']);
            Route::delete('invoice/{invoice}/attachment/{attachment}', [InvoiceController::class, 'deleteAttachment']);
            Route::get('invoice/{invoice}/stripe', [InvoiceController::class, 'getStripeClientSecret']);
            Route::post(
                'invoice/{invoice}/authorize_net',
                [InvoiceController::class, 'getAuthorizeTransactionDetails'],
            );

            Route::apiResource('tag', TagController::class)->names('Tag');
            Route::apiResource('license', LicenseController::class)->names('License');
            Route::apiResource('payment', PaymentController::class)->names('Payment')
                ->except(['destroy']);
            Route::apiResource('payment_profile', PaymentProfileController::class)
                ->names('paymentProfile');

            Route::apiResource('template', TemplateController::class)->names('template');

            Route::apiResource('sales_tax', SalesTaxController::class)->names('salesTax');
            Route::apiResource('subject_line', SubjectLineController::class)->names('subjectLine');
            Route::apiResource('proposal', ProposalController::class)->only(['index', 'store', 'update'])->names(
                'proposal',
            );

            Route::get('/log/{entityType}/{entityId}', [EntityLogController::class, 'getLogs']);

            Route::prefix('oauth2')
                ->group(function () {
                    Route::get('/', [Oauth2Controller::class, 'index']);
                    Route::delete('{token}', [Oauth2Controller::class, 'destroy']);
                    Route::put('{token}', [Oauth2Controller::class, 'update']);
                    Route::post('{serviceType}', [Oauth2Controller::class, 'generateTokenByCode'])
                        ->whereIn('serviceType', [
                            OauthToken::SERVICE_NAME_GOOGLE_MAIL,
                        ]);
                });

            Route::get('config/findLocation', [ConfigController::class, 'findLocation']);
            Route::get('emails', [EmailController::class, 'index']);
            Route::get('emails/{email}', [EmailController::class, 'show']);
            Route::post('emails', [EmailController::class, 'send']);
            Route::apiResource('preference', PreferenceController::class)->names('preference');
            Route::apiResource('terms_and_conditions', TermsAndConditionsController::class)
                ->names('terms_and_condition');
            Route::apiResource('solution_set', SolutionSetController::class)
                ->names('solution_set');
            Route::apiResource('reminders', RemindersController::class)->names('reminders');
            Route::apiResource('sequence', SequenceController::class)->names('sequence');

            Route::get('dashboard', [DashboardController::class, 'index']);
        });

        Route::get('opportunity/{opportunity}/proposal', [OpportunityController::class, 'getOpportunityProposal']);
        Route::get(
            'opportunity/{opportunity}/proposalStats',
            [OpportunityController::class, 'getOpportunityProposalStats'],
        );

        Route::prefix('zoho/')
            ->group(static function () {
                Route::get('/oauthLink', [ZohoController::class, 'oauthLink']);
                Route::get('/authorization', [ZohoController::class, 'authorization']);
                Route::post('/notification', [ZohoController::class, 'notification']);
                Route::post('/runImportJob', [ZohoController::class, 'runImportJob']);
                Route::post('/downloadImportJob', [ZohoController::class, 'downloadImportJob']);
                Route::post('/importDownloadedZoho', [ZohoController::class, 'importDownloadedZoho']);
                Route::post('/convertImportedZoho', [ZohoController::class, 'convertImportedZoho']);
                Route::post('/runUserImport', [ZohoController::class, 'runUserImport']);
                Route::post('/enableNotification', [ZohoController::class, 'enableNotification']);
                Route::post('/getNotification', [ZohoController::class, 'getNotification']);
            });

        Route::prefix('mailgun/')
            ->group(static function () {
                Route::post('/webhook', [EmailController::class, 'mailgunWebhook'])->middleware(
                    CheckMailgunWebhook::class,
                );
            });

        Route::prefix('debug/')  //this data used for debug and check how prod worked
        ->group(static function () {
            Route::post('/makeApiBulk', [DebugController::class, 'makeApiBulk']);
            Route::post('/conversionStats', [DebugController::class, 'conversionStats']);
            Route::post('/checkZohoBooks', [DebugController::class, 'checkZohoBooks']);
            Route::post('/addProductBySku', [DebugController::class, 'addProductBySku']);
        });


        Route::prefix('cms/')
            ->group(static function () {
                Route::post('/device/sync', [DeviceController::class, 'syncDevice']);
            })->middleware(ValidateCMSApiKey::class);

        Route::middleware([
            'jwt.auth',
            'userStatus',
            'assistUser',
        ])->prefix('assist')->namespace('Assist')
            ->group(static function () {
                require __DIR__ . '/assist/assist.php';
            });

        Route::prefix('public')
            ->name('public.')
            ->middleware('CheckPublishToken')
            ->group(function () {
                Route::prefix('proposal')
                    ->name('proposal.')
                    ->group(function () {
                        Route::get('general', [ProposalController::class, 'getPublicProposal'])->name('general');
                    });
            });
    });
