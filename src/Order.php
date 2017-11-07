<?php

/*
 * Copyright (c) 2017, infotec (service@infotec-edv.de)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Yii2MPay24;

use yii\base\BaseObject;

/**
 * The class encapsulates an order, that can be given to the payment processor.
 *
 * @author Sven Schoradt (s.schoradt@infotec-edv.de)
 */
class Order extends BaseObject {
    /**
     * Local transaction id.
     *
     * @var integer
     */
    public $tid;

    /**
     * The amount for the transaction
     *
     * This value is computed by the addItem methode.
     *
     * @var number
     */
    public $price = 0;

    /**
     * The currency for the transaction
     *
     * Thre character currency code like EUR.
     * @var string
     */
    public $currency = "EUR";

    /**
     * The language for the transaction
     *
     * Two character country code like DE or EN
     *
     * @var string
     */
    public $language = "DE";

    /**
     * The customer (name) for the transaction.
     *
     * @var string
     */
    public $customerName;

    /**
     * The customer e-mail for the transaction
     *
     * @var string
     */
    public $customerMail;

    /**
     * The customer ID for the transaction
     *
     * @var int
     */
    public $customerId;

    /**
     * The customer street for the transaction
     *
     * @var string
     */
    public $customerStreet;

    /**
     * The customer ZIP code for the transaction
     *
     * @var string
     */
    public $customerZip;

    /**
     * The customer city for the transaction
     *
     * @var string
     */
    public $customerCity;

    /**
     * The customer country ISO code for the transaction
     *
     * @var string
     */
    public $customerCountry = "DE";

    /**
     * Products list.
     *
     * @var array
     */
    private $cardItems = [];

    /**
     * Adds an order item to the order.
     *
     * Stores the item and computes the new order price by adding the item price.
     *
     * @param \Yii2MPay24\OrderItem $item
     */
    public function addItem(OrderItem $item) {
        $this->cardItems[] = $item;

        $this->price += $item->price;
    }

    /**
     * Returns the list of order items.
     *
     * @return array
     */
    public function getItems() {
        return $this->cardItems;
    }
}
