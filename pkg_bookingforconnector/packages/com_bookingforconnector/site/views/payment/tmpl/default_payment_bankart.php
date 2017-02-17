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
 * Bankart System Form
 * ================
 * 
 * */
$order = $this->item->order;
$merchantPayment = $this->item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;

$paymentData = $merchantPayment->Data;

$uri                    = JURI::getInstance();
$urlBase = $uri->toString(array('scheme', 'host', 'port'));

$urlerror =  $urlBase . JRoute::_('index.php?view=payment&actionmode=error&payedOrderId=' . $order->OrderId);
// !!!---------------- sempre sotto https ------------------!!!
$url = JRoute::_('index.php?view=payment&actionmode=bankartServer&payedOrderId=' . $order->OrderId,true,1);

$overrideAmount =0;
$suffixOrder = "";
if (isset($this->item->paymentCount)) {
	$suffixOrder = (string)($this->item->paymentCount +1) ;
	$overrideAmount = $this->item->overrideAmount;
	}
$Bankart = new Bankart($merchantPayment->Data, $order, $this->language, $urlerror, $url, $suffixOrder,$overrideAmount , $SandboxMode);
?>
<script type="text/javascript">
<!--
	document.location = '<?php echo $Bankart->requestUrl; ?>';
//-->
</script>
