<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

?>
<!-- {emailcloak=off} -->
<div class="bfi_form_txt">
	<label for="orderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></label> 
	<input id="orderId" name="externalOrderId" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?>" data-rule-required="true" data-rule-digits="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-msg-digits="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID_ERROR') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="email"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?></label> 
	<input id="email" name="email" type="email" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?>" data-rule-required="true" data-rule-email="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-msg-email="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL_ERROR') ?>" aria-required="true" />
</div>
