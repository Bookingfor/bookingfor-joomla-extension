<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = $this->language;
$orderid = $this->orderId;

$errorPayment = false;
$invalidate=0;
$errorCode ="0";
$lastPayment =  null;

////$route= JRoute::_('index.php?view=orders&checkmode=' . $checkmode);
//$db   = JFactory::getDBO();
//$uriCart  = 'index.php?option=com_bookingforconnector&view=cart';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriCart .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdCart= ($db->getErrorNum())? 0 : intval($db->loadResult());
//if ($itemIdCart<>0)
//	$uriCart.='&Itemid='.$itemIdCart;

$uriCart  = COM_BOOKINGFORCONNECTOR_URICART;
$url_cart_page = JRoute::_($uriCart);

$redirect = JRoute::_($uriCart.'&layout=thanks&orderid='.$orderid, true, -1);
$redirecterror = JRoute::_($uriCart.'&layout=errors&orderid='.$orderid, true, -1);

if(!empty( $orderid )){
	$lastPayment = BFCHelper::GetLastOrderPayment($orderid);
}

if (empty($lastPayment) || $lastPayment->PaymentType!=3 || ($lastPayment->Status!=1 && $lastPayment->Status!=3 && $lastPayment->Status!=7 && $lastPayment->Status!=0 && $lastPayment->Status!=4 && $lastPayment->Status!=5 && $lastPayment->Status!=22 )) {
    $errorPayment= true;
	$errorCode ="1";

}
if (!empty($lastPayment)){
	if($lastPayment->Status==1 ||$lastPayment->Status==3 || $lastPayment->Status==7 ){
		$invalidate=1;
	}
	if ($lastPayment->Status==5 ) {
		$errorPayment= true;
		$errorCode ="2";
	}
}

		
		$paymentUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PAYMENTURL).$orderid."/".$lastPayment->OrderPaymentId;
		$typeMode="hidden";


	if ($errorPayment) {
			$redirecterror .= '?errorCode='.$errorCode;
			header( 'Location: ' . $redirecterror  );
			exit();
	}
		
?>
<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_WAITREDIRECT') ?><br />
<form action="<?php echo $paymentUrl?>" method="post" id="bfi_paymentform">
	<input id="urlok" name="urlok" type="<?php echo $typeMode ?>" title="urlok" value="<?php echo $redirect?>" />
	<input id="urlko" name="urlko" type="<?php echo $typeMode ?>" title="urlko"  value="<?php echo $redirecterror ?>" />
	<input id="invalidate" name="invalidate" type="<?php echo $typeMode ?>" title="urlok" value="<?php echo $invalidate?>" />
	<input type="submit" value="Invia">
</form>
<script type="text/javascript">
<!--
		jQuery(function($) {
			jQuery("#bfi_paymentform").submit();
		});
//-->
</script>
