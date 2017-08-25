<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language 	= $this->language;
$checkmode = $this->params['checkmode'];
$route = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ORDERURL);
 
?>
<h2><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_TITLE') ?></h2>

<form action="<?php echo  $route ?>" method="post" class="bfi-form-vertical" id="formCheckMode" target="_blank">
	<div class="bfi-form-field">		
		<?php echo  $this->loadTemplate('checkmode'.$checkmode);  ?>
		<input type="hidden" id="cultureCode" name="cultureCode" value="<?php echo $language;?>" />
		<input type="hidden" id="actionform" name="actionform" value="login" />
		<input name="checkmode" type="hidden" value="<?php echo $checkmode;?>">
		<div class="bfi-text-center" >
			<br />
			<button type="submit" class="bfi-btn"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SEND') ?></button>
		</div>
	</div>
</form>		
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("#formCheckMode").validate({
			errorClass: "invalid",
			errorElement: "em",
		});
	});
</script>
