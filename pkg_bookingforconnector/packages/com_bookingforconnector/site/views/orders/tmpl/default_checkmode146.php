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
	<label for="orderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></label> 
	<input id="orderId" name="externalOrderId" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?>" data-rule-required="true" data-rule-digits="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-msg-digits="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID_ERROR') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="customerLastname"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME') ?></label>
	<input id="customerLastname" name="customerLastname" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="checkInCheckMode"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN') ?></label>
	<input id="checkInCheckMode" name="checkIn" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-rule-dateITA="true" data-msg-dateITA="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN_FORMAT_ERROR') ?>" aria-required="true" />
</div>

<script type="text/javascript">
var checkInCheckMode = null;
jQuery(function($)
		{
			checkInCheckMode = function() { $("#checkInCheckMode").datepicker({
				defaultDate: "+2d"
				,changeMonth: true
				,changeYear: true
				,dateFormat: "dd/mm/yy"
				,beforeShow: function(input, inst) {
					$('#ui-datepicker-div').addClass('notranslate');
					setTimeout(function() {bfiCalendarCheck()}, 1);
					}
				, minDate: '+0d', onSelect: function(dateStr) { $("#formCheckMode").validate().element(this); }
			})};
			checkInCheckMode();
			//fix Google Translator and datepicker
			$('.ui-datepicker').addClass('notranslate');
		});

</script>	
