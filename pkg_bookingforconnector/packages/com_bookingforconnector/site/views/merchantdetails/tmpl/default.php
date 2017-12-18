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

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

$currencyclass = bfi_get_currentCurrency();

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

$resourceLat = null;
$resourceLon = null;

if (!empty($merchant->XGooglePos) && !empty($merchant->YGooglePos)) {
	$resourceLat = $merchant->XGooglePos;
	$resourceLon = $merchant->YGooglePos;
}
if(!empty($merchant->XPos)){
	$resourceLat = $merchant->XPos;
}
if(!empty($merchant->YPos)){
	$resourceLon = $merchant->YPos;
}
$showMap = (($resourceLat != null) && ($resourceLon !=null) ); 

$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";

$fromSearch =  BFCHelper::getVar('fromsearch','0');

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());

$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant = JRoute::_($uriMerchant,true, -1);
$routeRating = JRoute::_($uriMerchant.'&layout=rating');				
$routeRatings = JRoute::_($uriMerchant.'&layout=ratings');				


$rating = $merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
} 
$reviewavg = isset($merchant->Avg) ? $merchant->Avg->Average : 0;
$reviewcount = isset($merchant->Avg) ? $merchant->Avg->Count : 0;
$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
$merchantDescription = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ;

/*---------------IMPOSTAZIONI SEO----------------------*/
	$merchantDescriptionSeo = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 170) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,170);
	}
	$titleHead = "$merchantName ($comune, $stato) - $merchant->MainCategoryName - $sitename";
	$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName";
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
<div class="bfi-content bfi-hideonextra">

	<?php if($reviewcount>0){ ?>
	<div class="bfi-row">
		<div class="bfi-col-md-10">
	<?php } ?>
		<div class="bfi-title-name bfi-hideonextra"><h1><?php echo  $merchant->Name?></h1>
			<span class="bfi-item-rating">
				<?php for($i = 0; $i < $rating; $i++) { ?>
				<i class="fa fa-star"></i>
				<?php } ?>
			</span>
		</div>
		<div class="bfi-address bfi-hideonextra">
			<i class="fa fa-map-marker fa-1"></i> <?php if (($showMap)) :?><a class="bfi-map-link" rel="#merchant_map"><?php endif; ?><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
			<?php if (($showMap)) :?></a><?php endif; ?>
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
	<ul class="bfi-menu-top">
		<?php if (!empty($merchantDescription)):?><li ><a rel=".bfi-description-data"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?><li><a rel=".bfi-ratingslist"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_REVIEWS') ?></a></li><?php endif; ?>
		<?php if (($showMap)) :?><li><a rel="#merchant_map"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_MAP') ?></a></li><?php endif; ?>
		<?php if ($merchant->HasResources):?><li class="bfi-book"><a rel="#divcalculator" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li><?php endif; ?>
	</ul>
</div>

	<div class="bfi-resourcecontainer-gallery">
		<?php  include('merchant-gallery.php');  ?>
	</div>

