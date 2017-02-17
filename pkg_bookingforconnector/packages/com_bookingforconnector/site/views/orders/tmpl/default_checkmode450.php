<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$document = $this->document;
$language 	= $this->language;

if(substr($language,0,2)!='en'){
//	$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
	$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
}

$date = new JDate('now'); 

?>

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
			<?php echo htmlHelper::calendar(
					$date->format('m/d/Y'),
					'checkIn', 
					'checkIn', 
					'm/d/Y' /*input*/, 
					'd/m/Y' /*output*/, 
					'dd/mm/yy', 
					array('class' => 'calendar'), 
					true, 
					array(
						'minDate' => '\'+0d\'',
						'onSelect' => 'function(dateStr) { $("#formCheckMode").validate().element(this); }'
					)
				) ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="checkOut"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT') ?></label>
	<div class="controls">
				<?php echo htmlHelper::calendar(
					$date->modify("+7 day")->format('m/d/Y'),
					'checkOut', 
					'checkOut', 
					'm/d/Y' /*input*/, 
					'd/m/Y' /*output*/, 
					'dd/mm/yy', 
					array('class' => 'calendar'), 
					true, 
					array(
						'minDate' => '\'+7d\'',
						'onSelect' => 'function(dateStr) { $("#formCheckMode").validate().element(this); }'
					)
				) ?>
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
		        		dateITA: true,
		        		greaterThanDateITA : "#checkIn"
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
			        	dateITA:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_FORMAT_ERROR') ?>",
			        	greaterThanDateITA : "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHECKOUT_GREATTHAN_ERROR') ?>"
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
