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

use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use Mpay24\Mpay24;

/**
 * This class provides the methodes for a redirect integration of the mpay24
 * system.
 *
 * @author Sven Schoradt
 */
class RedirectIntegration extends BaseObject {
    /**
     * MPay24 API
     *
     * @var \Mpay24\Mpay24
     */
    private $mpay24;

    /**
     * Language code foor the integration.
     *
     * The integrated iframe or popup content uses this language.
     *
     * @var string
     */
    public $language = 'DE';

    /**
     * The route that is called after a successful payment is done.
     *
     * The route will be processed by the Yii2 Url helper.
     *
     * @var string
     */
    public $successRoute      = 'success';

    /**
     * The route that is called, if the payment is canceled by the user.
     *
     * The route will be processed by the Yii2 Url helper.
     *
     * @var string
     */
    public $cancelRoute       = 'index';

    /**
     * The route is used to confirm the payment from the mpay24 system.
     *
     * The route will be processed by the Yii2 Url helper.
     *
     * @var string
     */
    public $confirmationRoute = 'confirmation';

    /**
     * The route is used to provide error information by the mpay24 system.
     *
     * The route will be processed by the Yii2 Url helper.
     *
     * @var string
     */
    public $errorRoute        = 'error';

    /**
     * IFrame display options.
     *
     * @var array
     */
    private $defaultOptions = [
        'mPAY24OrderStyle' => "margin-left: auto; margin-right: auto;",
        'mPAY24OrderLogoStyle' => "",
        'mPAY24OrderPageHeaderStyle' => "background-color: #FFF;margin-bottom:14px;",
        'mPAY24OrderPageCaptionStyle' => "background-color:#FFF;background:transparent;color:#647378;padding-left:0px;",
        'mPAY24OrderPageStyle' => "border:1px solid #838F93;background-color:#FFF;",
        'mPAY24OrderInputFieldsStyle' => "background-color:#ffffff;border:1px solid #DDE1E7;padding:2px 0px;margin-bottom:5px;width:100%;max-width:200px;",
        'mPAY24OrderDropDownListsStyle' => "padding:2px 0px;margin-bottom:5px;",
        'mPAY24OrderButtonsStyle' => "background-color: #005AC1;border: none;color: #FFFFFF;cursor: pointer;font-size:10px;font-weight:bold;padding:5px 10px;text-transform:uppercase;",
        'mPAY24OrderErrorsStyle' => "background-color: #FFF;padding: 10px 0px;",
        'mPAY24OrderSuccessTitleStyle' => "background-color: #FFF;",
        'mPAY24OrderErrorTitleStyle' => "background-color: #FFF;",
        'mPAY24OrderFooterStyle' => "",
        'mPAY24ShoppingCartStyle' => "",
        'mPAY24ShoppingCartHeader' => "",
        'mPAY24ShoppingCartHeaderStyle' => "background-color:#FFF;margin-bottom:14px;color:#647378",
        'mPAY24ShoppingCartCaptionStyle' => "background-color:#FFF;background:transparent;color:#647378;padding-left:0px;font-size:14px;",
        'mPAY24ShoppingCartNumberHeader' => "",
        'mPAY24ShoppingCartNumberStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartProductNumberHeader' => "",
        'mPAY24ShoppingCartProductNumberStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartDescriptionHeader' => "",
        'mPAY24ShoppingCartDescriptionStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartPackageHeader' => "",
        'mPAY24ShoppingCartPackageStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartQuantityHeader' => "",
        'mPAY24ShoppingCartQuantityStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartItemPriceHeader' => "",
        'mPAY24ShoppingCartItemPriceStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartPriceHeader' => "",
        'mPAY24ShoppingCartPriceStyle' => "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;",
        'mPAY24ShoppingCartItemNumberStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemNumberStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemProductNumberStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemProductNumberStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemDescriptionStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemDescriptionStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemPackageStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemPackageStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemQuantityStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemQuantityStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemItemPriceStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemItemPriceStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemPriceStyleOdd' => "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartItemPriceStyleEven' => "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;",
        'mPAY24ShoppingCartSubTotalHeader' => "",
        'mPAY24ShoppingCartSubTotalHeaderStyle' => "background-color:#FFF;color: #647378;padding:3px;font-weight:normal;",
        'mPAY24ShoppingCartSubTotalStyle' => "background-color:#FFF;color:#647378;border:none;font-weight:normal;padding:3px 20px;",
        'mPAY24ShoppingCartDiscountHeader' => "",
        'mPAY24ShoppingCartDiscountHeaderStyle' => "background-color: #FFF; color: #647378;font-weight:normal;padding:3px;",
        'mPAY24ShoppingCartDiscountStyle' => "background-color:#FFF;color:#647378;border:none;padding:3px 20px;",
        'mPAY24ShoppingCartShippingCostsHeader' => "",
        'mPAY24ShoppingCartShippingCostsHeaderStyle' => "background-color: #FFF; color: #647378;font-weight:normal;padding:3px;",
        'mPAY24ShoppingCartShippingCostsStyle' => "background-color:#FFF;color:#647378;border:none;padding:3px 20px;",
        'mPAY24ShoppingCartTaxHeader' => "",
        'mPAY24ShoppingCartTaxHeaderStyle' => "background-color:#FFF;color: #647378;padding:3px;font-weight:normal;",
        'mPAY24ShoppingCartTaxStyle' => "background-color:#FFF;color:#647378;border:none;font-weight:normal;padding:3px 20px;",
        'mPAY24PriceHeader' => "",
        'mPAY24PriceHeaderStyle' => "background-color:#FFF;color: #647378;padding:3px;font-weight:normal;border-top: 1px solid #838F93;",
        'mPAY24PriceStyle' => "background-color:#FFF;color:#005AC1;border:none;padding:4px;font-weight:bold;padding:3px 20px;font-size:14px;border-top: 1px solid #838F93;",
        'mPAY24ShoppingCartDescription' => "",
    ];

