<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$merchant = $this->item;
$sitename = $this->sitename;

$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_CONTACTS_TITLE'),$merchant->Name,$sitename));
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
?>
<div class="com_bookingforconnector_merchantdetails">
	<?php echo  $this->loadTemplate('head'); ?>
	<div class="com_bookingforconnector_merchantdetails-contacts">
		<div class="alert alert-error">
		  <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS_KO')?>
		</div>
	</div>
</div>
