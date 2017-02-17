<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


//echo "<pre>this->params";
//echo print_r($this->params);
//echo "</pre>";
$donation = $this->params['donation'];

if($donation){
	echo  $this->loadTemplate('donation'); 
}else{ //else no donation


$checkmode = $this->params['checkmode'];
$route= JRoute::_('index.php?view=orders&checkmode=' . $checkmode);

$order = $this->item;
$email = "";
if(!empty($order)){
	$email = BFCHelper::getItem($order->CustomerData, 'email')."";
}

$actionform =  $this->actionform;

?>
<h2><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_TITLE') ?></h2>

<?php if ($order == null && $checkmode!==0) :?>
	<?php if ($actionform == "login") :?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ERROR') ?></strong>
		</div>
	<?php endif; ?>
<form action="<?php echo  $route ?>" method="post" class="form-horizontal" id="formCheckMode">
	<?php echo  $this->loadTemplate('checkmode'.$checkmode);  ?>
	 <div class="control-group">
		<div class="controls">
			<input type="hidden" id="cultureCode" name="cultureCode" value="<?echo $this->language;?>" />
			<input type="hidden" id="actionform" name="actionform" value="login" />
			<button type="submit" class="button"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SEND') ?></button>
		</div>
	</div>
</form>
<?php else: ?>
	<?php if ($email===""):?>
	<div class="container-fluid">  
	<form action="<?php echo  $route ?>" method="post" class="form-horizontal" id="formEmail">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="control-group">
				<div class="controls">
					<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_EMAIL') ?></label>
					<input name="email" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6" placeholder="email" value="<?php echo $email;?>" >    
					<input type="hidden" id="actionform" name="actionform" value="insertemail" />
					<input type="hidden" name="orderId" value="<?php echo $order->OrderId;?>" />
					<button type="submit" class="button"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SEND') ?></button>
				</div>
			</div>
		</div>
	</form>
	</div>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#formEmail").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        rules:
		        {
		            email:
		            {
		                required: true,
		                email: true
		            },
		        	confirmprivacy : "required"
		        },
		        messages:
		        {
		        	confirmprivacy: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CONFIRM_ERROR') ?>",
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

	<?php else: ?>
		<?php echo  $this->loadTemplate('order'); ?>
	<?php endif; ?>
<?php endif; ?>
<?php 
} //end donation

?>
