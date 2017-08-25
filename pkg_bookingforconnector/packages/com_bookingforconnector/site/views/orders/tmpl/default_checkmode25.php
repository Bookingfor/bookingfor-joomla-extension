<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<div class="bfi_form_txt">
	<label for="orderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?></label> 
	<input id="orderId" name="orderId" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?>" data-rule-required="true" data-rule-digits="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-msg-digits="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID_ERROR') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="customerFirstname"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_FIRSTNAME') ?></label>
	<input id="customerFirstname" name="customerFirstname" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_FIRSTNAME') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="customerLastname"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME') ?></label>
	<input id="customerLastname" name="customerLastname" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true" />
</div>