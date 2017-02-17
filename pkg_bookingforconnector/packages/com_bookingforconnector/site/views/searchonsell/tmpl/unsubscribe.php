<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.module.helper');

$hashLenght = 32;

$params = $this->params;
$language = $this->language;
$hashunsubscribe = $this->hashunsubscribe;
$hash = $hashunsubscribe;
if (strlen($hashunsubscribe)>$hashLenght){
	$hash = substr($hashunsubscribe,0, $hashLenght);    
	$id = substr($hashunsubscribe, $hashLenght,strlen($hashunsubscribe)-$hashLenght);    
}
$result = BFCHelper::unsubscribeAlertOnSell($hash, $id);
?>
<div id="bfcOnsellunitslist">

		<h1 class="com_bookingforconnector_search-title"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_UNSUBSCRIBE_TITLE') ?></h1>

<?php if($result):  ?>
	<div class="alert alert-success">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_UNSUBSCRIBE_OK') ?>
	</div>
<?php else:  ?>
	<div class="alert alert-error">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_UNSUBSCRIBE_KO') ?>
	</div>
<?php endif  ?>

</div>
