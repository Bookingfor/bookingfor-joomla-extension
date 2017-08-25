<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$merchant = $this->item;
$sitename = $this->sitename;
$language = $this->language;
$db   = JFactory::getDBO();
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = intval($db->loadResult());
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant  = JRoute::_($uriMerchant);

$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_CONTACTS_TITLE'),$merchant->Name,$sitename));
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
?>
<div class="bfi-content">
	<br />
	<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS')?>
	<div class="bfi-clearboth"></div>
	<?php  include(JPATH_COMPONENT.'/views/shared/merchant_small_details.php');  ?>
</div>
