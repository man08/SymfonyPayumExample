<?php
/**
 * Created by PhpStorm.
 * User: Lino
 * Date: 22/5/16
 * Time: 17:36
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Payment;
use Payum\Core\Bridge\Symfony\Event\ExecuteEvent;
use Payum\Core\Bridge\Symfony\PayumEvents;
use Payum\Core\Request\GetHttpRequest;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\ConfirmOrder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaymentListener implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            PayumEvents::GATEWAY_PRE_EXECUTE => 'onPayumGatewayPreExecute'
        ];
    }


    public function onPayumGatewayPreExecute(ExecuteEvent $event)
    {
        $context = $event->getContext();
        if (!$context->getRequest() instanceof ConfirmOrder) {
            return;
        }


        $context->getGateway()->execute($httpRequest = new GetHttpRequest());

        if ('POST' == $httpRequest->method && false == empty($httpRequest->request['cancel'])) {
            /** @var Payment $payment */
            $payment = $context->getRequest()->getFirstModel();
            $details = $payment->getDetails();

            $url = $details['CANCELURL'];
            $a = new RedirectResponse($url, 302, array('Location' => $url));
            $a->send();

            $event->stopPropagation();
        }
    }

}