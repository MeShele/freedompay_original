<?php

declare(strict_types=1);

namespace PrestaShop\Module\FreedomPay\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FreedomPayConfigurationController extends FrameworkBundleAdminController
{
    public function index(Request $request): Response
    {
        $formDataHandler = $this->get(
            'prestashop.module.freedompay.form.freedompay_configuration_form_data_handler'
        );

        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formDataHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', 'Данные успешно сохранены');

                return $this->redirectToRoute('configuration_form');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@Modules/freedompay/views/templates/admin/form.html.twig', [
            'configurationForm' => $form->createView()
        ]);
    }
}
