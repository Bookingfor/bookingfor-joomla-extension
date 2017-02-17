<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = $this->language;
$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));

$merchant = $this->item;

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchantthanks = $uriMerchant .'&layout=thanks';

$uriMerchant .='&layout=contacts';
$route = JRoute::_($uriMerchant);
$routeThanks = JRoute::_($uriMerchantthanks);

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
//$cLanguageList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_LANGUAGESLIST'));

$nation="";
$culture="";

$privacy = BFCHelper::GetPrivacy($this->language);

?>
<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t<?php echo  $this->item->MerchantTypeId?>">
	<!-- <?php echo  $this->loadTemplate('head'); ?> -->
	<?php if ($this->items != null): 
		$offer = $this->items;
		$offer->OfferId = $offer->VariationPlanId;
		$formRoute = JRoute::_('index.php?option=com_bookingforconnector&format=search&tmpl=component&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name)) . '&offerId=' . $offer->OfferId;

?>
	
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $offer->Name?> </h2>
	<div class="clear"></div>
	
	<ul class="nav nav-pills nav-justified bfcmenu ">
		<li role="presentation" class="active"><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_PHOTO') ?></a></li>
		<?php if (!empty($offer->Description)):?><li role="presentation" ><a rel=".com_bookingforconnector_resource-description" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<li role="presentation" class="book" ><a rel="#divcalculator" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li>
	</ul>
	
	<div class="resourcecontainer-gallery">
		<?php echo  $this->loadTemplate('gallery'); ?>
	</div>
	<?php if (!empty($offer->Description)):?>
	<div class="com_bookingforconnector_resource-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<h4 class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h4>
		<div class="com_bookingforconnector_resource-description-data <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
		<?php echo  BFCHelper::getLanguage($offer->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="clear"></div>
	
	<a name="calc"></a>
	<div id="divcalculator"><div style="padding:10px;text-align:center;"><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</div></div>
	<!-- <div id="firstresources">Loading....</div> -->
	
	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php endif?>
</div>
<script type="text/javascript">
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
jQuery(function($)
		{
			jQuery('.bfcmenu li a').click(function(e) {
				e.preventDefault();
				jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
			});
			
			//$("#firstresources").hide();
			
			$("#divcalculator").load('<?php echo $formRoute?>', function() {
			});
		});

</script>
