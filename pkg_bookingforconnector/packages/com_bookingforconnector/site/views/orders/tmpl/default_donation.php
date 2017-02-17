<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$urlPayment = JRoute::_('index.php?view=payment');

?>
<!-- <h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DATAORDER') ?></h3> -->
				
<form action="<?php echo $urlPayment?>"  method="post" class="form-inline" style="margin-bottom:0" id="otherpayment">
	<div>
		<label for="name"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_NAME') ?>*</label><br />
		<input type="text" name="name"  placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_NAME') ?>" /><br />
	</div>
	<div>
		<label for="email"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?>*</label><br />
		<input type="text" name="email"  placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?>" /><br />
	</div>
	<div>
		<label for="Amount"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYMENTAMOUNT') ?>*</label><br />
		<input type="text" name="Amount" id="Amount" value="" placeholder="0.00" />
	</div>
	<div>
		<label for="causale"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CAUSALE') ?>*</label><br />
		<textarea id="causale" name="causale"></textarea>
	</div>
	<div>
		<br />
		<input type="hidden" name="actionmode" value="donation" />
		<input type="submit" class="btn btn-warning " value="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERPAYMENTS_PAY') ?>" />
	</div>
</form>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#otherpayment").validate(
		    {
		        rules:
		        {
		            email:
		            {
		                required: true,
		                email: true
		            },
		            name:
		            {
		                required: true
		            },
		            causale:
		            {
		                required: true,
						maxlength: 90
		            },
		        	Amount: {
						required: true,
						TwoDecimal: true,
						MinDecimal: true
						}
		        },
		        messages:
		        {
		            email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL_ERROR') ?>",
		            name: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_NAME_ERROR') ?>",
		            causale: {
							required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CAUSALE_ERROR') ?>",
							maxlength:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CAUSALE_ERROR_MAXLENGTH') ?>"
							},
		        	Amount: {
						required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYIMPORT_ERROR') ?>",
						MinDecimal:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYIMPORT_ERROR') ?>",
						TwoDecimal: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYIMPORT_ERROR_TWODECIMAL') ?>"
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
			//$("#overrideAmount").mask("9999?.99",{placeholder:"0"});   
		});

</script>	
