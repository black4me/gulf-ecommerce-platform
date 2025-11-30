# Payment Gateway Integration Guide - Phase 2

## Supported Payment Gateways

### 1. PayTabs Integration

**Service Class:** `app/Services/Payment/PayTabsService.php`

**Features:**
- Credit/Debit card payments
- Multiple currency support (AED, SAR, KWD, QAR, OMR)
- Tokenization for saved cards
- Webhook notifications
- 3D Secure authentication
- Refund processing

**Configuration (.env):**
```
PAYTABS_API_KEY=your_api_key
PAYTABS_MERCHANT_ID=your_merchant_id
PAYTABS_ENDPOINT=https://secure.paytabs.com/api/gateway/payment_api
```

**Implementation Steps:**
1. Create PayTabsService class with payment methods
2. Implement PaymentController with PayTabs logic
3. Add webhook endpoint for payment notifications
4. Integrate with CheckoutController
5. Create payment status tracking
6. Implement refund functionality

**Key Methods:**
```php
class PayTabsService {
    public function initiatePayment($order): array
    public function verifyPayment($transactionId): bool
    public function refundPayment($transactionId, $amount): bool
    public function handleWebhook($payload): void
}
```

### 2. Telr Integration

**Service Class:** `app/Services/Payment/TelrService.php`

**Features:**
- Multi-currency payments
- Payment method flexibility
- Real-time transaction notifications
- Settlement tracking
- Advanced security features

**Configuration (.env):**
```
TELR_STORE_ID=your_store_id
TELR_API_KEY=your_api_key
TELR_ENDPOINT=https://api.telr.com/v1
```

**Key Methods:**
```php
class TelrService {
    public function createPayment($order): PaymentResponse
    public function confirmPayment($reference): bool
    public function refundPayment($paymentId, $amount): bool
    public function getTransactionStatus($reference): string
}
```

## Payment Flow

### Step 1: Order Creation
- User completes shopping cart
- Selects shipping method
- Chooses payment gateway
- Order created with status 'pending_payment'

### Step 2: Payment Initiation
- Checkout controller calls PaymentService
- Service prepares payment request
- Redirect to payment gateway
- Gateway displays payment form

### Step 3: Payment Processing
- Customer enters payment details
- Gateway processes transaction
- Returns success/failure response
- Customer redirected back to platform

### Step 4: Payment Verification
- Platform verifies transaction
- Updates order status
- Sends confirmation email
- Initiates order processing

### Step 5: Webhook Handling
- Payment gateway sends webhook
- Platform verifies webhook authenticity
- Updates order and payment records
- Triggers fulfillment process

## Database Schema

### Payments Table
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY,
    order_id BIGINT,
    user_id BIGINT,
    payment_method VARCHAR(50),
    gateway VARCHAR(50),
    amount DECIMAL(10,2),
    currency VARCHAR(3),
    status VARCHAR(20),
    transaction_id VARCHAR(100),
    reference_number VARCHAR(100),
    response JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Payment Transactions Table
```sql
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY,
    payment_id BIGINT,
    type VARCHAR(20),
    amount DECIMAL(10,2),
    status VARCHAR(20),
    gateway_response JSON,
    created_at TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id)
);
```

## Security Measures

1. **PCI Compliance**: Never store card data on platform
2. **SSL/TLS**: All payment communications encrypted
3. **Tokenization**: Store payment tokens for recurring payments
4. **Webhook Verification**: Validate gateway signatures
5. **Rate Limiting**: Prevent payment request abuse
6. **Logging**: Audit all payment transactions
7. **Error Handling**: Don't expose sensitive errors to users

## Refund Process

### Full Refund
```php
$paymentService->refund($order, $fullAmount);
// Updates order status to 'refunded'
// Processes refund through gateway
// Sends notification to customer
```

### Partial Refund
```php
$paymentService->refund($order, $partialAmount);
// Creates refund record
// Updates payment status
// Notifies customer
```

## Payment Status Codes

| Status | Meaning | Action |
|--------|---------|--------|
| pending | Awaiting payment | Wait for completion |
| processing | Being processed | Monitor status |
| completed | Successfully processed | Update inventory |
| failed | Payment declined | Notify customer |
| refunded | Refund initiated | Confirm with gateway |
| cancelled | Payment cancelled | Release order hold |

## Error Handling

**Common Errors:**
1. `INVALID_AMOUNT` - Order total doesn't match
2. `GATEWAY_ERROR` - Payment gateway unavailable
3. `AUTHENTICATION_FAILED` - Invalid credentials
4. `PAYMENT_DECLINED` - Card declined
5. `TIMEOUT` - Request timeout

**Resolution:**
- Log error with transaction ID
- Notify admin for investigation
- Provide generic error to customer
- Retry mechanism for temporary failures
- Manual intervention process for disputes

## Testing

### Sandbox Credentials
- PayTabs Sandbox: Available in PayTabs dashboard
- Telr Sandbox: Available in Telr merchant portal

### Test Cards
```
Visa: 4111 1111 1111 1111 (Any future date)
Mastercard: 5555 5555 5555 4444
Expiry: 12/25
CVV: 123
```

### Test Cases
1. Successful payment
2. Declined payment
3. Timeout handling
4. Webhook notification
5. Refund processing
6. Currency conversion
7. Multi-vendor commission split

## Multi-Vendor Commission Handling

When order contains multiple vendors:

1. **Total Amount**: Sum of all items + tax + shipping
2. **Payment Processing**: Single payment for total
3. **Commission Calculation**: By vendor
4. **Settlement**: Separate payouts to each vendor
5. **Platform Commission**: 15-20% (configurable)

**Example:**
- Order Total: $100
- Vendor A: $40 (Commission: $6)
- Vendor B: $50 (Commission: $7.50)
- Platform: $10

## Deployment Checklist

- [ ] API keys configured in production .env
- [ ] SSL certificates installed
- [ ] Webhook endpoints configured
- [ ] Database migrations executed
- [ ] Payment tests passed
- [ ] Refund process tested
- [ ] Error logging configured
- [ ] Admin notifications set up
- [ ] Customer communications tested
- [ ] Disaster recovery plan documented

---

**Status:** Ready for implementation
**Priority:** Critical
**Estimated Timeline:** 2-3 weeks
