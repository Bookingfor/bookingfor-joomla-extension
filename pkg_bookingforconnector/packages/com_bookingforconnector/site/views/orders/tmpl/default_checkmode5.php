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
<div class="control-group">
	<label class="control-label" for="orderId"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?></label>
	<div class="controls">
		<input id="orderId" name="orderId" type="text" />
	</div>
</div>	
<div class="control-group">
	<label class="control-label" for="email"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?></label>
	<div class="controls">
		<input id="email" name="email" type="text" value="<?php echo $user->email; ?>" />
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
		            email:
		            {
		                required: true,
		                email: true
		            },
		            accetto: "required"
		        },
		        messages:
		        {
		        	orderId: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID_ERROR') ?>",
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
