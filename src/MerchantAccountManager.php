<?php

namespace Genome\Merchant;

/**
 * General manager for merchant account activity.
 */
class MerchantAccountManager
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * Constructor.
     *
     * @param string $login Merchant login.
     * @param string $password Merchant password.
     * @param Environment|null $environment Environment settings.
     */
    public function __construct(string $login, string $password, Environment $environment = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->environment = is_null($environment) ? Environment::getProduction() : $environment;
    }

    /**
     * @return Environment Environment settings.
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @return string Merchant account login.
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Constructs and returns hosted payment page manager.
     *
     * @param string $api_key Hosted payment page API key.
     * @param string $api_secret Hosted payment page secret.
     * @param string $redirect_signature_mode Redirect signature mode ('MODE_A', 'MODE_A_TS')
     * @return HostedPaymentPageManager.
     */
    public function getHostedPaymentPageManager(
        string $api_key,
        string $api_secret,
        string $redirect_signature_mode = HostedPaymentPageManager::SIGNATURE_MODE_A_TS
    ): HostedPaymentPageManager
    {
        return new HostedPaymentPageManager($this, $api_key, $api_secret, $redirect_signature_mode);
    }
}