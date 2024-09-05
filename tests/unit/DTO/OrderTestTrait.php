<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\Order;
use App\Entity\OrderItem;

trait OrderTestTrait
{
    protected function createOrder(): Order
    {
        return (new Order())
            ->setUsername(bin2hex(random_bytes(16)))
            ->setOrderNumber(bin2hex(random_bytes(12)))
            ->setReceiptId(bin2hex(random_bytes(16)))
            ->setBillName(bin2hex(random_bytes(16)))
            ->setBillCompany(bin2hex(random_bytes(16)))
            ->setBillAddress1(bin2hex(random_bytes(16)))
            ->setBillAddress2(bin2hex(random_bytes(16)))
            ->setBillCity(bin2hex(random_bytes(16)))
            ->setBillState(bin2hex(random_bytes(1)))
            ->setBillZipCode(bin2hex(random_bytes(8)))
            ->setBillCountry(bin2hex(random_bytes(1)))
            ->setShipName(bin2hex(random_bytes(16)))
            ->setShipCompany(bin2hex(random_bytes(16)))
            ->setShipAddress1(bin2hex(random_bytes(16)))
            ->setShipAddress2(bin2hex(random_bytes(16)))
            ->setShipCity(bin2hex(random_bytes(16)))
            ->setShipState(bin2hex(random_bytes(1)))
            ->setShipZipCode(bin2hex(random_bytes(8)))
            ->setShipCountry(bin2hex(random_bytes(1)))
            ->setPhoneNumber(bin2hex(random_bytes(16)))
            ->setEmail(bin2hex(random_bytes(16)))
            ->setComments(bin2hex(random_bytes(16)))
            ->setSubtotal(random_int(0, PHP_INT_MAX))
            ->setTax(random_int(0, PHP_INT_MAX))
            ->setShipping(random_int(0, PHP_INT_MAX))
            ->setRefundUnusedShipping((bool) random_int(0, 1))
        ;
    }

    protected function createOrderItem(): OrderItem
    {
        return new OrderItem();
    }

    protected function getOrderData(Order $order): array
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            $items[] = $this->getOrderItemData($item);
        }

        return [
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
            'items' => $items,
            'createdAt' => $order->getCreatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }

    protected function getOrderItemData(OrderItem $item): array
    {
        $quantity = (int) $item->getQuantity();
        $quantityCost = $quantity * (int) $item->getCost();

        return [
            'name' => $item->getName(),
            'sku' => $item->getSku(),
            'cost' => $item->getCost(),
            'quantity' => $quantity,
            'quantityCost' => $quantityCost,
        ];
    }
}