    public $options = [];

    /**
     * Create a redirect integration.
     *
     * @param array $data Configuration to fill the object.
     *
     * @throws \yii\base\Exception Some parameters are not found in configuration.
     */
    public function __construct($data)
    {
        $data['options'] = ArrayHelper::merge($this->defaultOptions, (isset($data['options'])?$data['options']:[]));

        parent::__construct($data);

        $params = Yii::$app->params['payment'];

        if (!($params && isset($params['merchant']) && isset($params['soappass']))) {
            throw new \yii\base\Exception("payment is not configured");
        }

        $this->mpay24 = new Mpay24(
            $params['merchant'],
            $params['soappass'],
            $params['test_system'],
            $params['debug'],
            $params['proxy_host'],
            $params['proxy_port'],
            $params['proxy_user'],
            $params['proxy_pass'],
            $params['verify_peer']
        );
    }

    /**
     * Start a redirect payment.
     *
     * @param \Yii2MPay24\Order $order Order to execute.
     *
     * @return \Mpay24\Responses\SelectPaymentResponse
     */
    public function pay(Order $order) {
        $mdxi = $this->createMdxi($order);

        return $this->mpay24->paymentPage($mdxi);
    }

    /**
     * Query the transaction status.
     *
     * @param int $tid Local transaction id.
     *
     * @return \Mpay24\Responses\TransactionStatusResponse
     */
    public function transactionStatus($tid) {
        return $this->mpay24->paymentStatusByTid($tid);
    }

