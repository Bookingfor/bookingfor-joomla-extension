<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
$merchant = $resource->Merchant;

//$maxCapacityPaxes = $resource->MaxCapacityPaxes;
//$minCapacityPaxes = $resource->MinCapacityPaxes;
//			$Extras =  $this->Extras;
//			$PriceTypes =  null;//$this->PriceTypes;
//			$MerchantBookingTypes =  $this->MerchantBookingTypes;

?>
<div id="booknow" class="ajaxReload"><?php echo  $this->loadTemplate('calculator_rateplan'); ?></div>
