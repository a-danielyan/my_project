<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\AccountDemo;
use App\Models\AccountPartnershipStatus;
use App\Models\AccountTraining;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactAuthority;
use App\Models\ContactType;
use App\Models\CustomField;
use App\Models\Email;
use App\Models\Sequence\Sequence;
use App\Models\Estimate;
use App\Models\Industry;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\LeadType;
use App\Models\License;
use App\Models\OauthToken;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\PaymentProfile;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Proposal;
use App\Models\Reminder;
use App\Models\Role;
use App\Models\SalesTax;
use App\Models\Solutions;
use App\Models\SolutionSet;
use App\Models\Stage;
use App\Models\SubjectLine;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\Template;
use App\Models\TermsAndConditions;
use App\Models\User;
use App\Policies\AccountDemoPolicy;
use App\Policies\AccountPartnershipPolicy;
use App\Policies\AccountPolicy;
use App\Policies\AccountTrainingPolicy;
use App\Policies\ActivityPolicy;
use App\Policies\ContactAuthorityPolicy;
use App\Policies\ContactPolicy;
use App\Policies\ContactTypePolicy;
use App\Policies\CustomFieldPolicy;
use App\Policies\EmailPolicy;
use App\Policies\SequencePolicy;
use App\Policies\EstimatePolicy;
use App\Policies\IndustryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LeadPolicy;
use App\Policies\LeadSourcePolicy;
use App\Policies\LeadStatusPolicy;
use App\Policies\LeadTypePolicy;
use App\Policies\LicensePolicy;
use App\Policies\OAuthPolicy;
use App\Policies\OpportunityPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PaymentProfilePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\ReminderPolicy;
use App\Policies\RolePolicy;
use App\Policies\SalesTaxPolicy;
use App\Policies\SolutionPolicy;
use App\Policies\SolutionSetPolicy;
use App\Policies\StagePolicy;
use App\Policies\SubjectLinePolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TagPolicy;
use App\Policies\TemplatePolicy;
use App\Policies\TermsAndConditionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Lead::class => LeadPolicy::class,
        CustomField::class => CustomFieldPolicy::class,
        Permission::class => PermissionPolicy::class,
        Industry::class => IndustryPolicy::class,
        Solutions::class => SolutionPolicy::class,
        LeadSource::class => LeadSourcePolicy::class,
        LeadStatus::class => LeadStatusPolicy::class,
        LeadType::class => LeadTypePolicy::class,
        Contact::class => ContactPolicy::class,
        Account::class => AccountPolicy::class,
        Stage::class => StagePolicy::class,
        Opportunity::class => OpportunityPolicy::class,
        Product::class => ProductPolicy::class,
        Estimate::class => EstimatePolicy::class,
        Invoice::class => InvoicePolicy::class,
        Activity::class => ActivityPolicy::class,
        Tag::class => TagPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        License::class => LicensePolicy::class,
        Payment::class => PaymentPolicy::class,
        AccountPartnershipStatus::class => AccountPartnershipPolicy::class,
        ContactAuthority::class => ContactAuthorityPolicy::class,
        ContactType::class => ContactTypePolicy::class,
        Template::class => TemplatePolicy::class,
        PaymentProfile::class => PaymentProfilePolicy::class,
        SalesTax::class => SalesTaxPolicy::class,
        AccountDemo::class => AccountDemoPolicy::class,
        AccountTraining::class => AccountTrainingPolicy::class,
        TermsAndConditions::class => TermsAndConditionPolicy::class,
        SolutionSet::class => SolutionSetPolicy::class,
        OauthToken::class => OAuthPolicy::class,
        Email::class => EmailPolicy::class,
        SubjectLine::class => SubjectLinePolicy::class,
        Reminder::class => ReminderPolicy::class,
        Proposal::class => ProposalPolicy::class,
        Sequence::class => SequencePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
