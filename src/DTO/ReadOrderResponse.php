<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Order;

readonly class ReadOrderResponse implements \JsonSerializable
{
    private array $jsonData;

    public function __construct(Order $order)
    {
        $jsonData = [
            'id' => $order->getId(),
            'username' => $order->getUsername(),
            'orderNumber' => $order->getOrderNumber(),
            'receiptId' => $order->getReceiptId(),
            'billingInfo' => [
                'name' => $order->getBillName(),
                'company' => $order->getBillCompany(),
                'address1' => $order->getBillAddress1(),
                'address2' => $order->getBillAddress2(),
                'city' => $order->getBillCity(),
                'state' => $order->getBillState(),
                'zipCode' => $order->getBillZipCode(),
                'country' => $order->getBillCountry(),
            ],
            'shippingInfo' => [
                'name' => $order->getShipName(),
                'company' => $order->getShipCompany(),
                'address1' => $order->getShipAddress1(),
                'address2' => $order->getShipAddress2(),
                'city' => $order->getShipCity(),
                'state' => $order->getShipState(),
                'zipCode' => $order->getShipZipCode(),
                'country' => $order->getShipCountry(),
            ],
            'phoneNumber' => $order->getPhoneNumber(),
            'email' => $order->getEmail(),
            'comments' => $order->getComments(),
            'costs' => [
                'subtotal' => $order->getSubtotal(),
                'tax' => $order->getTax(),
                'shipping' => $order->getShipping(),
                'refundUnusedShipping' => $order->isRefundUnusedShipping(),
            ],
            'items' => [],
            'createdAt' => $order->getCreatedAt()?->format(\DateTimeInterface::ATOM),
        ];

        foreach ($order->getItems() as $item) {
            $quantity = (int) $item->getQuantity();
            $quantityCost = $quantity * (int) $item->getCost();

            $jsonData['items'][] = [
                'name' => $item->getName(),
                'sku' => $item->getSku(),
                'cost' => $item->getCost(),
                'quantity' => $quantity,
                'quantityCost' => $quantityCost,
            ];
        }

        $this->jsonData = $jsonData;
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}
