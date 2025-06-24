<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FreedomPay extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'freedompay';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'FreedomPay';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'FreedomPay';
        $this->description = 'Freedom Pay - это международная компания специализирующаяся на платежных решениях для онлайн-бизнеса.';

        $this->confirmUninstall = 'Уверены, что хотите удалить расширение?';

        if (!Configuration::get('FREEDOMPAY_MODULE_NAME')) {
            $this->warning = 'Не указано название расширения';
        }
    }

    public function install(): bool
    {
        return (
            parent::install()
            && Configuration::updateValue('FREEDOMPAY_MODULE_NAME', 'FreedomPay')
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
        );
    }

    public function uninstall(): bool
    {
        return (
            parent::uninstall()
            && Configuration::deleteByName('FREEDOMPAY_MODULE_NAME')
        );
    }

    /**
     * @throws Exception
     */
    public function getContent(): void
    {
        $route = $this->get('router')->generate('configuration_form');

        Tools::redirectAdmin($route);
    }

    /**
     * @throws SmartyException
     */
    public function hookPaymentOptions(array $params): array
    {
        /** @var Cart $cart */
        $cart = $params['cart'];

        if (false === Validate::isLoadedObject($cart)) {
            return [];
        }

        $paymentOptions[] = $this->getExternalPaymentOption($cart);

        return $paymentOptions;
    }

    public function hookPaymentReturn(array $params): void
    {
    }

    /**
     * @throws SmartyException
     */
    private function getExternalPaymentOption(Cart $cart): PaymentOption
    {
        $externalOption = new PaymentOption();
        $externalOption->setModuleName($this->name);
        $externalOption->setCallToActionText($this->l('FreedomPay'));
        $externalOption->setAction($this->context->link->getModuleLink($this->name, 'payment'));

        return $externalOption;
    }
}
