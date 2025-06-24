<?php

declare(strict_types=1);

namespace PrestaShop\Module\FreedomPay\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

final class FreedomPayConfigurationDataConfiguration implements DataConfigurationInterface
{
    private ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        $config = [];

        $config['merchant_id'] = $this->configuration->get('merchant_id');
        $config['merchant_secret'] = $this->configuration->get('merchant_secret');
        $config['api_url'] = $this->configuration->get('api_url');
        $config['test_mode'] = (bool)$this->configuration->get('test_mode');
        $config['ofd'] = (bool)$this->configuration->get('ofd');
        $config['ofd_version'] = $this->configuration->get('ofd_version');
        $config['taxation_system'] = $this->configuration->get('taxation_system');
        $config['payment_method'] = $this->configuration->get('payment_method');
        $config['payment_object'] = $this->configuration->get('payment_object');
        $config['tax_type'] = $this->configuration->get('tax_type');
        $config['new_tax_type'] = $this->configuration->get('new_tax_type');
        $config['ofd_in_delivery'] = (bool)$this->configuration->get('ofd_in_delivery');
        $config['delivery_payment_object'] = $this->configuration->get('delivery_payment_object');
        $config['delivery_tax_type'] = $this->configuration->get('delivery_tax_type');
        $config['delivery_new_tax_type'] = $this->configuration->get('delivery_new_tax_type');
        $config['delivery_ikpu_code'] = $this->configuration->get('delivery_ikpu_code');
        $config['delivery_package_code'] = $this->configuration->get('delivery_package_code');
        $config['delivery_unit_code'] = $this->configuration->get('delivery_unit_code');

        return $config;
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('merchant_id', $configuration['merchant_id']);
            $this->configuration->set('merchant_secret', $configuration['merchant_secret']);
            $this->configuration->set('api_url', $configuration['api_url']);
            $this->configuration->set('test_mode', (bool)$configuration['test_mode']);
            $this->configuration->set('ofd', (bool)$configuration['ofd']);
            $this->configuration->set('ofd_version', $configuration['ofd_version']);
            $this->configuration->set('taxation_system', $configuration['taxation_system']);
            $this->configuration->set('payment_method', $configuration['payment_method']);
            $this->configuration->set('payment_object', $configuration['payment_object']);
            $this->configuration->set('tax_type', $configuration['tax_type']);
            $this->configuration->set('new_tax_type', $configuration['new_tax_type']);
            $this->configuration->set('ofd_in_delivery', (bool)$configuration['ofd_in_delivery']);
            $this->configuration->set('delivery_payment_object', $configuration['delivery_payment_object']);
            $this->configuration->set('delivery_tax_type', $configuration['delivery_tax_type']);
            $this->configuration->set('delivery_new_tax_type', $configuration['delivery_new_tax_type']);
            $this->configuration->set('delivery_ikpu_code', $configuration['delivery_ikpu_code']);
            $this->configuration->set('delivery_package_code', $configuration['delivery_package_code']);
            $this->configuration->set('delivery_unit_code', $configuration['delivery_unit_code']);
        }

        return $errors;
    }

    public function validateConfiguration(array $configuration): bool
    {
        return isset(
            $configuration['merchant_id'],
            $configuration['merchant_secret'],
            $configuration['api_url'],
            $configuration['test_mode'],
        );
    }
}
