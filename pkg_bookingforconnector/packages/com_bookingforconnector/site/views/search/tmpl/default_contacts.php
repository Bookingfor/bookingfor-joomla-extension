<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = $this->language;

$merchants = BFCHelper::getMerchantsSearch('',0,1,null,null);
if(!empty($merchants)){
$merchant = array_values($merchants)[0]; 
?>
							<div class="bfi-content">
								<div class="bfi-check-more" data-type="merchant" data-id="<?php echo $merchant->MerchantId?>" >
									<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_NORESULTS_MOREAVAILABILITY') ?>
									<div class="bfi-check-more-slider">
									</div>
								</div>
							</div>

<?php 

BFCHelper::bfi_get_template('shared/merchant_contacts.php',array("merchant"=>$merchant)); 

}
?>