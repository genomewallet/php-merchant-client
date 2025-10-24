<?php

namespace Genome\Merchant;

/**
 * Mutable container of payment data to be initialized.
 */
class HostedPayment
{
    // Mandatory fields
    const ORDER_ID = 'order_id';
    const USER_ID = 'user_id';
    const AMOUNT = 'amount';
    const CURRENCY = 'currency_iso';
    const MCC = 'mcc';

    // Nonce
    const TS_NONCE = 'ts_nonce';

    // Optional URLs
    const SUCCESS_URL = 'success_url';
    const FAILURE_URL = 'failure_url';

    // Additional fields
    const DESCRIPTION = 'description';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';

    // Options
    const SHOW_PHONE = 'show_phone';
    const SHOW_EMAIL = 'show_email';
    const SHOW_DESCRIPTION = 'show_description';
    const SHOW_GDPR_AGREEMENT = 'show_gdpr_agreement';

    /**
     * @var array Hosted payment data.
     */
    private $data;

    /**
     * @var array Custom data.
     */
    private $custom = [];

    /**
     * Constructor
     * Creates payment object with minimal data.
     *
     * @param string $orderId User identifier on merchant side.
     * @param string $userId Unique order identifier on merchant side.
     * @param string $mcc Operation MCC code.
     * @param string $currency Payment currency ISO A3 code.
     * @param float $amount Payment amount.
     */
    public function __construct(
        string $orderId,
        string $userId,
        string $mcc,
        string $currency,
        float  $amount
    )
    {
        $this->data = [
            self::ORDER_ID => $orderId,
            self::USER_ID => $userId,
            self::MCC => $mcc,
            self::CURRENCY => strtoupper($currency),
            self::AMOUNT => $amount,
        ];
    }

    private function set(string $key, $value): HostedPayment
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function addCustom(array $custom): HostedPayment
    {
        foreach ($custom as $key => $value) {
            $this->addCustomKeyValue($key, $value);
        }
        return $this;
    }

    public function addCustomKeyValue(string $key, string $value): HostedPayment
    {
        $len = strlen($key);
        if ($len < 8 || substr($key, 0, 7) !== 'custom_') {
            throw new \InvalidArgumentException('Custom value key should start with "custom_", ' . $key . ' given');
        }

        $this->custom[$key] = $value;
        return $this;
    }

    public function buildQuery(): string
    {
        $chunks = [];
        $reflection = new \ReflectionClass(HostedPayment::class);
        $constants = $reflection->getConstants();
        foreach ($constants as $value) {
            if (isset($this->data[$value])) {
                if (is_bool($this->data[$value])) {
                    $chunks[$value] = $this->data[$value] ? 'true' : 'false';
                } else {
                    $chunks[$value] = $this->data[$value];
                }
            }
        }
        $chunks = array_merge($chunks, $this->custom);

        return http_build_query($chunks);
    }

    public function getOrderId(): string
    {
        return $this->data[self::ORDER_ID];
    }

    public function getUserId(): string
    {
        return $this->data[self::USER_ID];
    }

    public function getMcc(): string
    {
        return $this->data[self::MCC];
    }

    public function setTsNonceAutomatically(): HostedPayment
    {
        return $this->setTsNonce(strval(time()));
    }

    public function setTsNonce(int $nonce): HostedPayment
    {
        return $this->set(self::TS_NONCE, $nonce);
    }

    public function hasTsNonce(): bool
    {
        return isset($this->data[self::TS_NONCE]);
    }

    public function getTsNonce(): int
    {
        return $this->data[self::TS_NONCE];
    }

    public function getCurrency(): string
    {
        return $this->data[self::CURRENCY];
    }

    public function getAmount(): float
    {
        return $this->data[self::AMOUNT];
    }

    public function setRedirectUrls(string $successUrl, string $failureUrl): HostedPayment
    {
        return $this->set(self::SUCCESS_URL, $successUrl)->set(self::FAILURE_URL, $failureUrl);
    }

    public function getSuccessRedirectUrl(): string
    {
        return $this->data[self::SUCCESS_URL];
    }

    public function getFailureRedirectUrl(): string
    {
        return $this->data[self::FAILURE_URL];
    }

    public function setDescription(string $description): HostedPayment
    {
        return $this->set(self::DESCRIPTION, $description);
    }

    public function getDescription(): string
    {
        return $this->data[self::DESCRIPTION];
    }

    public function setPhone(string $phone): HostedPayment
    {
        return $this->set(self::PHONE, $phone);
    }

    public function getPhone(): string
    {
        return $this->data[self::PHONE];
    }

    public function setEmail(string $email): HostedPayment
    {
        return $this->set(self::EMAIL, $email);
    }

    public function getEmail(): string
    {
        return $this->data[self::EMAIL];
    }

    public function setFirstLastName(string $firstName, string $lastName): HostedPayment
    {
        return $this->set(self::FIRST_NAME, $firstName)->set(self::LAST_NAME, $lastName);
    }

    public function getFirstName(): string
    {
        return $this->data[self::FIRST_NAME];
    }

    public function getLastName(string $lastName): string
    {
        return $this->data[self::LAST_NAME];
    }

    public function setShowPhone(bool $showPhone): HostedPayment
    {
        return $this->set(self::SHOW_PHONE, $showPhone);
    }

    public function setShowEmail(bool $show): HostedPayment
    {
        return $this->set(self::SHOW_EMAIL, $show);
    }

    public function setShowGdprAgreement(bool $show): HostedPayment
    {
        return $this->set(self::SHOW_GDPR_AGREEMENT, $show);
    }

    public function setShowDescription(bool $show): HostedPayment
    {
        return $this->set(self::SHOW_DESCRIPTION, $show);
    }
}