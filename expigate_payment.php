<?php

define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);


// echo '<pre>';
// print_r($_POST);
// exit;

class gwapi
{

// Initial Setting Functions

    function setLogin($security_key)
    {
        $this->login['security_key'] = $security_key;
    }

    function setOrder($orderid,
                      $orderdescription,
                      $tax,
                      $shipping,
                      $ponumber,
                      $ipaddress)
    {
        $this->order['orderid'] = $orderid;
        $this->order['orderdescription'] = $orderdescription;
        $this->order['tax'] = $tax;
        $this->order['shipping'] = $shipping;
        $this->order['ponumber'] = $ponumber;
        $this->order['ipaddress'] = $ipaddress;
    }

    function setBilling($billingData)
    {

        $this->billing['firstname'] = $billingData['firstname'];
        $this->billing['lastname'] = $billingData['lastname'];
        $this->billing['company'] = $billingData['company'];
        $this->billing['address1'] = $billingData['address1'];
        $this->billing['address2'] = $billingData['address2'];
        $this->billing['city'] = $billingData['city'];
        $this->billing['state'] = $billingData['state'];
        $this->billing['zip'] = $billingData['zip'];
        $this->billing['country'] = $billingData['country'];
        $this->billing['phone'] = $billingData['phone'];
        $this->billing['fax'] = $billingData['fax'];
        $this->billing['email'] = $billingData['email'];
        $this->billing['website'] = $billingData['website'];
    }

    function setShipping($shippingData)
    {
        $this->shipping['firstname'] = $shippingData['firstname'];
        $this->shipping['lastname'] = $shippingData['lastname'];
        $this->shipping['company'] = $shippingData['company'];
        $this->shipping['address1'] = $shippingData['address1'];
        $this->shipping['address2'] = $shippingData['address2'];
        $this->shipping['city'] = $shippingData['city'];
        $this->shipping['state'] = $shippingData['state'];
        $this->shipping['zip'] = $shippingData['zip'];
        $this->shipping['country'] = $shippingData['country'];
        $this->shipping['email'] = $shippingData['email'];
    }

    // Transaction Functions

    function doSale($amount, $ccnumber, $ccexp, $cvv = "", $is_amazon=0,$payment_url=null)
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
        $query .= "cvv=" . urlencode($cvv) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=sale";

        return $this->_doPost($query, $is_amazon,$payment_url);
    }

    function doAuth($amount, $ccnumber, $ccexp, $cvv = "123")
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
        $query .= "cvv=" . urlencode($cvv) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=auth";
        return $this->_doPost($query);
    }

    function doCredit($amount, $ccnumber, $ccexp)
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        $query .= "type=credit";
        return $this->_doPost($query);
    }

    function doOffline($authorizationcode, $amount, $ccnumber, $ccexp)
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
        $query .= "authorizationcode=" . urlencode($authorizationcode) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=offline";
        return $this->_doPost($query);
    }

    function doCapture($transactionid, $amount = 0)
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        if ($amount > 0) {
            $query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
        }
        $query .= "type=capture";
        return $this->_doPost($query);
    }

    function doVoid($transactionid)
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        $query .= "type=void";
        return $this->_doPost($query);
    }

    function doRefund($transactionid, $amount = 0)
    {

        $query = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->login['security_key']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        if ($amount > 0) {
            $query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
        }
        $query .= "type=refund";
        return $this->_doPost($query);
    }

    function _doPost($query, $is_amazon=0,$payment_url=null)
    {
        $ch = curl_init();
        if ($payment_url) {
            curl_setopt($ch, CURLOPT_URL, $payment_url);
        } else {
            curl_setopt($ch, CURLOPT_URL, "https://secure.expigate.com/api/transact.php");
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (!($data = curl_exec($ch))) {
            return ERROR;
        }
        curl_close($ch);
        unset($ch);
        // print "\n$data\n";
        $data = explode("&", $data);
        for ($i = 0; $i < count($data); $i++) {
            $rdata = explode("=", $data[$i]);
            $this->responses[$rdata[0]] = $rdata[1];
        }
        // return $this->responses['response'];
        return $this->responses;
    }
}

// echo '<Pre>';
// print ;

// echo json_encode($gw->responses);
?>
