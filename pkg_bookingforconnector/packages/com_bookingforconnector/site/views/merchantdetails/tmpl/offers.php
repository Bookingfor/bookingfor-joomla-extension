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
$offers = $this->items;
$analyticsListName = $this->analyticsListName;
$listNameAnalytics = $this->listNameAnalytics;
$fromsearchparam = "&lna=".$listNameAnalytics;

$total = $this->pagination->total;

$db   = JFactory::getDBO();
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = intval($db->loadResult());
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant  = JRoute::_($uriMerchant.$fromsearchparam);

$listsId = array();

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = (int)$merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
	$hasSuperior = ($MerchantDetail->Rating%10)>0;
} 

$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";

/*---------------IMPOSTAZIONI SEO----------------------*/
	$merchantDescriptionSeo = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 170) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,170);
	}
	$titleHead = "$merchantName ($comune, $stato) - " . JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_OFFERS') . " - $sitename";
	$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName, " . JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_OFFERS') ;
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
	$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
	$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MainCategoryName);

?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>
<div class="bfi-content">
<div class="bfi-row">
	<div class="bfi-title-name bfi-hideonextra"><h1><?php echo  $merchant->Name?></h1>
		<span class="bfi-item-rating">
			<?php for($i = 0; $i < $rating; $i++) { ?>
			<i class="fa fa-star"></i>
			<?php } ?>
			<?php if ($hasSuperior) { ?>
				&nbsp;S
			<?php } ?>
		</span>
	</div>
		<div class="bfi-search-title">
			<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_OFFERS_TITLE_TOTAL'), $total);?>
		</div>
</div>	
<div class="bfi-search-menu">
	<div class="bfi-view-changer">
		<div class="bfi-view-changer-selected"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
		<div class="bfi-view-changer-content">
			<div id="list-view"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div id="grid-view" class="bfi-view-changer-grid"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
		</div>
	</div>
</div>
<div class="bfi-clearfix"></div>
	<?php if ($offers != null){ ?>
		<div id="bfi-list" class="bfi-row bfi-list">
			<?php foreach($offers as $currKey=>$resource){ ?>
			<?php
		$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags')); 
		$resourceRoute  = JRoute::_($uriMerchant.'&layout=offer&offerId=' . $resource->VariationPlanId . ':' . BFCHelper::getSlug($resourceName).$fromsearchparam);
		if(!empty($resource->DefaultImg)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('variationplans',$resource->DefaultImg, 'medium');
		}
		$resourceNameTrack =  BFCHelper::string_sanitize($resourceName);

			?>
				<div class="bfi-col-sm-6 bfi-item">
					<div class="bfi-row bfi-sameheight" >
						<div class="bfi-col-sm-3 bfi-img-container">
							<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;' target="_blank" class="eectrack" data-type="Offer" data-id="<?php echo $resource->VariationPlanId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
						</div>
						<div class="bfi-col-sm-9 bfi-details-container">
							<!-- merchant details -->
							<div class="bfi-row" >
								<div class="bfi-col-sm-10">
									<div class="bfi-item-title">
										<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->VariationPlanId?>" target="_blank" class="eectrack" data-type="Offer" data-id="<?php echo $resource->VariationPlanId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><?php echo  $resource->Name ?></a> 
									</div>
									<div class="bfi-description"><?php echo $resourceDescription ?></div>
								</div>
							</div>
							<div class="bfi-clearfix bfi-hr-separ"></div>
							<!-- end merchant details -->
							<!-- resource details -->
							<div class="bfi-row" >
								<div class="bfi-col-sm-8">
								
								</div>
								<div class="bfi-col-sm-4 bfi-text-right">
										<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack" target="_blank" data-type="Offer" data-id="<?php echo $resource->VariationPlanId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_VIEWOFFER') ?></a>
								</div>
							</div>
							<!-- end resource details -->
							<div class="bfi-clearfix"></div>
							<!-- end price details -->
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php }else{?>
	<div class="bfi-noresults">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_NORESULT')?>
	</div>
	<?php } ?>	
	<div class="bfi-clearboth"></div>
<?php
				BFCHelper::bfi_get_template('shared/merchant_small_details.php',array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant)); 
?>
	</div>

<script type="text/javascript">
<!--
	jQuery('#list-view').click(function() {
		jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
		jQuery('#bfi-list').removeClass('bfi-grid-group')
		jQuery('#bfi-list .bfi-item').addClass('bfi-list-group-item')
		jQuery('#bfi-list .bfi-img-container').addClass('bfi-col-sm-3')
		jQuery('#bfi-list .bfi-details-container').addClass('bfi-col-sm-9')

		localStorage.setItem('display', 'list');
	});

	jQuery('#grid-view').click(function() {
		jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
		jQuery('#bfi-list').addClass('bfi-grid-group')
		jQuery('#bfi-list .bfi-item').removeClass('bfi-list-group-item')
		jQuery('#bfi-list .bfi-img-container').removeClass('bfi-col-sm-3')
		jQuery('#bfi-list .bfi-details-container').removeClass('bfi-col-sm-9')
		localStorage.setItem('display', 'grid');
	});
		jQuery('#bfi-list .bfi-item').addClass('bfi-grid-group-item')

	if (localStorage.getItem('display')) {
		if (localStorage.getItem('display') == 'list') {
			jQuery('#list-view').trigger('click');
		} else {
			jQuery('#grid-view').trigger('click');
		}
	} else {
	 if(typeof bfi_variable === 'undefined' || bfi_variable.bfi_defaultdisplay === 'undefined') {
			jQuery('#list-view').trigger('click');
		 } else {
			if (bfi_variable.bfi_defaultdisplay == '1') {
				jQuery('#grid-view').trigger('click');
			} else { 
				jQuery('#list-view').trigger('click');
			}
		}
	}

	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
   };
   jQuery(document).ready(function() {
	  jQuery(".bfi-description").shorten(shortenOption);
   });
//-->
</script>