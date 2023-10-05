<?php

namespace App\Http\Services;

use App\DTO\StripeCustomerDTO;
use App\DTO\StripeProductDTO;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;
use Stripe\Price;
use Stripe\Product;
use Stripe\Quote;
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.api_secret'));
    }

    //@todo convert account to Zoho Customer

    /**
     * @param StripeCustomerDTO $stripeCustomerDTO
     * @return Customer
     * @throws ApiErrorException
     */
    public function createCustomer(StripeCustomerDTO $stripeCustomerDTO): Customer
    {
        return $this->stripe->customers->create($stripeCustomerDTO->toStripeArray());
    }

    /**
     * @param StripeProductDTO $stripeProductDTO
     * @return Product
     * @throws ApiErrorException
     */
    public function createProduct(StripeProductDTO $stripeProductDTO): Product
    {
        return $this->stripe->products->create($stripeProductDTO->toStripeArray());
    }

    /**
     * @param string $customerId
     * @param array $lineItems
     * @param ?int $expiresAt
     * @return Quote
     * @throws ApiErrorException
     */
    public function createQuote(string $customerId, array $lineItems, ?int $expiresAt): Quote
    {
        return $this->stripe->quotes->create([
            'customer' => $customerId,
            'line_items' => $lineItems,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * @param string $quoteId
     * @param array $lineItems
     * @param int $expiresAt
     * @return Quote
     * @throws ApiErrorException
     */
    public function updateQuote(string $quoteId, array $lineItems, int $expiresAt): Quote
    {
        return $this->stripe->quotes->update($quoteId, [
            'line_items' => $lineItems,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * @param string $quoteId
     * @return Quote
     * @throws ApiErrorException
     */
    public function finalizeQuote(string $quoteId): Quote
    {
        return $this->stripe->quotes->finalizeQuote($quoteId);
    }

    /**
     * @param string $quoteId
     * @return Quote
     * @throws ApiErrorException
     */
    public function acceptQuote(string $quoteId): Quote
    {
        return $this->stripe->quotes->accept($quoteId);
    }

    /**
     * @param string $invoiceId
     * @return Invoice
     * @throws ApiErrorException
     */
    public function finalizeInvoice(string $invoiceId): Invoice
    {
        return $this->stripe->invoices->finalizeInvoice($invoiceId, ['expand' => ['payment_intent']]);
    }

    /**
     * @param string $invoiceId
     * @return Invoice
     * @throws ApiErrorException
     */
    public function getInvoice(string $invoiceId): Invoice
    {
        return $this->stripe->invoices->retrieve($invoiceId, ['expand' => ['payment_intent']]);
    }

    /**
     * @param string $customerId
     * @param array $lineItems
     * @return Subscription
     * @throws ApiErrorException
     */
    public function createSubscription(string $customerId, array $lineItems): Subscription
    {
        return $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => $lineItems,
            'payment_behavior' => 'default_incomplete',

        ]);
    }

    /**
     * @param string $customerId
     * @return Invoice
     * @throws ApiErrorException
     */
    public function createInvoice(string $customerId): Invoice
    {
        return $this->stripe->invoices->create([
            'customer' => $customerId,
        ]);
    }

    /**
     * @param string $customerId
     * @param string $price
     * @return void
     * @throws ApiErrorException
     */
    public function createInvoiceItem(string $customerId, string $price): void
    {
        $this->stripe->invoiceItems->create([
            'customer' => $customerId,
            'price' => $price,
        ]);
    }

    /**
     * @param array $priceData
     * @return Price
     * @throws ApiErrorException
     */
    public function createPrice(array $priceData): Price
    {
        return $this->stripe->prices->create($priceData);
    }
}
