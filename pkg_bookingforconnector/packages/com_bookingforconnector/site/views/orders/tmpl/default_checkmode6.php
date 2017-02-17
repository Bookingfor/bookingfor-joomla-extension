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
	<label class="control-label" for="externalOrderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></label>
	<div class="controls">
		<input id="externalOrderId" name="externalOrderId" type="text" />
	</div>
</div>	
<div class="control-group">
	<label class="control-label" for="email"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?></label>
	<div class="controls">
		<input id="email" name="email" type="text" />
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
		            email:
		            {
		                required: true,
		                email: true
		            },
		            accetto: "required"
		        },
		        messages:
		        {
		        	externalOrderId: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID_ERROR') ?>",
		            email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL_ERROR') ?>"
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
