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

include(JPATH_COMPONENT.'/views/shared/merchant_contacts.php'); //merchant contact 

}
?>