<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-md-8 bfi-description-data">
			<?php echo $merchantDescription ?>		
		</div>	
		<div class="bfi-col-md-4">
			<div class="bfi-feature-data">
				<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_INSHORT') ?></strong>
				<div id="bfi-merchant-tags"></div>
			</div>
				<!-- AddToAny BEGIN -->
				<a class="bfi-btn bfi-alternative2 bfi-pull-right a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_SHARE') ?></a>
				<script async src="https://static.addtoany.com/menu/page.js"></script>
				<!-- AddToAny END -->
		</div>	
	</div>

	<?php if ($merchant->HasResources){?>
		<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				$resourceId = 0;
				$condominiumId = 0;

				include(JPATH_COMPONENT.'/views/shared/search_details.php'); //merchant temp ?>
					

			</div>
	<?php } ?>	
	<div class="bfi-clearboth"></div>
	<?php 
	$services = [];
	if (!empty($merchant->ServiceIdList)){
		$services = BFCHelper::GetServicesByIds($merchant->ServiceIdList,$language);
	}
	?>	
	<?php if (!empty($services) && count($services ) > 0){?>
		<div class="bfi-facility"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></div>
		<div class="bfi-facility-list">
			<?php 
			$count=0;
			?>
			<?php foreach ($services as $service){?>
				<?php
				if ($count > 0) { 
					echo ',';
				}
				?>			
				<?php echo BFCHelper::getLanguage($service->Name, $language) ?>
				<?php $count += 1; ?>
			<?php } ?>
		</div>
	<?php } ?>	

	<div class="bfi-clearboth"></div>
	<?php  include(JPATH_COMPONENT.'/views/shared/merchant_small_details.php');  ?>

	<?php if (($showMap)) {?>
	<br /><br />
<div id="merchant_map" style="width:100%;height:350px"></div>
	<script type="text/javascript">
	<!--
		var mapMerchant;
		var myLatlngMerchant;

		// make map
		function handleApiReadyMerchant() {
			myLatlngMerchant = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlngMerchant,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapMerchant = new google.maps.Map(document.getElementById("merchant_map"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlngMerchant,
				  map: mapMerchant
			  });
		}

		function openGoogleMapMerchant() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "//maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReadyMerchant";
				document.body.appendChild(script);
			}else{
				if (typeof mapMerchant !== 'object'){
					handleApiReadyMerchant();
				}
			}
			redrawmap()
		}
		function redrawmap() {
			if (typeof google !== "undefined")
			{
				if (typeof google === 'object' || typeof google.maps === 'object'){
					google.maps.event.trigger(mapMerchant, 'resize');
					mapMerchant.setCenter(myLatlngMerchant);
				}
			}
		}

		jQuery(window).resize(function() {
			redrawmap()
		});

		jQuery(document).ready(function(){
				openGoogleMapMerchant();
		});

	//-->
	</script>
<?php } ?>

<?php if ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3){?>
	<div class="bfi-ratingslist">
	<?php
		$summaryRatings = BFCHelper::getRatingByMerchantId($merchant->MerchantId);
//		$modelmerchant->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
		$ratings = BFCHelper::getMerchantRatings(0,5,$merchant->MerchantId);
//		if ( false !== ( $temp_message = get_transient( 'temporary_message' ) )) :
//			echo $temp_message;
//			delete_transient( 'temporary_message' );
//		endif;
	?>
		<?php include('merchant-ratings.php'); ?>
	</div>
<?php } ?>
	
</div>
<script type="text/javascript">
	var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
	var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
	var bfiMrcTags = '<?php echo $merchant->TagsIdList; ?>';
	var bfiTagsMg = [];
	var bfiTagsMgLoaded=false;
	function bfiGetTagsMg(){
		if (!bfiTagsMgLoaded && bfiMrcTags != null && bfiMrcTags != '')
		{
			bfiTagsMgLoaded=true;
			var queryMG = "task=getMerchantGroups";
			jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
					if(data!=null){
						jQuery.each(JSON.parse(data) || [], function(key, val) {
							if (val.ImageUrl!= null && val.ImageUrl!= '') {
								var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
								var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
								/*--------getName----*/
								var $name = val.Name;
								/*--------getName----*/
								bfiTagsMg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
							} else {
								if (val.IconSrc != null && val.IconSrc != '') {
									bfiTagsMg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
								}
							}
						});	
						var mglist = bfiMrcTags .split(',');
						$htmlmg = '<span class="bfcmerchantgroup">';
						jQuery.each(mglist, function(key, mgid) {
							if(typeof bfiTagsMg[mgid] !== 'undefined' ){
								$htmlmg += bfiTagsMg[mgid];
							}
						});
						$htmlmg += '</span>';
						jQuery("#bfi-merchant-tags").html($htmlmg);
						jQuery('[data-toggle="tooltip"]').tooltip({
							position : { my: 'center bottom', at: 'center top-10' },
							tooltipClass: 'bfi-tooltip bfi-tooltip-top '
							});
					}
			},'json');
		}
	}
	
jQuery(function($) {
	jQuery('.bfi-menu-top li a,.bfi-map-link').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});
	
	jQuery('#bfi-avgreview').click(function() {
		jQuery('html, body').animate({ scrollTop: jQuery(".bfi-ratingslist").offset().top }, 2000);
	});
	
	bfiGetTagsMg();
	jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 

});
</script>