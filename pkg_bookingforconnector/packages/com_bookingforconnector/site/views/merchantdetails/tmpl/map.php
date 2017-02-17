<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
$layout = BFCHelper::getString('layout','default');

$language = $this->language;

//$route = JRoute::_('index.php?view=merchantdetails&merchantId=' . $this->item->MerchantId . ':' . BFCHelper::getSlug($this->item->Name));

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $this->item->MerchantId . ':' . BFCHelper::getSlug($this->item->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$route = JRoute::_($uriMerchant);

?>
<div class="mapdetails">
<h2 class="com_bookingforconnector_merchant-name"><a class="com_bookingforconnector_merchantdetails-nameAnchor" href="<?php echo $route ?>"><?php echo  $this->item->Name?></a>
  <span class="com_bookingforconnector_resource-merchant-rating">
  <?php for($i = 0; $i < $this->item->Rating; $i++) { ?>
    <i class="fa fa-star"></i>
  <?php } ?>
  </span>
</h2>
</div>
