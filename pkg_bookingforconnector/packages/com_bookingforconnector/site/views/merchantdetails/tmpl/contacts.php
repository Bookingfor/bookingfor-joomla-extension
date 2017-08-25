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
$language = $this->language;
$base_url = JURI::root();

$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_CONTACTS_TITLE'),$merchant->Name,$sitename));
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $language));
?>
<div class="bfi-content">

<?php 
include(JPATH_COMPONENT.'/views/shared/merchant_contacts.php'); //merchant contact 
?>
</div>