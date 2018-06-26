<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$base_url = JURI::root();

$currencyclass = bfi_get_currentCurrency();

$resource = $this->item;
$resource_id = $resource->ResourceId; //per form contactpopup
$merchant = $resource->Merchant;
$language = $this->language;
$sitename = $this->sitename;

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

$rating_text = array('merchants_reviews_text_value_0' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_0'),
						'merchants_reviews_text_value_1' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_1'),   
						'merchants_reviews_text_value_2' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_2'),
						'merchants_reviews_text_value_3' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_3'),
						'merchants_reviews_text_value_4' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_4'),
						'merchants_reviews_text_value_5' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_5'),  
						'merchants_reviews_text_value_6' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_6'),
						'merchants_reviews_text_value_7' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_7'),  
						'merchants_reviews_text_value_8' =>JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_8'), 
						'merchants_reviews_text_value_9' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_9'),  
						'merchants_reviews_text_value_10' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_10'),                                 
					);

$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br'));


$resourceLat = null;
$resourceLon = null;

if (!empty($resource->XGooglePos) && !empty($resource->YGooglePos)) {
	$resourceLat = $resource->XGooglePos;
	$resourceLon = $resource->YGooglePos;
}
if(!empty($resource->XPos)){
	$resourceLat = $resource->XPos;
}
if(!empty($resource->YPos)){
	$resourceLon = $resource->YPos;
}
if(empty($resourceLat) && !empty($merchant->XPos)){
	$resourceLat = $merchant->XPos;
}
if(empty($resourceLon) && !empty($merchant->YPos)){
	$resourceLon = $merchant->YPos;
}
if(empty($resourceLat) && !empty($merchant->XGooglePos)){
	$resourceLat = $merchant->XGooglePos;
}
if(empty($resourceLon) && !empty($merchant->YGooglePos)){
	$resourceLon = $merchant->YGooglePos;
}

$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) );
$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;

$indirizzo = isset($resource->Address)?$resource->Address:"";
$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
$comune = isset($resource->CityName)?$resource->CityName:"";
$stato = isset($resource->StateName)?$resource->StateName:"";

//
//$merchantRules = "";
//if(isset($merchant->Rules)){
//	$merchantRules = BFCHelper::getLanguage($merchant->Rules, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
//}
//$resourceRules = "";
//if(isset($resource->Rules)){
//	$resourceRules = BFCHelper::getLanguage($resource->Rules, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
//}


$services = [];
$listServices = array();
if (!empty($resource->ServiceIdList)){
	$listServices = explode(",", $resource->ServiceIdList);
	$services = BFCHelper::GetServicesByIds($resource->ServiceIdList,$language);
	$services = array_filter($services, function($p) use ($listServices) {return in_array($p->ServiceId,$listServices);});
}

//if (!empty($resource->ServiceIdList)){
//	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList, $language);
//}

//$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
//$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;);
//
$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	$itemIdMerchant = intval($db->loadResult());
}
if($itemId == 0){
	$itemId = $itemIdMerchant;
}

$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);

if ($itemId<>0){
	$currUriresource.='&Itemid='.$itemId;
}
$resourceRoute = JRoute::_($currUriresource);
$routeRating = JRoute::_($currUriresource.'&layout=rating');				
$routeRatings = JRoute::_($currUriresource.'&layout=ratings&tmpl=component&format=raw');				

$currUriMerchant = $uriMerchant. '&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName);
if ($itemIdMerchant<>0){
	$currUriMerchant.= '&Itemid='.$itemIdMerchant;
}
$routeMerchant = JRoute::_($currUriMerchant,true, -1);

$ProductAvailabilityType = $resource->AvailabilityType;

$reviewavg = 0;
$reviewcount = 0;
$showReview = false;

$summaryRatings = null;

if ($merchant->RatingsContext != NULL && $merchant->RatingsContext > 0) {
	$showReview = true;
	if ($merchant->RatingsContext ==1 && !empty($merchant->Avg)) {
		$reviewavg =  isset($merchant->Avg) ? $merchant->Avg->Average : 0;
		$reviewcount =  isset($merchant->Avg) ? $merchant->Avg->Count : 0;
		$summaryRatings = $merchant->Avg;
	}
	if ($merchant->RatingsContext ==2 || $merchant->RatingsContext ==3 ) {
		$summaryRatings = BFCHelper::getResourceRatingAverage($merchant->MerchantId,$resource->ResourceId);
		if(!empty($summaryRatings)){
			$reviewavg = $summaryRatings->Average;
			$reviewcount = $summaryRatings->Count;
		}
	}
}

