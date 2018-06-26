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
	<label for="externalCustomerId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALCUSTOMERID') ?></label>
	<input id="externalCustomerId" name="externalCustomerId" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALCUSTOMERID') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="externalOrderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></label>
	<input id="externalOrderId" name="externalOrderId" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="checkInCheckMode"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN') ?></label>
	<input id="checkInCheckMode" name="checkIn" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN_ERROR') ?>" data-rule-dateITA="true" data-msg-dateITA="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN_FORMAT_ERROR') ?>" aria-required="true" />
</div>
<div class="bfi_form_txt">
	<label for="checkOutCheckMode"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT') ?></label>
	<input id="checkOutCheckMode" name="checkOut" type="text" value="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_ERROR') ?>" data-rule-dateITA="true" data-msg-dateITA="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_FORMAT_ERROR') ?>" data-rule-greaterThanDateITA="#checkInCheckMode" data-msg-greaterThanDateITA="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_GREATTHAN_ERROR') ?>" aria-required="true" />
</div>

<script type="text/javascript">
var checkInCheckMode = null;
var checkOutCheckMode = null;
jQuery(function ($)
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
			checkOutCheckMode = function () {
			    $("#checkOutCheckMode").datepicker({
			        defaultDate: "+2d"
                    , changeMonth: true
                    , changeYear: true
                    , dateFormat: "dd/mm/yy"
                    , beforeShow: function (input, inst) {
						$('#ui-datepicker-div').addClass('notranslate'); 
						setTimeout(function() {bfiCalendarCheck()}, 1);
						}
                    , minDate: '+7d', onSelect: function (dateStr) { $("#formCheckMode").validate().element(this); }
			    })
			};
			checkOutCheckMode();
            //fix Google Translator and datepicker
			$('.ui-datepicker').addClass('notranslate');
		});

</script>	