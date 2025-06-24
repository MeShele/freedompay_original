<?php

use JetBrains\PhpStorm\NoReturn;

class FreedomPayPaymentModuleFrontController extends ModuleFrontController
{
    private const API_SOURCE_PARAM = 'source:cms_prestashop';

    private Customer $customer;
    private Address $address;
    private $products;

    /**
     * @throws Exception
     */
    #[NoReturn] public function postProcess(): void
    {

        $this->customer = new Customer($this->context->cart->id_customer);
        $this->address = new Address($this->context->cart->id_address_delivery);

        $this->module->validateOrder(
            (int)$this->context->cart->id,
            (int)Configuration::get('PS_OS_CHEQUE'),
            (float)$this->context->cart->getOrderTotal(),
            'FreedomPay',
            null,
            [
                'transaction_id' => Tools::passwdGen(),
            ],
            (int)$this->context->currency->id,
            false,
            $this->customer->secure_key
        );

        $paymentData = $this->generatePaymentData();

        $response = $this->post(
            Configuration::get('api_url') . '/init_payment.php',
            $this->getRequestData($paymentData)
        );

        Tools::redirect($this->getRedirectUrlFromXmlResponse($response));
    }

    private function generatePaymentData(): array
    {
        $order = Order::getByCartId((int)$this->context->cart->id);

        if ($order === null) {
            throw new RuntimeException();
        }

        $paymentData['pg_salt'] = uniqid('', true);
        $paymentData['pg_merchant_id'] = (int)Configuration::get('merchant_id');
        $paymentData['pg_order_id'] = (string)$order->id;
        $paymentData['pg_amount'] = (float)number_format($this->context->cart->getCartTotalPrice(), 2);
        $paymentData['pg_description'] = 'Order №' . $order->id;
        $paymentData['pg_user_contact_email'] = $this->customer->email;
        $paymentData['pg_success_url'] = $this->context->link->getPageLink(
            'order-confirmation',
            true,
            $this->context->language->id,
            [
                'id_cart' => (int) $this->context->cart->id,
                'id_module' => (int) $this->module->id,
                'id_order' => (int) $this->module->currentOrder,
                'key' => $this->customer->secure_key,
            ]
        );
        $paymentData['pg_result_url'] = $this->context->link->getModuleLink($this->module->name,  'callback',  [], true);
        $paymentData['pg_currency'] = $this->context->currency->iso_code;
        $paymentData['pg_param1'] = self::API_SOURCE_PARAM;

        if (!empty($this->getPhone())) {
            $paymentData['pg_user_phone'] = $this->getPhone();
        }

        if (Configuration::get('ofd')) {
            $this->products = $this->context->cart->getProducts(true);

            $ofdVersion = Configuration::get('ofd_version');

            switch ($ofdVersion) {
                case 'old_ru_1_05':
                    $paymentData['pg_receipt_positions'] = $this->getReceiptPositionsForDeprecatedOfd();
                    break;
                case 'uz_1_0':
                    $paymentData['pg_receipt'] = $this->getReceiptForGnk();
                    break;
                default:
                    $paymentData['pg_receipt'] = $this->getReceipt();
            }
        }

        return $paymentData;
    }

    private function getPhone(): string
    {
        $phone = !empty($this->address->phone_mobile) ? $this->address->phone_mobile : $this->address->phone;

        return preg_replace('/\D/', '', $phone);
    }

    private function getReceiptPositionsForDeprecatedOfd(): array
    {
        $receiptPositions = [];

        foreach ($this->products as $product) {
            $receiptPosition = [
                'name'     => $product['name'],
                'tax_type' => Configuration::get('tax_type'),
                'count'    => (int)$product['quantity'],
                'price'    => (float)number_format($product['price'], 2),
            ];

            $receiptPositions[] = $receiptPosition;
        }

        $deliveryPrice = $this->context->cart->getPackageShippingCost();

        if ($deliveryPrice > 0 && Configuration::get('ofd_in_delivery')) {
            $receiptPosition = [
                'count'    => 1,
                'name'     => 'Доставка',
                'tax_type' => Configuration::get('delivery_tax_type'),
                'price'    => $deliveryPrice,
            ];

            $receiptPositions[] = $receiptPosition;
        }

        return $receiptPositions;
    }

    private function getReceiptForGnk(): array
    {
        $receipt = [];

        $receipt['receipt_format'] = 'uz_1_0';
        $receipt['positions'] = $this->getReceiptPositionsForGnk();

        return $receipt;
    }

