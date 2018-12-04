<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$usessl = COM_BOOKINGFORCONNECTOR_USESSL;

$currency_text = array('978' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_978'), //Euro
						'191' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_191'), //Kune
						'840' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_840'), //U.S. dollar   
						'392' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_392'), //Japanese yen
						'124' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_124'), //Canadian dollar
						'36' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_36'), //Australian dollar
						'643' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_643'), //Russian Ruble  
						'200' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_200'), //Czech koruna
						'702' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_702'), //Singapore dollar  
						'826' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_826') //Pound sterling                         
					);

if($showcurrencyswitcher && !empty($currencyExchanges)){
?>
	<div class="bfi-currency-switcher">
		<div class="bfi-currency-switcher-selected bfi_<?php echo $currentCurrency ?>">&nbsp;</div>
		<div class="bfi-currency-switcher-content">
<?php 
foreach ($currencyExchanges as $currencyExchangeCode => $currencyExchange ) {
    ?>
			<div class="bfi-currency-switcher-selector bfi_<?php echo $currencyExchangeCode ?>" rel="<?php echo $currencyExchangeCode ?>"> <?php echo $currency_text[$currencyExchangeCode] ?><!-- (<span class=" bfi_<?php echo $defaultCurrency ?>">1</span> = <span class=" bfi_<?php echo $currencyExchangeCode ?>"><?php echo $currencyExchange?><span>) --></div>
    
<?php     
}
?>

		</div>
	</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function() {
	jQuery('.bfi-currency-switcher-selector').click(function() {
		var currCurrency= "<?php echo $currentCurrency ?>";
		var newCurrency= jQuery(this).attr("rel");
		if(currCurrency!==newCurrency){
			var queryChangeCurrency = "task=bfi_change_currency&bfiselectedcurrency="+newCurrency;
			jQuery.post(bfi_variable.bfi_urlCheck, queryChangeCurrency, function(data) {
					window.location.href = window.location.href;

			},'json');			
		}
	});
});  
//-->
</script>
<?php 
	} //$showcurrencyswitcher
	if($showcart){
//	$db   = JFactory::getDBO();
//	$uriCart  = 'index.php?option=com_bookingforconnector&view=cart';
//	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriCart .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//	$itemIdCart= ($db->getErrorNum())? 0 : intval($db->loadResult());
//	if ($itemIdCart<>0)
//		$uriCart.='&Itemid='.$itemIdCart;
	$url_cart_page = JRoute::_(COM_BOOKINGFORCONNECTOR_URICART);
	$currentCartsItems = BFCHelper::getSession('totalItems', 0, 'bfi-cart');
	if($usessl){
		$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
	}
  ?>
<a href="<?php echo $url_cart_page ?>" class="bfi-shopping-cart"><i class="fa fa-shopping-cart "></i> <span class="bfibadge" style="<?php echo (COM_BOOKINGFORCONNECTOR_SHOWBADGE) ?"":"display:none"; ?>"><?php echo ($currentCartsItems>0) ?$currentCartsItems:"";
	 ?></span><span class="bfi-shopping-cart-text"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART') ?></span></a>
	<div class="bfi-hide bfimodalcart">
		<div class="bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART') ?></div>
		<div class="bfi-body"></div>
		<div class="bfi-footer">
			<span class="bfi-btn bfi-alternative" onclick="jQuery('.bfi-shopping-cart').webuiPopover('destroy');"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART_CONTINUE') ?></span>
			<span onclick="javascript:window.location.assign('<?php echo $url_cart_page ?>')" class="bfi-btn"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART_CHECKOUT')  ?></span>
		</div>
	</div><!-- /.modal -->
  <?php 
	} //$showcurrencyswitcher
  ?>