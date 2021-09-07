<?php
/**
 *NOTICE OF LICENSE
 *
 *This source file is subject to the Open Software License (OSL 3.0)
 *that is bundled with this package in the file LICENSE.txt.
 *It is also available through the world-wide-web at this URL:
 *http://opensource.org/licenses/osl-3.0.php
 *If you did not receive a copy of the license and are unable to
 *obtain it through the world-wide-web, please send an email
 *to license@prestashop.com so we can send you a copy immediately.
 *
 *DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *versions in the future. If you wish to customize PrestaShop for your
 *needs please refer to http://www.prestashop.com for more information.
 *
 *@author INVERTUS UAB www.invertus.eu  <support@invertus.eu>
 *@copyright SIX Payment Services
 *@license   SIX Payment Services
 */

namespace Invertus\SaferPay\Service\TransactionFlow;

use Invertus\SaferPay\Api\Request\AssertRefundService;
use Invertus\SaferPay\DTO\Response\Assert\AssertBody;
use Invertus\SaferPay\DTO\Response\AssertRefund\AssertRefundBody;
use Invertus\SaferPay\Repository\SaferPayOrderRepository;
use Invertus\SaferPay\Service\Request\AssertRefundRequestObjectCreator;
use Invertus\SaferPay\Service\Request\AssertRequestObjectCreator;
use Invertus\SaferPay\Service\SaferPayOrderStatusService;
use Order;
use SaferPayOrder;

class SaferPayTransactionRefundAssertion
{
    /**
     * @var AssertRefundRequestObjectCreator
     */
    private $assertRefundRequestCreator;

    /**
     * @var SaferPayOrderRepository
     */
    private $orderRepository;

    /**
     * @var AssertRefundService
     */
    private $assertionRefundService;

    /**
     * @var SaferPayOrderStatusService
     */
    private $orderStatusService;

    public function __construct(
        AssertRefundRequestObjectCreator $assertionRefundService,
        SaferPayOrderRepository $orderRepository,
        AssertRefundService $assertRefundService,
        SaferPayOrderStatusService $orderStatusService
    ) {
        $this->assertRefundRequestCreator = $assertionRefundService;
        $this->orderRepository = $orderRepository;
        $this->assertionRefundService = $assertRefundService;
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * @param int $orderId
     *
     * @return AssertRefundBody
     * @throws \Exception
     */
    public function assertRefund($orderId)
    {
        $saferPayOrder = $this->getSaferPayOrder($orderId);
        $order = new Order($orderId);

        $assertRequest = $this->assertRefundRequestCreator->create($orderId);
        $assertResponse = $this->assertionRefundService->assertRefund($assertRequest);

        $assertBody = $this->assertionRefundService->createObjectsFromAssertRefundResponse(
            $assertResponse,
            $saferPayOrder->id
        );

        return $assertBody;
    }

    /**
     * @param $orderId
     *
     * @return false|SaferPayOrder
     * @throws \Exception
     */
    private function getSaferPayOrder($orderId)
    {
        $saferPayOrderId = $this->orderRepository->getIdByOrderId($orderId);

        return new SaferPayOrder($saferPayOrderId);
    }
}