    private function getReceipt(): array
    {
        $receipt = [];

        $receipt['receipt_format'] = Configuration::get('ofd_version');
        $receipt['operation_type'] = Configuration::get('taxation_system')('TAXATION_SYSTEM');
        $receipt['customer'] = $this->getReceiptCustomer();
        $receipt['positions'] = $this->getReceiptPositionsForNewOfd();

        return $receipt;
    }

    private function getReceiptPositionsForGnk(): array
    {
        $receiptPositions = [];

        foreach ($this->products as $product) {
            $receiptPosition = [
                'quantity'     => (int)$product['quantity'],
                'name'         => $product['name'],
                'vat_code'     => Configuration::get('new_tax_type'),
                'price'        => (float)number_format($product['price'], 2),
                'ikpu_code'    => 132,
                'package_code' => 123,
                'unit_code'    => 123,
            ];

            $receiptPositions[] = $receiptPosition;
        }

        $deliveryPrice = $this->context->cart->getPackageShippingCost();

        if ($deliveryPrice > 0 && Configuration::get('ofd_in_delivery')) {
            $receiptPosition = [
                'quantity'     => 1,
                'name'         => 'Доставка',
                'vat_code'     => Configuration::get('delivery_new_tax_type'),
                'price'        => $deliveryPrice,
                'ikpu_code'    => Configuration::get('delivery_ikpu_code'),
                'package_code' => Configuration::get('delivery_package_code'),
                'unit_code'    => Configuration::get('delivery_unit_code'),
            ];

            $receiptPositions[] = $receiptPosition;
        }

        return $receiptPositions;
    }

    private function getReceiptCustomer(): array
    {
        $customer = [];

        $email = $this->customer->email;

        if (!empty($email)) {
            $customer['email'] = $this->customer->email;
        }

        $phone = $this->getPhone();

        if (!empty($phone)) {
            $customer['phone'] = $phone;
        }

        if (empty($email) && empty($phone)) {
            throw new RuntimeException();
        }

        return $customer;
    }

    private function getReceiptPositionsForNewOfd(): array
    {
        $receiptPositions = [];

        foreach ($this->products as $product) {
            $receiptPosition = [
                'quantity'       => (int)$product['quantity'],
                'name'           => $product['name'],
                'vat_code'       => Configuration::get('new_tax_type'),
                'price'          => (float)number_format($product['price'], 2),
                'payment_method' => Configuration::get('payment_method'),
                'payment_object' => Configuration::get('payment_object'),
            ];

            if (Configuration::get('ofd_version') === 'ru_1_2') {
                $receiptPosition['measure'] = Configuration::get('measure');
            }

            $receiptPositions[] = $receiptPosition;
        }

        $deliveryPrice = $this->context->cart->getPackageShippingCost();

        if ($deliveryPrice > 0 && Configuration::get('ofd_in_delivery')) {
            $receiptPosition = [
                'quantity'       => 1,
                'name'           => 'Доставка',
                'vat_code'       => Configuration::get('delivery_new_tax_type'),
                'price'          => $deliveryPrice,
                'payment_method' => Configuration::get('delivery_payment_method'),
                'payment_object' => Configuration::get('delivery_payment_object'),
            ];

            if (Configuration::get('ofd_version') === 'ru_1_2') {
                $receiptPosition['measure'] = 'piece';
            }

            $receiptPositions[] = $receiptPosition;
        }

        return $receiptPositions;
    }

    private function post(string $url, string $postData): bool|string
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $postData
        );

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function getRedirectUrlFromXmlResponse(string $responseBody): string
    {
        $responseXml = simplexml_load_string($responseBody);

        if (!$responseXml instanceof SimpleXMLElement || empty($responseXml->pg_redirect_url)) {
            throw new RuntimeException();
        }

        return (string)$responseXml->pg_redirect_url;
    }

    private function getRequestData(array $requestArray): string
    {
        $data = $this->prepareRequestData($requestArray);
        ksort($data);
        array_unshift($data, 'init_payment.php');
        $data[] = Configuration::get('merchant_secret');
        $str = implode(';', $data);
        $requestArray['pg_sig'] = md5($str);

        return json_encode($requestArray, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function prepareRequestData($data): array
    {
        if (!is_array($data)) {
            return $data;
        }

        $result = [];
        $i = 0;

        foreach ($data as $key => $val) {
            $name = ((string)$key) . sprintf('%03d', ++$i);

            if (is_array($val)) {
                $result = array_merge($result, $this->prepareRequestData($val, $name));

                continue;
            }

            $result += [$name => (string)$val];
        }

        return $result;
    }
}
