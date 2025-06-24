<?php

declare(strict_types=1);

namespace PrestaShop\Module\FreedomPay\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class FreedomPayConfigurationFormDataProvider implements FormDataProviderInterface
{
    private DataConfigurationInterface $configurationDataConfiguration;

    public function __construct(DataConfigurationInterface $configurationDataConfiguration)
    {
        $this->configurationDataConfiguration = $configurationDataConfiguration;
    }

    public function getData(): array
    {
        return $this->configurationDataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->configurationDataConfiguration->updateConfiguration($data);
    }
}