    /**
     * Create the XML Document to describe the order.
     *
     * @param \Yii2MPay24\Order $order Order object to transform.
     *
     * @return \Mpay24\Mpay24Order Order XML object
     */
    private function createMdxi(Order $order) {
        $mdxi = new \Mpay24\Mpay24Order();

        $mdxi->Order->Tid = $order->tid;

        //$mdxi->Order->ClientIP = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);

        $this->configureOrderStyles($mdxi);

        $this->configureTemplate($mdxi);

        $this->configureShoppingCardStyles($mdxi);

        $items = $order->getItems();

        for ($index = 1; $index <= sizeof($items); $index ++) {
            /**
             * @var OrderItem $item
             */
            $item = $items[$index -1];

            $item->number = $index;

            $mdxi->Order->ShoppingCart->Item($index)->Number = $item->number;

            if ($item->ident) {
                $mdxi->Order->ShoppingCart->Item($index)->ProductNr = $item->ident;
            }

            $mdxi->Order->ShoppingCart->Item($index)->Description = $item->description;

            if ($item->package) {
                $mdxi->Order->ShoppingCart->Item($index)->Package = $item->package;
            }

            $mdxi->Order->ShoppingCart->Item($index)->Quantity = $item->quantity;
            $mdxi->Order->ShoppingCart->Item($index)->ItemPrice = number_format($item->price, 2, '.', '');
            $mdxi->Order->ShoppingCart->Item($index)->ItemPrice->setTax(number_format($item->tax, 2, '.', ''));

            $mdxi->Order->ShoppingCart->Item($index)->Price = number_format($item->quantity * $item->tax, 2, '.', '');

            $this->configureShoppingCardItemStyles($mdxi, $item);
        }

        //TODO: set this properties if they were set in the order
        //$mdxi->Order->ShoppingCart->SubTotal(1, number_format("10.00", 2, '.', ''));
        //$mdxi->Order->ShoppingCart->SubTotal(1)->setHeader($this->options['mPAY24ShoppingCartSubTotalHeader']);
//        $mdxi->Order->ShoppingCart->SubTotal(1)->setHeaderStyle($this->options['mPAY24ShoppingCartSubTotalHeaderStyle']);
//        $mdxi->Order->ShoppingCart->SubTotal(1)->setStyle($this->options['mPAY24ShoppingCartSubTotalStyle']);
//
//        $mdxi->Order->ShoppingCart->ShippingCosts(1, number_format("5.00", 2, '.', ''));
//        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setHeader($this->options['mPAY24ShoppingCartShippingCostsHeader']);
//        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setHeaderStyle($this->options['mPAY24ShoppingCartShippingCostsHeaderStyle']);
//        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setStyle($this->options['mPAY24ShoppingCartShippingCostsStyle']);
//
//        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setTax(number_format("1.00", 2, '.', ''));
//
//        $mdxi->Order->ShoppingCart->Tax(1, number_format("2.00", 2, '.', ''));
//        $mdxi->Order->ShoppingCart->Tax(1)->setHeader($this->options['mPAY24ShoppingCartTaxHeader']);
//        $mdxi->Order->ShoppingCart->Tax(1)->setHeaderStyle($this->options['mPAY24ShoppingCartTaxHeaderStyle']);
//        $mdxi->Order->ShoppingCart->Tax(1)->setStyle($this->options['mPAY24ShoppingCartTaxStyle']);
//
//        $mdxi->Order->ShoppingCart->Discount(1, '-' . number_format("5.00", 2, '.', ''));
//        $mdxi->Order->ShoppingCart->Discount(1)->setHeader($this->options['mPAY24ShoppingCartDiscountHeader']);
//        $mdxi->Order->ShoppingCart->Discount(1)->setHeaderStyle($this->options['mPAY24ShoppingCartDiscountHeaderStyle']);
//        $mdxi->Order->ShoppingCart->Discount(1)->setStyle($this->options['mPAY24ShoppingCartDiscountStyle']);

        $mdxi->Order->Price = $order->price;
        $this->configurePriceStyle($mdxi);

        $mdxi->Order->Currency = $order->currency;

        $mdxi->Order->Customer = $order->customerName;

        $mdxi->Order->Customer->setId($order->customerId);
        $mdxi->Order->Customer->setUseProfile("false");

        $mdxi->Order->BillingAddr->setMode("ReadOnly");
        $mdxi->Order->BillingAddr->Name    = $order->customerName;
        $mdxi->Order->BillingAddr->Street  = $order->customerStreet;
        //$mdxi->Order->BillingAddr->Street2 = $this->customer_street2;
        $mdxi->Order->BillingAddr->Zip     = $order->customerZip;
        $mdxi->Order->BillingAddr->City    = $order->customerCity;
        //TODO $mdxi->Order->BillingAddr->Email   = $order->customerMail;

        $mdxi->Order->BillingAddr->Country->setCode($order->customerCountry);

        $mdxi->Order->ShippingAddr->setMode("ReadOnly");
        $mdxi->Order->ShippingAddr->Name    = $order->customerName;
        $mdxi->Order->ShippingAddr->Street  = $order->customerStreet;
        //$mdxi->Order->ShippingAddr->Street2 = $this->customer_street2;
        $mdxi->Order->ShippingAddr->Zip     = $order->customerZip;
        $mdxi->Order->ShippingAddr->City    = $order->customerCity;
        //TODO $mdxi->Order->ShippingAddr->Email   = $order->customerMail;

        $mdxi->Order->ShippingAddr->Country->setCode($order->customerCountry);

        $mdxi->Order->URL->Success      = Url::to([$this->successRoute], true);
        $mdxi->Order->URL->Error        = Url::to([$this->errorRoute], true);
        $mdxi->Order->URL->Confirmation = Url::to([$this->confirmationRoute, 'token' => ''], true);
        $mdxi->Order->URL->Cancel       = Url::to([$this->cancelRoute], true);

        \Yii::trace("MDXI: " . $mdxi->toXML());

        return $mdxi;
    }