/*---------------IMPOSTAZIONI SEO----------------------*/
	$merchantDescriptionSeo = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	$resourceDescriptionSeo = BFCHelper::getLanguage($resource->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 170) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,170);
	}
	if (!empty($resourceDescriptionSeo) && strlen($resourceDescriptionSeo) > 170) {
	    $resourceDescriptionSeo = substr($resourceDescriptionSeo,0,170);
	}

	$titleHead = "$merchantName: $resourceName ($comune, $stato) - $merchant->MainCategoryName - $sitename";
	$keywordsHead = "$merchantName, $resourceName, $comune, $stato, $merchant->MainCategoryName";
	$routeSeo = ($isportal)? $routeMerchant: $base_url;
	$resourceRouteSeo = JRoute::_($currUriresource,true, -1);

	$this->document->setTitle($titleHead);
	$this->document->setDescription($resourceDescriptionSeo);
	$this->document->setMetadata('keywords', $keywordsHead);
	$this->document->setMetadata('robots', "index,follow");
	$this->document->setMetadata('og:title', $titleHead);
	$this->document->setMetadata('og:description', $resourceDescriptionSeo);
	$this->document->setMetadata('og:url', $resourceRouteSeo);

	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = $routeSeo; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}

	$payloadresource["@type"] = "Product";
	$payloadresource["@context"] = "http://schema.org";
	$payloadresource["name"] = $resourceName;
	$payloadresource["description"] = $resourceDescriptionSeo;
	$payloadresource["url"] = $resourceRouteSeo; 
	if (!empty($resource->ImageUrl)){
		$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/


?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payloadresource,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>

<div class="bfi-content bfi-hideonextra">	
	
	<?php if($reviewcount>0){ ?>
	<div class="bfi-row">
		<div class="bfi-col-md-10">
	<?php } ?>
			<div class="bfi-title-name bfi-hideonextra"><h1><?php echo  $resourceName?></h1> - <h2 class="bfi-cursor"><?php echo  $merchantName?></h2></div>
			<div class="bfi-address bfi-hideonextra">
				<i class="fa fa-map-marker fa-1"></i> <?php if (($showResourceMap)) {?><a class="bfi-map-link" rel="#resource_map"><?php } ?><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
				<?php if (($showResourceMap)) {?></a><?php } ?>
			</div>	
	<?php if($reviewcount>0){ 
		$totalreviewavg = BFCHelper::convertTotal($reviewavg);
		?>
		</div>	
		<div class="bfi-col-md-2 bfi-cursor bfi-avg bfi-text-right" id="bfi-avgreview">
			<a href="#bfi-rating-container" class="bfi-avg-value"><?php echo $rating_text['merchants_reviews_text_value_'.$totalreviewavg]; ?> <?php echo number_format($reviewavg, 1); ?></a><br />
			<a href="#bfi-rating-container" class="bfi-avg-count"><?php echo $reviewcount; ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_REVIEWS') ?></a>
		</div>	
	</div>	
	<?php } ?>
	<div class="bfi-clearfix"></div>
<!-- Navigation -->	
	<ul class="bfi-menu-top bfi-hideonextra">
		<?php if (!empty($resourceDescription)){?><li><a rel=".bfi-description-data"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php } ?>
		<?php if($isportal){ ?><li ><a rel=".bfi-merchant-simple"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_HOST') ?></a></li>
		<?php if ($showReview){?><li><a rel=".bfi-ratingslist"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_REVIEWS') ?></a></li><?php } ?><?php } ?>
		<?php if(!$resource->IsCatalog){ ?><li class="bfi-book"><a rel="#divcalculator"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li><?php } ?>
	</ul>
</div>
<div class="bfi-resourcecontainer-gallery bfi-hideonextra">
	<?php  
			$bfiSourceData = 'resources';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($resource->ImageData)) {
				$bfiImageData = $resource->ImageData;
			}
			if(!empty($resource->VideoData)) {
				$bfiVideoData = $resource->VideoData;
			}
				BFCHelper::bfi_get_template('shared/gallery.php',array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
	?>
</div>



<div class="bfi-content">	


	<div class="bfi-row bfi-hideonextra">
		<div class="bfi-col-md-8 bfi-description-data">
			<?php echo $resourceDescription ?>		
		</div>	
		<div class="bfi-col-md-4">
			<div class=" bfi-feature-data">
				<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_INSHORT') ?></strong>
				<div class="bfiresourcegroups" id="bfitags" rel="<?php echo $resource->TagsIdList ?>"></div>
				<?php if(isset($resource->Area) && $resource->Area>0  ){ ?><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?>: <?php echo $resource->Area ?> m&sup2; <br /><?php } ?>
				<?php if ($resource->MaxCapacityPaxes>0){?>
					<br />
					<?php if ($resource->MinCapacityPaxes<$resource->MaxCapacityPaxes){?>
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MINPAXES') ?>: <?php echo $resource->MinCapacityPaxes ?><br />
					<?php } ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MAXPAXES') ?>: <?php echo $resource->MaxCapacityPaxes ?><br />
				<?php } ?>
				<?php if((isset($resource->EnergyClass) && $resource->EnergyClass>0 ) || (isset($resource->EpiValue) && $resource->EpiValue>0 ) ){ ?>
				<!-- Table Details --><br />	
				<table class="bfi-table bfi-table-striped bfi-resourcetablefeature">
					<tr>
						<?php if(isset($resource->EnergyClass) && $resource->EnergyClass>0){ ?>
						<td class="bfi-col-md-"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS') ?>:</td>
						<td class="bfi-col-md-3" <?php if(!isset($resource->EpiValue)) {echo "colspan=\"3\"";}?>>
							<div class="bfi-energyClass bfi-energyClass<?php echo $resource->EnergyClass?>">
							<?php 
								switch ($resource->EnergyClass) {
									case 0: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_NOTSET') ; break;
									case 1: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_NONDESCRIPT') ; break;
									case 2: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_FREEPROPERTY') ; break;
									case 3: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_UNDEREVALUATION') ; break;
									case 100: echo "A+"; break;
									case 101: echo "A"; break;
									case 102: echo "B"; break;
									case 103: echo "C"; break;
									case 104: echo "D"; break;
									case 105: echo "E"; break;
									case 106: echo "F"; break;
									case 107: echo "G"; break;
								}
							?>
							</div>
						</td>
						<?php } ?>
						<?php if(isset($resource->EpiValue) && $resource->EpiValue>0){ ?>
						<td class="bfi-col-md-"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_EPIVALUE') ?></td>
						<td class="bfi-col-md-" <?php if(!isset($resource->EnergyClass)) {echo "colspan=\"3\"";}?>><?php echo $resource->EpiValue?> <?php echo $resource->EpiUnit?></td>
						<?php } ?>
					</tr>
				</table>
				<?php } ?>
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
					<!-- AddToAny BEGIN -->
					<a class="bfi-btn bfi-alternative2 bfi-pull-right a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_SHARE') ?></a>
					<script async src="https://static.addtoany.com/menu/page.js"></script>
					<!-- AddToAny END -->
		</div>	
	</div>

		<?php if(!$resource->IsCatalog){ ?>
			<!-- calc -->
			<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				$resourceId = $resource->ResourceId;
				$condominiumId = 0;
				BFCHelper::bfi_get_template('shared/search_details.php',array("resource"=>$resource,"merchant"=>$merchant,"resourceId"=>$resourceId,"condominiumId"=>$condominiumId,"currencyclass"=>$currencyclass));	
				?>
					

			</div>
		<?php } ?>

	<div class="bfi-clearboth"></div>
<?php
				BFCHelper::bfi_get_template('shared/merchant_small_details.php',array("resource_id"=>$resource_id,"merchant"=>$merchant,"routeMerchant"=>$routeMerchant)); 
?>

<?php if (($showResourceMap)) {?>
	<div class="bfi-content-map bfi-hideonextra">
		<br /><br />
		<div id="resource_map" style="width:100%;height:350px"></div>
	</div>
	<script type="text/javascript">
	<!--
		var mapUnit;
		var myLatlng;

		// make map
		function handleApiReady() {
			myLatlng = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("resource_map"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: mapUnit
			  });
			redrawmap()
		}

		function openGoogleMapResource() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReady";
				document.body.appendChild(script);
			}else{
				if (typeof mapUnit !== 'object'){
					handleApiReady();
				}
			}
		}
		function redrawmap() {
			if (typeof google !== "undefined")
			{
				if (typeof google === 'object' || typeof google.maps === 'object'){
					google.maps.event.trigger(mapUnit, 'resize');
					mapUnit.setCenter(myLatlng);
				}
			}
		}

		jQuery(window).resize(function() {
			redrawmap()
		});
		jQuery(document).ready(function(){
			openGoogleMapResource();
		});

	//-->

	</script>
<?php } ?>
<?php if ($showReview){?>
	<div class="bfi-ratingslist bfi-hideonextra">
	<?php
//	$ratings = null;
//	if(!empty( $reviewcount )){
//		if ($merchant->RatingsContext ==1){
//			$ratings = BFCHelper::getMerchantRatings(0,5,$merchant->MerchantId);
//		}else{
//			$ratings = BFCHelper::getResourceRatings(0,5,$resource->ResourceId);
//		}
//		
//	}
//	include('resource-ratings.php');

	?>
	</div>
<?php } ?>




	<script type="text/javascript">
	<!--
	if(typeof jQuery.fn.button.noConflict !== 'undefined'){
		var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
		jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
	}
	jQuery(function($) {
		jQuery('.bfi-menu-top li a,.bfi-map-link').click(function(e) {
			e.preventDefault();
			jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
		});
		jQuery('#bfi-avgreview').click(function() {
			jQuery('html, body').animate({ scrollTop: jQuery(".bfi-ratingslist").offset().top }, 2000);
		});
		jQuery('.bfi-title-name h2').click(function() {
			jQuery('html, body').animate({ scrollTop: jQuery(".bfi-merchant-simple").offset().top }, 2000);
		});

		<?php if(isset($_REQUEST["pricetype"])){ ?>	
			
			jQuery('html, body').animate({
				scrollTop: jQuery("#divcalculator").offset().top
			}, 1000);

		<?php }  ?>	
<?php if ($showReview){?>
		jQuery.get( "<?php echo $routeRatings ?>", function( data ) {
			jQuery( ".bfi-ratingslist " ).html( data );
		});
<?php } ?>

	});
	function bfiGoToTop() {
		this.event.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(".bfi-title-name ").offset().top }, 2000);
	};
	//-->
	</script>
</div>
