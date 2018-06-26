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
$base_url = JURI::root();

$merchant = $this->item;
$sitename = $this->sitename;
$language = $this->language;
$offer = $this->items;

$db   = JFactory::getDBO();
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = intval($db->loadResult());
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant  = JRoute::_($uriMerchant);
$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";

/*---------------IMPOSTAZIONI SEO----------------------*/
	$merchantDescriptionSeo = BFCHelper::getLanguage($offer->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 170) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,170);
	}
	$titleHead = "$merchantName: $offer->Name ($comune, $stato) - $merchant->MainCategoryName - " . JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_OFFER') . " - $sitename";
	$keywordsHead = "$offer->Name, $merchantName, $comune, $stato, $merchant->MainCategoryName, " . JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_OFFER') ;
	$routeSeo = ($isportal)? $routeMerchant: $base_url;

	$this->document->setTitle($titleHead);
	$this->document->setDescription($merchantDescriptionSeo);
	$this->document->setMetadata('keywords', $keywordsHead);
	$this->document->setMetadata('robots', "index,follow");
	
	$this->document->setMetadata('og:type', "Organization");
	$this->document->setMetadata('og:title', $titleHead);
	$this->document->setMetadata('og:description', $merchantDescriptionSeo);
	$this->document->setMetadata('og:url', $routeSeo);

	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = $routeSeo; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/

?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>

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
		<div class="bfi-title-name"><h1><?php echo  $offer->Name?></h1> </div>
		<div class="bfi-clearfix "></div>
	
		<ul class="bfi-menu-top">
			<?php if (!empty($offer->Description)){?><li><a rel=".bfi-description-data" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></a></li><?php } ?>
			<li class="bfi-book" ><a rel="#divcalculator"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li>
		</ul>
	
		<div class="bfi-resourcecontainer-gallery">
	<?php  
			$bfiSourceData = 'variationplans';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($offer->Images)) {
				$bfiImageData = $offer->Images;
			}
			if(!empty($offer->VideoData)) {
				$bfiVideoData = $offer->VideoData;
			}
			BFCHelper::bfi_get_template('shared/gallery.php',array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
	?>

		</div>
		<?php if (!empty($offer->Description) || (isset($resource->AttachmentsString) && !empty($resource->AttachmentsString)) ){?>
		<div class="bfi-description-data bfi-row">
			<div class="bfi-col-md-8 bfi-description-data">
				<?php echo BFCHelper::getLanguage($offer->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')); ?>
			</div>	
			<div class="bfi-col-md-4">
				<div class=" bfi-feature-data">
					<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_INSHORT') ?></strong>
					<?php if(isset($resource->AttachmentsString) && !empty($resource->AttachmentsString)){
						?>
						<div  class="bfi-attachmentfiles">
						<?php 
									
						$resourceAttachments = json_decode($resource->AttachmentsString);
						
						foreach ($resourceAttachments as $keyAttachment=> $resourceAttachment) {
							if ($keyAttachment>COM_BOOKINGFORCONNECTOR_MAXATTACHMENTFILES) {
								break;
							}
							$resourceAttachmentName = $resourceAttachment->Name;
							$resourceAttachmentExtension= "";
							
							$path_parts = pathinfo($resourceAttachmentName);
							if(!empty( $path_parts['extension'])){
								$resourceAttachmentExtension = $path_parts['extension'];
								$resourceAttachmentName =  str_replace(".".$resourceAttachmentExtension, "", $resourceAttachmentName);
							}
							$resourceAttachmentIcon = bfi_get_file_icon($resourceAttachmentExtension);
							?>
							<?php echo $resourceAttachmentIcon ?> <a href="<?php echo $resourceAttachment->LinkValue ?>" target="_blank"><?php echo $resourceAttachmentName ?></a><br />
							<?php 
						}
					?>
						</div>
					<?php } ?>
				</div>
			</div>
					<!-- AddToAny BEGIN -->
					<a class="bfi-btn bfi-alternative2 bfi-pull-right a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_SHARE') ?></a>
					<script async src="https://static.addtoany.com/menu/page.js"></script>
					<!-- AddToAny END -->

		</div>
		<?php } ?>
		<div class="bfi-clearfix "></div>
	
		<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				$resourceId = 0;
				$condominiumId = 0;
				BFCHelper::bfi_get_template('shared/search_details.php',array("merchant"=>$merchant,"resourceId"=>$resourceId,"condominiumId"=>$condominiumId,"currvariationPlanId"=>$currvariationPlanId,"currencyclass"=>$currencyclass));	
//				include(JPATH_COMPONENT.'/views/shared/search_details.php');
				?>
			</div>
	</div>
	
	<?php }else{?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php } ?>

	<div class="bfi-clearboth"></div>
<?php
				BFCHelper::bfi_get_template('shared/merchant_small_details.php',array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant)); 
?>

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