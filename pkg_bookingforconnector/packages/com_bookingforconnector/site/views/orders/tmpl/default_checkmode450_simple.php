<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<!--
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */-->
<div class="control-group">
	<label class="control-label" for="externalCustomerId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALCUSTOMERID') ?></label>
	<div class="controls">
		<input id="externalCustomerId" name="externalCustomerId" type="text" />
	</div>
</div>	<div class="control-group">
	<label class="control-label" for="externalOrderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></label>
	<div class="controls">
		<input id="externalOrderId" name="externalOrderId" type="text" />
	</div>
</div>	
<div class="control-group">
	<label class="control-label" for="checkIn"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN') ?></label>
	<div class="controls">
		<input id="checkIn" name="checkIn" type="text" />
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="checkOut"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT') ?></label>
	<div class="controls">
		<input id="checkOut" name="checkOut" type="text" />
	</div>
</div>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#formCheckMode").validate(
		    {
		        rules:
		        {
		        	externalOrderId: "required",
		        	externalCustomerId: "required",
		        	checkIn: {
			        		required: true,
			        		dateITA: true
			        	},
		        	checkOut:  {
		        		required: true,
		        		dateITA: true
		        	}
		        },
		        messages:
		        {
		        	externalOrderId: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID_ERROR') ?>",
		        	externalCustomerId: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALCUSTOMERID_ERROR') ?>",
		        	checkIn: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN_ERROR') ?>",
		        		dateITA:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKIN_FORMAT_ERROR') ?>"
		        		},
		        	checkOut:  {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_ERROR') ?>",
			        	dateITA:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_FORMAT_ERROR') ?>"
		        		}
		        	},
		        highlight: function(label) {
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.text('ok!').addClass('valid')
			    		.closest('.control-group').removeClass('error').addClass('success');
			    }
		    });
		});

</script>	