    /**
     * Set the order style options into the ORDER object.
     *
     * @param \Mpay24\Mpay24Order $mdxi Mpay24Order object.
     */
    private function configureOrderStyles($mdxi) {
        // Order design settings for the mPAY24 pay page
        $mdxi->Order->setStyle($this->options['mPAY24OrderStyle']);
        $mdxi->Order->setLogoStyle($this->options['mPAY24OrderLogoStyle']);
        $mdxi->Order->setPageHeaderStyle($this->options['mPAY24OrderPageHeaderStyle']);
        $mdxi->Order->setPageCaptionStyle($this->options['mPAY24OrderPageCaptionStyle']);
        $mdxi->Order->setPageStyle($this->options['mPAY24OrderPageStyle']);
        $mdxi->Order->setInputFieldsStyle($this->options['mPAY24OrderInputFieldsStyle']);
        $mdxi->Order->setDropDownListsStyle($this->options['mPAY24OrderDropDownListsStyle']);
        $mdxi->Order->setButtonsStyle($this->options['mPAY24OrderButtonsStyle']);
        $mdxi->Order->setErrorsStyle($this->options['mPAY24OrderErrorsStyle']);
        $mdxi->Order->setSuccessTitleStyle($this->options['mPAY24OrderSuccessTitleStyle']);
        $mdxi->Order->setErrorTitleStyle($this->options['mPAY24OrderErrorTitleStyle']);
        $mdxi->Order->setFooterStyle($this->options['mPAY24OrderFooterStyle']);
    }

