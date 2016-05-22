<?php

namespace AppBundle\Extension;

use AppBundle\Entity\Payment;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\GetHttpRequest;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\ConfirmOrder;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaypalExtension implements ExtensionInterface
{

    /**
    * @inheritDoc
    */
    public function onExecute(Context $context)
    {
        $this->confirmOrderAction($context);
    }

    /**
     * @inheritDoc
     */
    public function onPreExecute(Context $context)
    {

    }

    /**
     * @inheritDoc
     */
    public function onPostExecute(Context $context)
    {

    }

    private function confirmOrderAction(Context $context)
    {
        if (!$context->getRequest() instanceof ConfirmOrder) {
            return;
        }

        var_dump('working!');

        $context->getGateway()->execute($httpRequest = new GetHttpRequest());

        if ('POST' == $httpRequest->method && false == empty($httpRequest->request['cancel'])) {
            /** @var Payment $payment */
            $payment = $context->getRequest()->getFirstModel();
            $details = $payment->getDetails();

            $url = $details['CANCELURL'];
            $a = new RedirectResponse($url, 302, array('Location' => $url));
            $a->send();

            die();
        }
    }

}

