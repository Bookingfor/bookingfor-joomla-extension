<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$currencyclass = bfi_get_currentCurrency();

$resource = $this->item;
$merchant = $resource->Merchant;
$resourceId = $resource->ResourceId;
$condominiumId = 0;
$language = $this->language;

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

?>
				<?php 
				$resourceId = $resource->ResourceId;
				$condominiumId = 0;

				include(JPATH_COMPONENT.'/views/shared/search_details.php'); //merchant temp ?>
