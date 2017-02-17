<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//
//$db   = JFactory::getDBO();
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
////$itemIdMerchant = intval($db->loadResult());
//
//$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
//
//if ($itemIdMerchant<>0)
//	$uriMerchant.='&Itemid='.$itemIdMerchant;
//
//$uriMerchant .='&layout=contacts';
//$route = JRoute::_($uriMerchant);

?>
<div class="com_bookingforconnector_merchantdetails">
	<div class="com_bookingforconnector_merchantdetails-contacts">
	<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS')?>
	</div>
</div>
