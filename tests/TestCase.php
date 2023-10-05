<?php

namespace Tests;

use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use AuthTrait;
    use DatabaseTransactions;

    public const PRODUCT_ID_WITH_PRICE_10=21;
    public const PRODUCT_ID_WITH_PRICE_20=22;
    public const PRODUCT_ID_WITH_PRICE_100=23;

    public const STATE_WITH_TAXES='WY';
    public const STATE_TAXES_PERCENT=30;
    public const CONTACT_WITH_TAXES_ID=25;

    public const LEAD_ROUTE = '/api/v1/lead';
    public const OPPORTUNITY_ROUTE = '/api/v1/opportunity';
    public const CONTACT_ROUTE = '/api/v1/contact';
    public const ROLE_ROUTE = '/api/v1/role';
    public const USER_ROUTE = '/api/v1/user';
    public const CUSTOM_FIELD_ROUTE = '/api/v1/customField';
    public const INDUSTRY_ROUTE = '/api/v1/industry';
    public const LEAD_SOURCE_ROUTE = '/api/v1/leadSource';
    public const LEAD_TYPE_ROUTE = '/api/v1/leadType';
    public const LEAD_STATUS_ROUTE = '/api/v1/leadStatus';
    public const STAGE_ROUTE = '/api/v1/stage';
    public const PRODUCT_ROUTE = '/api/v1/product';
    public const ESTIMATE_ROUTE = '/api/v1/estimate';
    public const ACTIVITY_ROUTE = '/api/v1/activity';
    public const TAG_ROUTE = '/api/v1/tag';
    public const ME_ROUTE = '/api/v1/me';
    public const REFRESH_ROUTE = '/api/v1/refresh';
    public const LOGOUT_ROUTE = '/api/v1/logout';
    public const SUBSCRIPTION_ROUTE = '/api/v1/subscription';
    public const LICENSE_ROUTE = '/api/v1/license';
    public const PAYMENT_ROUTE = '/api/v1/payment';
    public const SOLUTION_ROUTE = '/api/v1/solution';
    public const ACCOUNT_ROUTE = '/api/v1/account';
    public const ACCOUNT_PARTNERSHIP_ROUTE = '/api/v1/accountPartnership';
    public const CONTACT_AUTHORITY_ROUTE = '/api/v1/contactAuthority';
    public const CONTACT_TYPE_ROUTE = '/api/v1/contactType';
    public const LOG_ROUTE = '/api/v1/log';
    public const INVOICE_ROUTE = '/api/v1/invoice';
    public const CMS_DEVICE_SYNC_ROUTE = '/api/v1/cms/device/sync';
    public const ZOHO_ROUTE = 'api/v1/zoho';
    public const TEMPLATE_ROUTE = 'api/v1/template';
    public const SALES_TAX_ROUTE = 'api/v1/sales_tax';
    public const PAYMENT_PROFILE_ROUTE = 'api/v1/payment_profile';
    public const PREFERENCE_ROUTE = 'api/v1/preference';
    public const TERMS_AND_CONDITIONS_ROUTE = 'api/v1/terms_and_conditions';
    public const SOLUTION_SET_ROUTE = 'api/v1/solution_set';
    public const EMAIL_ROUTE = '/api/v1/emails';
    public const OAUTH_ROUTE = '/api/v1/oauth2';
    public const FIND_LOCATION_ROUTE = '/api/v1/config/findLocation';
    public const SUBJECT_LINE_ROUTE = '/api/v1/subject_line';
    public const REMINDER_ROUTE = '/api/v1/reminders';
    public const SEQUENCE_ROUTE = '/api/v1/sequence';


    /**
     * @return void
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->authorize(TestDatabaseSeeder::TEST_ADMIN_USER_EMAIL);
        Cache::flush();
    }
}
