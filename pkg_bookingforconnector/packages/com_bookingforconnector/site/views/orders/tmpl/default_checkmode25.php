<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<!--/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */-->
<div class="control-group">
	<label class="control-label" for="orderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?></label>
	<div class="controls">
		<input id="orderId" name="orderId" type="text" />
	</div>
</div>	
<div class="control-group">
	<label class="control-label" for="customerFirstname"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_FIRSTNAME') ?></label>
	<div class="controls">
		<input id="customerFirstname" name="customerFirstname" type="text" />
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="customerLastname"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME') ?></label>
	<div class="controls">
		<input id="customerLastname" name="customerLastname" type="text" />
	</div>
</div>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#formCheckMode").validate(
		    {
		        rules:
		        {
		        	orderId: "required",
		        	customerFirstname: "required",
		        	customerLastname: "required"
		        },
		        messages:
		        {
		        	orderId: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID_ERROR') ?>",
		        	customerFirstname: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_FIRSTNAME_ERROR') ?>",
		        	customerLastname: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME_ERROR') ?>"
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
