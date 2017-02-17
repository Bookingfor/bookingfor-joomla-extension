<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/*
 * ================
 * PaypalExpress System Form
 * ================
 * 
 * */
$order = $this->item->order;
$merchantPayment = $this->item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;

$donation = false;


$paymentData = $merchantPayment->Data;

$uri                    = JURI::getInstance();
$urlBase = $uri->toString(array('scheme', 'host', 'port'));


$urlBack = $urlBase . JRoute::_('index.php?view=payment&actionmode=cancel&payedOrderId=' . $order->OrderId);
$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=paypalexpress&payedOrderId=' . $order->OrderId);
$urlBack = $url;

$suffixOrder = "";
if (isset($this->item->paymentCount)) {
	$suffixOrder = (string)($this->item->paymentCount +1) ;
	$overrideAmount = $this->item->overrideAmount;
}

if ($this->actionmode=='donation')
{
	$donation = true;
}

$paypalExpress = new paypalExpress($merchantPayment->Data, $order, $this->language, $urlBack, $url,$SandboxMode,$donation);


?><?php echo $paypalExpress->getUrl(); ?>



