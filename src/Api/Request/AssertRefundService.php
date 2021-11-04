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

namespace Invertus\SaferPay\Api\Request;

use Invertus\SaferPay\Api\ApiRequest;
use Invertus\SaferPay\DTO\Request\Assert\AssertRequest;
use Invertus\SaferPay\DTO\Request\AssertRefund\AssertRefundRequest;
use Invertus\SaferPay\DTO\Response\Assert\AssertBody;
use Invertus\SaferPay\DTO\Response\AssertRefund\AssertRefundBody;
use Invertus\SaferPay\EntityBuilder\SaferPayAssertBuilder;
use Invertus\SaferPay\EntityBuilder\SaferPayAssertRefundBuilder;
use Invertus\SaferPay\Exception\Api\SaferPayApiException;
use Invertus\SaferPay\Service\Response\AssertRefundResponseObjectCreator;
use Invertus\SaferPay\Service\Response\AssertResponseObjectCreator;
use PHPUnit\Exception;
use SaferPayOrder;

class AssertRefundService
{
    const ASSERT_REFUND_API_TRANSACTION = 'Payment/v1/Transaction/AssertRefund';

    /**
     * @var ApiRequest
     */
    private $apiRequest;
    /**
     * @var AssertRefundResponseObjectCreator
     */
    private $assertRefundResponseObjectCreator;
    /**
     * @var SaferPayAssertRefundBuilder
     */
    private $assertRefundBuilder;

    public function __construct(
        ApiRequest $apiRequest,
        AssertRefundResponseObjectCreator $assertRefundResponseObjectCreator,
        SaferPayAssertRefundBuilder $assertRefundBuilder
    ) {
        $this->apiRequest = $apiRequest;
        $this->assertRefundResponseObjectCreator = $assertRefundResponseObjectCreator;
        $this->assertRefundBuilder = $assertRefundBuilder;
    }

    /**
     * @param AssertRefundRequest $assertRefundRequest
     *
     * @return AssertBody
     * @throws \Exception
     */
    public function assertRefund(AssertRefundRequest $assertRefundRequest)
    {
        $assertApi = self::ASSERT_REFUND_API_TRANSACTION;
        try {
            $response = $this->apiRequest->post(
                $assertApi,
                [
                    'body' => json_encode($assertRefundRequest->getAsArray()),
                ]
            );
        } catch (Exception $e) {
            throw new SaferPayApiException('Assert Refund API failed', SaferPayApiException::ASSERT);
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param object $responseBody
     *
     * @return AssertRefundBody
     */
    public function createObjectsFromAssertRefundResponse($responseBody)
    {
        $assertBody = $this->assertRefundResponseObjectCreator->createAssertRefundObject($responseBody);

        return $assertBody;
    }
}