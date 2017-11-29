<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language 	= $this->language;
$tmpUserId = BFCHelper::bfi_get_userId();
$currCart = BFCHelper::GetCartByExternalUser($tmpUserId, $language, true);

$errorCode = BFCHelper::getVar('errorCode',"0");

$errorMessage = JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS_KO');
if ($errorCode=="1") {
   $errorMessage = JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS_KOPAYMENT');
}
if ($errorCode=="2") {
   $errorMessage = JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS_KOPAYMENT');
}
?>
<div class="bfi-content">	
<br />
		<div class="bfi-alert bfi-alert-danger">
		 <?php echo $errorMessage ?>
		</div>
</div>