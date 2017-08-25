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
$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

$merchant = $this->item;
$sitename = $this->sitename;
$language = $this->language;
$offer = $this->items;

$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_OFFERS_TITLE'),$merchant->Name,$sitename));

$db   = JFactory::getDBO();
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = intval($db->loadResult());
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant  = JRoute::_($uriMerchant);
?>

<div class="bfi-content">
	<?php if (!empty($offer)){ ?>
	<div>
		<?php
			$currencyclass = bfi_get_currentCurrency();
			$offer->OfferId =  $offer->VariationPlanId;
			$currvariationPlanId = $offer->VariationPlanId;
//			$offer->Price = $offer->Value;
//			$formRoute = $routeMerchant . '/?task=getMerchantResources&variationPlanId=' . $offer->OfferId;

			$offerName = BFCHelper::getLanguage($offer->Name, $language);
				
		?>
		<div class="bfi-title-name"><?php echo  $offer->Name?> </div>
		<div class="bfi-clearfix "></div>
	
		<ul class="bfi-menu-top">
			<?php if (!empty($offer->Description)){?><li><a rel=".bfi-description-data" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></a></li><?php } ?>
			<li class="bfi-book" ><a rel="#divcalculator"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li>
		</ul>
	
		<div class="bfi-resourcecontainer-gallery">
			<?php 
			$images = array();
			$contextImg ="variationplans";
			if(!empty($offer->Images)) {
			  $strImg = str_replace(' ', '', $offer->Images);
			  foreach(explode(',', $strImg) as $image) {
				  $images[] = array('type' => 'image', 'data' => $image);
			  }
			}
			?>
			<?php  include('gallery.php');  ?>

		</div>
		<?php if (!empty($offer->Description)){?>
		<div class="bfi-description-data bfi-row">
			<div class="bfi-description-data bfi-col-md-12">
				<?php echo $offer->Description ?>		
			</div>
		</div>
		<?php } ?>
		<div class="bfi-clearfix "></div>
	
		<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				$resourceId = 0;
				$condominiumId = 0;

				include(JPATH_COMPONENT.'/views/shared/search_details.php'); ?>
			</div>
	</div>
	
	<?php }else{?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php } ?>

	<div class="bfi-clearboth"></div>
	<?php  include(JPATH_COMPONENT.'/views/shared/merchant_small_details.php');  ?>

</div>
<script type="text/javascript">
jQuery(function($)
		{
			jQuery('.bfcmenu li a').click(function(e) {
				e.preventDefault();
				jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
			});
		});

</script>