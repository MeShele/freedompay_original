<?php

declare(strict_types=1);

namespace PrestaShop\Module\FreedomPay\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FreedomPayConfigurationFormType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('merchant_id', TextType::class, [
                'label' => 'Идентификатор магазина',
            ])->add('merchant_secret', TextType::class, [
                'label' => 'Секретный ключ',
            ])->add('api_url', ChoiceType::class, [
                'label'   => 'API URL платежной системы',
                'choices' => [
                    'api.freedompay.uz' => 'https://api.freedompay.uz',
                    'api.freedompay.kg' => 'https://api.freedompay.kg',
                    'api.freedompay.kz' => 'https://api.freedompay.kz',
                ],
            ])->add('test_mode', CheckboxType::class, [
                'label'    => 'Тестовый режим',
                'required' => false,
            ])->add('ofd', CheckboxType::class, [
                'label'    => 'ФФД',
                'required' => false,
            ])->add('ofd_version', ChoiceType::class, [
                'label'   => 'Версия ФФД',
                'choices' => [
                    'old_ru_1_05' => 'ФФД v1',
                    'ru_1_05'     => 'ФФД v2 атол (1.05)',
                    'ru_1_2'      => 'ФФД v2 атол (1.2)',
                    'uz_1_0'      => 'ФФД v2 ГНК',
                ],
            ])->add('taxation_system', ChoiceType::class, [
                'label'   => 'Система налогообложения',
                'choices' => [
                    'osn'                => 'Общая система налогообложения',
                    'usn_income'         => 'Упрощенная (УСН, доходы)',
                    'usn_income_outcome' => 'Упрощенная (УСН, доходы минус расходы)',
                    'envd'               => 'Единый налог на вмененный доход (ЕНВД)',
                    'esn'                => 'Единый сельскохозяйственный налог (ЕСН)',
                    'patent'             => 'Патентная система налогообложения',
                ],
            ])->add('payment_method', ChoiceType::class, [
                'label'   => 'Признак способа расчета',
                'choices' => [
                    'full_prepayment'    => 'Предоплата',
                    'partial_prepayment' => 'Частичная предоплата',
                    'advance'            => 'Аванс',
                    'full_payment'       => 'Полный расчет',
                    'partial_payment'    => 'Частичный расчет и кредит',
                    'credit'             => 'Передача в кредит',
                    'credit_payment'     => 'Выплата по кредиту',
                ],
            ])->add('payment_object', ChoiceType::class, [
                'label'   => 'Признак предмета расчета товара',
                'choices' => [
                    'goods'                   => 'Товар',
                    'excise goods'            => 'Подакцизный товар',
                    'job'                     => 'Работа',
                    'service'                 => 'Услуга',
                    'gambling bet'            => 'Ставка азартной игры',
                    'gambling win'            => 'Выигрыш азартной игры',
                    'lottery ticket'          => 'Лотерейный билет',
                    'lottery win'             => 'Выигрыш в лотереи',
                    'intellectual activity'   => 'Результаты интеллектуальной деятельности',
                    'payment'                 => 'Платеж',
                    'agent commission'        => 'Агентское вознаграждение',
                    'payout'                  => 'Выплата',
                    'another subject'         => 'Иной предмет расчета',
                    'property right'          => 'Имущественное право',
                    'non operating income'    => 'Внереализационный доход',
                    'insurance contributions' => 'Страховые взносы',
                    'trade collection'        => 'Торговый сбор',
                    'resort collection'       => 'Курортный сбор',
                    'pledge'                  => 'Залог',
                    'expense'                 => 'Расход',
                    'pension insurance ip'    => 'Взносы на обязательное пенсионное страхование ИП',
                    'pension insurance'       => 'Взносы на обязательное пенсионное страхование',
                    'health insurance ip'     => 'Взносы на обязательное медицинское страхование ИП',
                    'health insurance'        => 'Взносы на обязательное медицинское страхование',
                    'social insurance'        => 'Взносы на обязательное социальное страхование',
                    'casino'                  => 'Платеж казино',
                    'insurance collection'    => 'Страховые взносы',
                ],
            ])->add('tax_type', ChoiceType::class, [
                'label'   => 'НДС на товары для ФФД старой версии',
                'choices' => [
                    '0' => 'Без НДС',
                    '1' => '0%',
                    '2' => '12%',
                    '3' => '12/112',
                    '4' => '18%',
                    '5' => '18/118',
                    '6' => '10%',
                    '7' => '10/110',
                    '8' => '20%',
                    '9' => '20/120',
                ],
            ])->add('new_tax_type', ChoiceType::class, [
                'label'   => 'НДС на товары',
                'choices' => [
                    'none'    => 'Без НДС',
                    'vat 0'   => 'НДС 0%',
                    'vat 10'  => 'НДС 10%',
                    'vat 20'  => 'НДС 20%',
                    'vat 110' => 'НДС 10/110',
                    'vat 120' => 'НДС 20/120',
                ],
            ])->add('ofd_in_delivery', CheckboxType::class, [
                'label'    => 'Учитывать доставку в ФФД',
                'required' => false,
            ])->add('delivery_payment_object', ChoiceType::class, [
                'label'   => 'Признак предмета расчета доставки',
                'choices' => [
                    'job'     => 'Работа',
                    'service' => 'Услуга',
                ],
            ])->add('delivery_tax_type', ChoiceType::class, [
                'label'   => 'НДС на доставку для ФФД старой версии',
                'choices' => [
                    '0' => 'Без НДС',
                    '1' => '0%',
                    '2' => '12%',
                    '3' => '12/112',
                    '4' => '18%',
                    '5' => '18/118',
                    '6' => '10%',
                    '7' => '10/110',
                    '8' => '20%',
                    '9' => '20/120',
                ],
            ])->add('delivery_new_tax_type', ChoiceType::class, [
                'label'   => 'НДС на доставку',
                'choices' => [
                    'none'    => 'Без НДС',
                    'vat 0'   => 'НДС 0%',
                    'vat 10'  => 'НДС 10%',
                    'vat 20'  => 'НДС 20%',
                    'vat 110' => 'НДС 10/110',
                    'vat 120' => 'НДС 20/120',
                ],
            ])->add('delivery_ikpu_code', TextType::class, [
                'label' => 'ИКПУ код для доставки',
            ])->add('delivery_package_code', TextType::class, [
                'label' => 'Код упаковки для доставки',
            ])->add('delivery_unit_code', TextType::class, [
                'label' => 'Код единицы измерения доставки',
            ]);
    }
}
