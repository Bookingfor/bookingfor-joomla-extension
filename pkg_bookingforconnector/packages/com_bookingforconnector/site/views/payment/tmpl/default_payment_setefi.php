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
 * Setefy System Form
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

$urlerror =  $urlBase . JRoute::_('index.php?view=payment&actionmode=errordonation&payedOrderId=' . $order->OrderId);
$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=setefiServer&payedOrderId=' . $order->OrderId);

$suffixOrder = "";
if (isset($this->item->paymentCount)) {
	$suffixOrder = (string)($this->item->paymentCount +1) ;
	$overrideAmount = $this->item->overrideAmount;
}

if ($this->actionmode=='donation')
{
	$donation = true;
}

$setefi = new setefi($merchantPayment->Data, $order, $this->language, $urlerror, $url, $suffixOrder,$overrideAmount , $SandboxMode,$donation);

?>
<script type="text/javascript">
<!--
	document.location = '<?php echo $setefi->requestUrl; ?>';
//-->
</script>