    /**
     * Set the shopping card style options into the ORDER object.
     *
     * @param \Mpay24\Mpay24Order $mdxi ORDER object.
     */
    private function configureShoppingCardStyles($mdxi) {
        $mdxi->Order->ShoppingCart->setStyle($this->options['mPAY24ShoppingCartStyle']);
        $mdxi->Order->ShoppingCart->setHeader($this->options['mPAY24ShoppingCartHeader']);
        $mdxi->Order->ShoppingCart->setHeaderStyle($this->options['mPAY24ShoppingCartHeaderStyle']);
        $mdxi->Order->ShoppingCart->setCaptionStyle($this->options['mPAY24ShoppingCartCaptionStyle']);
        $mdxi->Order->ShoppingCart->setNumberHeader($this->options['mPAY24ShoppingCartNumberHeader']);
        $mdxi->Order->ShoppingCart->setNumberStyle($this->options['mPAY24ShoppingCartNumberStyle']);
        $mdxi->Order->ShoppingCart->setProductNrHeader($this->options['mPAY24ShoppingCartProductNumberHeader']);
        $mdxi->Order->ShoppingCart->setProductNrStyle($this->options['mPAY24ShoppingCartProductNumberStyle']);
        $mdxi->Order->ShoppingCart->setDescriptionHeader($this->options['mPAY24ShoppingCartDescriptionHeader']);
        $mdxi->Order->ShoppingCart->setDescriptionStyle($this->options['mPAY24ShoppingCartDescriptionStyle']);
        $mdxi->Order->ShoppingCart->setPackageHeader($this->options['mPAY24ShoppingCartPackageHeader']);
        $mdxi->Order->ShoppingCart->setPackageStyle($this->options['mPAY24ShoppingCartPackageStyle']);
        $mdxi->Order->ShoppingCart->setQuantityHeader($this->options['mPAY24ShoppingCartQuantityHeader']);
        $mdxi->Order->ShoppingCart->setQuantityStyle($this->options['mPAY24ShoppingCartQuantityStyle']);
        $mdxi->Order->ShoppingCart->setItemPriceHeader($this->options['mPAY24ShoppingCartItemPriceHeader']);
        $mdxi->Order->ShoppingCart->setItemPriceStyle($this->options['mPAY24ShoppingCartItemPriceStyle']);
        $mdxi->Order->ShoppingCart->setPriceHeader($this->options['mPAY24ShoppingCartPriceHeader']);
        $mdxi->Order->ShoppingCart->setPriceStyle($this->options['mPAY24ShoppingCartPriceStyle']);

        $mdxi->Order->ShoppingCart->Description = ($this->options['mPAY24ShoppingCartDescription']);
    }

    /**
     * Set the shopping card item style options into the ORDER object.
     *
     * @param \Mpay24\Mpay24Order $mdxi ORDER object.
     */
    private function configureShoppingCardItemStyles($mdxi, $item) {
        if ($item->number % 2) {
            $ext = 'Even';
        } else {
            $ext = 'Odd';
        }

        $mdxi->Order->ShoppingCart->Item($item->number)->Number->setStyle($this->options['mPAY24ShoppingCartItemNumberStyle' . $ext]);

        if ($item->ident) {
            $mdxi->Order->ShoppingCart->Item($item->number)->ProductNr->setStyle($this->options['mPAY24ShoppingCartItemProductNumberStyle' . $ext]);
        }

        $mdxi->Order->ShoppingCart->Item($item->number)->Description->setStyle($this->options['mPAY24ShoppingCartItemDescriptionStyle' . $ext]);

        if ($item->package) {
            $mdxi->Order->ShoppingCart->Item ($item->number)->Package->setStyle ($this->options['mPAY24ShoppingCartItemPackageStyle' . $ext]);
        }

        $mdxi->Order->ShoppingCart->Item($item->number)->Quantity->setStyle($this->options['mPAY24ShoppingCartItemQuantityStyle' . $ext]);
        $mdxi->Order->ShoppingCart->Item($item->number)->ItemPrice->setStyle($this->options['mPAY24ShoppingCartItemItemPriceStyle' . $ext]);
        $mdxi->Order->ShoppingCart->Item($item->number)->Price->setStyle($this->options['mPAY24ShoppingCartItemPriceStyle' . $ext]);

    }

    /**
     * Set the price style options into the ORDER object.
     *
     * @param \Mpay24\Mpay24Order $mdxi ORDER object.
     */
    private function configurePriceStyle($mdxi) {
        $mdxi->Order->Price->setHeader($this->options['mPAY24PriceHeader']);
        $mdxi->Order->Price->setHeaderStyle($this->options['mPAY24PriceHeaderStyle']);
        $mdxi->Order->Price->setStyle($this->options['mPAY24PriceStyle']);
    }

    /**
     * Set the template options into the ORDER object.
     *
     * @param \Mpay24\Mpay24Order $mdxi ORDER object.
     */
    private function configureTemplate($mdxi) {
        $mdxi->Order->TemplateSet = "WEB";
        $mdxi->Order->TemplateSet->setLanguage($this->language);
        $mdxi->Order->TemplateSet->setCSSName("MOBILE");
    }


}
