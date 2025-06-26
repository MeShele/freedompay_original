<?php

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

// Получаем последнюю сессию FreedomPay
$session = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'freedompay_sessions` ORDER BY id DESC LIMIT 1');

if (!$session) {
    die('❌ Нет сессий');
}

$cart_id = (int) $session['cart_id'];
$access_code = $session['session_token']; // <-- тут ключевое исправление

// Проверяем, существует ли уже заказ с этой корзиной
$orderExists = Db::getInstance()->getValue('SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` WHERE id_cart = ' . $cart_id);
if ($orderExists) {
    die('❗ Order already exists.');
}

// Загружаем корзину
$cart = new Cart($cart_id);
if (!Validate::isLoadedObject($cart)) {
    die('❌ Корзина не может быть загружена.');
}

// Получаем данные клиента
$customer = new Customer((int) $cart->id_customer);
if (!Validate::isLoadedObject($customer)) {
    die('❌ Клиент не найден.');
}

// Создаем заказ
$context = Context::getContext();
$context->cart = $cart;
$context->customer = $customer;

$total = $cart->getOrderTotal(true, Cart::BOTH);

// Получаем ID статуса "Полная оплата получена"
$order_state_id = Configuration::get('PS_OS_PAYMENT');

// Создаем заказ
$module = Module::getInstanceByName('freedompay');

if (!Validate::isLoadedObject($module)) {
    die('❌ Модуль freedompay не найден.');
}

$module->validateOrder(
    (int)$cart->id,
    (int)$order_state_id,
    (float)$total,
    'freedompay',
    null,
    [],
    (int)$cart->id_currency,
    false,
    $customer->secure_key
);

echo "✅ Заказ успешно создан для корзины #$cart_id с кодом доступа $access_code";
