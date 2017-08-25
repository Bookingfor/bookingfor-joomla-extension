<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

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