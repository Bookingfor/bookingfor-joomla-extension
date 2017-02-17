<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$db   = JFactory::getDBO();
$language = $this->language;
$uri  = 'index.php?option=com_bookingforconnector&view=search';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND language='. $db->Quote($language) .' AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId.'' );
else
	$formAction = JRoute::_($uri);

// setting per slider prezzi
$priceScaleMin = 0;
$priceScaleMax = 300;
$priceScaleStep = 50;

//zone...
$locationZones = BFCHelper::getLocationZones();
$masterTypologies = BFCHelper::getMasterTypologies();

// elenco ulteriore filtri possibili
$merchantGroups = BFCHelper::getTags($language,"1,4");

$bookingTypes = array();
//$bookingTypes[0] = JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES_REQUEST');
$bookingTypes[6] = JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES_BOOK');

$offers = array();
$offers[1] = JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS_OFFER');

$pars = BFCHelper::getSearchParamsSession();

// se  passo questi dati è una nuova ricerca
$masterTypeId = $pars['masterTypeId'];
$merchantCategoryId = $pars['merchantCategoryId'];


if (!empty($merchantCategoryId)) {
	
//	$singeMerchantCategory = BFCHelper::getMerchantCategory($merchantCategoryId);
//	$services  = $singeMerchantCategory->Services;
	$services  =  BFCHelper::getServicesByMerchantsCategoryId($merchantCategoryId,$language);
}else{
	$services  =  BFCHelper::getServicesForSearch($language);

}


$duration = 1;// $pars['duration'];
if (empty($duration)) {
	$duration =1;
}


// è il codice per l'ordinamento a random....
$searchid = BFCHelper::getVar('searchid');
//$searchid =  $this->params['searchid'];
$isMerchantResults = $pars['merchantResults'];



$filtersEnabled = BFCHelper::getEnabledFilterSearchParamsSession();
//echo '<PRE>';print_r($filtersEnabled);die();
if (isset($filtersEnabled)) {
	if (!empty($filtersEnabled['stars'])) {
		$filtersEnabledStars = explode(",", $filtersEnabled['stars']);
	}
	if (!empty($filtersEnabled['locationzones'])) {
		$filtersEnabledLocationZones = explode(",", $filtersEnabled['locationzones']);
	}
	if (!empty($filtersEnabled['mastertypologies'])) {
		$filtersEnabledMasterTypologies = explode(",", $filtersEnabled['mastertypologies']);
	}
	if (!empty($filtersEnabled['merchantgroups'])) {
		$filtersEnabledMerchantGroups = explode(",", $filtersEnabled['merchantgroups']);
	}
	if (!empty($filtersEnabled['services'])) {
		$filtersEnabledServices = explode(",", $filtersEnabled['services']);
	}
	if (!empty($filtersEnabled['bookingtypes'])) {
		$filtersEnabledBookingTypes = explode(",", $filtersEnabled['bookingtypes']);
	}
	if (!empty($filtersEnabled['pricemin'])) {
		$priceScaleMin = $filtersEnabled['pricemin'];
		if($priceScaleMin<0){
			$priceScaleMin =0;
		}
	}
	if (!empty($filtersEnabled['pricemax'])) {
		$priceScaleMax = $filtersEnabled['pricemax'];
	}
	
	if (!empty($filtersEnabled['offers'])) {
 
		
		$filtersEnableOffers = explode(",", $filtersEnabled['offers']);
	}

}

$filtersPriceMin = $priceScaleMin;
$filtersPriceMax = $priceScaleMax;
if($filtersPriceMin ==$filtersPriceMax){
	$filtersPriceMax +=1;
}
if (($filtersPriceMax-$filtersPriceMin)<$priceScaleStep){
	$priceScaleStep = $filtersPriceMax-$filtersPriceMin;
}
if ($priceScaleStep<$priceScaleMax){
	$priceScaleStep= 1;
}


$filters = BFCHelper::getFilterSearchParamsSession();

$filtersStarsValue = "";
$filtersLocationZonesValue = "";
$filtersMerchantGroupsValue = "";
$filtersServicesValue = "";
$filtersMasterTypologiesValue = "";
$filtersBookingTypesValue = "";
$filtersOffersValue = "";

if (isset($filters)) {
	if (!empty($filters['stars'])) {
		$filtersStars = explode(",", $filters['stars']);
		$filtersStarsValue = $filters['stars'];
	}
	if (!empty($filters['locationzones'])) {
		$filtersLocationZones = explode(",", $filters['locationzones']);
		$filtersLocationZonesValue = $filters['locationzones'];
	}
	if (!empty($filters['merchantgroups'])) {
		$filtersMerchantGroups = explode(",", $filters['merchantgroups']);
		$filtersMerchantGroupsValue = $filters['merchantgroups'];
	}
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
		$filtersServicesValue = $filters['services'];
	}
	if (!empty($filters['mastertypologies'])) {
		$filtersMasterTypologies = explode(",", $filters['mastertypologies']);
		$filtersMasterTypologiesValue = $filters['mastertypologies'];
	}
	if (!empty($filters['pricemin'])) {
		$filtersPriceMin = 	$filters['pricemin'];// / $duration;
	}
	if (!empty($filters['pricemax'])) {
		$filtersPriceMax = 	$filters['pricemax'];// / $duration;
	}
	if (!empty($filters['bookingtypes'])) {
		$filtersBookingTypes = explode(",", $filters['bookingtypes']);
		$filtersBookingTypesValue = $filters['bookingtypes'];
	}
	if (!empty($filters['offers'])) {
		$filtersOffers = explode(",", $filters['offers']);
		$filtersOffersValue = $filters['offers'];
	}

}
//prezzi

//le stelle..... recupero eventualmente la prima checcata...
//				for ($star = 1; $star <= 5; ++$star) {
//					$checked = '';
//					if ( isset($filters) && !empty($filters['stars']) && $filters['stars']==$star) {
//						$checked = ' checked="checked"';
//					}
//                }
//			echo "<pre>filtersParam:";
//			echo print_r($pars);
//			echo "</pre>";
//			echo "<pre>filters:";
//			echo print_r($filters);
//			echo "</pre>";
//			echo "<pre>locationZones:";
//			echo print_r($locationZones);
//			echo "</pre>";
//			echo "<pre>filtersEnabled:";
//			echo print_r($filtersEnabled);
//			echo "</pre>";
//// ---------------------------------------------
//			echo "<pre>services:";
//			echo print_r($services);
//			echo "</pre>";
//			echo "<pre>filtersEnabledServices:";
//			echo print_r($filtersEnabledServices);
//			echo "</pre>";
//
//			echo "<pre>filtersBookingTypes:";
//			echo print_r($filtersBookingTypes);
//			echo "</pre>";
//			echo "<pre>filtersEnabledBookingTypes:";
//			echo print_r($filtersEnabledBookingTypes);
//			echo "</pre>";
?>
<form action="<?php echo $formAction; ?>" method="post" id="searchformfilter" name="searchformfilter" >
<!-- hidden file -->
	<input type="hidden" value="<?php echo $searchid; ?>" name="searchid" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" id="filter_order_filter" value="" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir_filter" value="" />
	<input type="hidden" name="format" value="raw" />
	<input type="hidden" name="tmpl" value="component" />
<div id="filtertoggle">
	<ul>

		<?php if (isset($filtersEnableOffers) &&  is_array($filtersEnableOffers)) : ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?></li>
			<div class="mod_bookingforsearchfilter_offers divtoggle">
				<?php foreach ($offers as $offer => $offerText):?>
					<?php if (isset($filtersEnableOffers) &&  is_array($filtersEnableOffers) && in_array($offer,$filtersEnableOffers)) :?>
						<?php $checked = '';
							if (isset($filtersOffers) &&  is_array($filtersOffers) && in_array($offer,$filtersOffers)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox inline"><input type="checkbox" name="filtersOffers"  class="checkboxoffers" value="<?php echo $offer ?>" <?php echo $checked ?> /><div class="checkboxofferslabel"><?php echo $offerText ?></div></label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php if (isset($isMerchantResults) && $isMerchantResults) :?>
		   <?php if(isset($filtersEnabledStars) && is_array($filtersEnabledStars)) : ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MERCHANTSTARS') ?></li>
			<div class="mod_bookingforsearchfilter_star divtoggle">
				<?php for ($star = 1; $star <= 5; ++$star) :?>
					<?php if (isset($filtersEnabledStars) &&  is_array($filtersEnabledStars) && in_array($star,$filtersEnabledStars)) :?>
						<?php $checked = '';
							if (isset($filtersStars) &&  is_array($filtersStars) && in_array($star,$filtersStars)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox inline"><input type="checkbox" name="filtersStars"  class="checkboxstar" value="<?php echo $star ?>" <?php echo $checked ?> /><span class="com_bookingforconnector_merchantdetails-rating com_bookingforconnector_merchantdetails-rating<?php echo $star ?>"><span class="com_bookingforconnector_merchantdetails-ratingText">
							<?php for($i=0; $i < $star; $i++) { ?>
							  <i class="fa fa-star"></i>
							<?php } ?>
							</span></span></label>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<?php endif; ?>
		<?php else :?>
			<?php if (isset($filtersEnabledBookingTypes) &&  is_array($filtersEnabledBookingTypes)) : ?>
				<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES') ?></li>
				<div class="mod_bookingforsearchfilter_bookingtypes divtoggle">
					<?php foreach ($bookingTypes as $bookingType => $bookingTypeText):?>
						<?php if (isset($filtersEnabledBookingTypes) &&  is_array($filtersEnabledBookingTypes) && in_array($bookingType,$filtersEnabledBookingTypes)) :?>
							<?php $checked = '';
								if (isset($filtersBookingTypes) &&  is_array($filtersBookingTypes) && in_array($bookingType,$filtersBookingTypes)){
									$checked = ' checked="checked"';
								}
							?>
								<label class="checkbox inline"><input type="checkbox" name="filtersBookingTypes"  class="checkboxbookingtypes" value="<?php echo $bookingType ?>" <?php echo $checked ?> /><?php echo $bookingTypeText ?></label>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif ?>
			<?php if (isset($filtersEnabledStars) &&  is_array($filtersEnabledStars))  :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_RESOURCESTARS') ?></li>
			<div class="mod_bookingforsearchfilter_star divtoggle">
				<?php for ($star = 1; $star <= 5; ++$star) :?>
					<?php if (isset($filtersEnabledStars) &&  is_array($filtersEnabledStars) && in_array($star,$filtersEnabledStars)) :?>
						<?php $checked = '';
							if (isset($filtersStars) &&  is_array($filtersStars) && in_array($star,$filtersStars)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox inline"><input type="checkbox" name="filtersStars"  class="checkboxstar" value="<?php echo $star ?>" <?php echo $checked ?> /><span class="com_bookingforconnector_merchantdetails-resourcerating com_bookingforconnector_merchantdetails-resourcerating<?php echo $star ?>"><span class="com_bookingforconnector_merchantdetails-ratingText"><?php echo $star ?></span></span></label>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<?php endif ?>
		<?php endif ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_PRICE') ?></li>
			<div class="mod_bookingforsearchfilter_slider divtoggle">
				<div id="amountrange"></div>
				<div id="sliderfilerting"></div>
			</div>
		<?php if (isset($locationZones) && isset($filtersEnabledLocationZones)) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_LOCATION') ?></li>
			<div class="mod_bookingforsearchfilter_location divtoggle">
				<?php foreach ($locationZones as $locationZone):?>
					<?php if (isset($filtersEnabledLocationZones) &&  is_array($filtersEnabledLocationZones) && in_array($locationZone->LocationZoneID,$filtersEnabledLocationZones)) :?>
						<?php $checked = '';
							if (isset($filtersLocationZones) &&  is_array($filtersLocationZones) && in_array($locationZone->LocationZoneID,$filtersLocationZones)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox inline"><input type="checkbox" name="filtersLocationZones"  class="checkboxlocationzones" value="<?php echo ($locationZone->LocationZoneID) ?>" <?php echo $checked ?> /><?php echo $locationZone->Name ?></label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php if (isset($merchantGroups) && isset($filtersEnabledMerchantGroups)) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MERCHANTGROUPS') ?></li>
			<div class="mod_bookingforsearchfilter_merchantGroups divtoggle">
				<?php foreach ($merchantGroups as $merchantGroup):?>
					<?php if (isset($filtersEnabledMerchantGroups) &&  is_array($filtersEnabledMerchantGroups) && in_array($merchantGroup->MerchantGroupId,$filtersEnabledMerchantGroups)) :?>
						<?php $checked = '';
							if (isset($filtersMerchantGroups) &&  is_array($filtersMerchantGroups) && in_array($merchantGroup->MerchantGroupId,$filtersMerchantGroups)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox inline"><input type="checkbox" name="filtersMerchantGroups"  class="checkboxmerchantgroups" value="<?php echo ($merchantGroup->MerchantGroupId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($merchantGroup->Name, $language) ?></label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
		<?php if (isset($services) && isset($filtersEnabledServices) ) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_SERVICES') ?></li>
			<div class="mod_bookingforsearchfilter_services divtoggle">
				<?php foreach ($services as $service):?>
					<?php if (isset($filtersEnabledServices) &&  is_array($filtersEnabledServices) && in_array($service->ServiceId,$filtersEnabledServices)) :?>
						<?php $checked = '';
							if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox inline"><input type="checkbox" name="filtersServices"  class="checkboxservices" value="<?php echo ($service->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($service->Name, $language) ?></label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php if (isset($masterTypologies) && isset($filtersEnabledMasterTypologies)) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MASTERTYPOLOGIES') ?></li>
			<div class="mod_bookingforsearchfilter_mastertypologies divtoggle">
				<?php foreach ($masterTypologies as $masterTypology):?>
					<?php if (isset($filtersEnabledMasterTypologies) &&  is_array($filtersEnabledMasterTypologies) && in_array($masterTypology->MasterTypologyId,$filtersEnabledMasterTypologies)) :?>
						<?php $checked = '';
							if (isset($filtersMasterTypologies) &&  is_array($filtersMasterTypologies) && in_array($masterTypology->MasterTypologyId,$filtersMasterTypologies)){
								$checked = ' checked="checked"';
							}
						?>
						<label class="checkbox inline"><input type="checkbox" name="filtersMasterTypologies"  class="checkboxmastertypologies" value="<?php echo ($masterTypology->MasterTypologyId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($masterTypology->Name, $language) ?></label><br /> 
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
	</ul>
</div>
<div class="clearboth"></div>
	<input type="hidden" id="filtersStarsHidden" name="filters[stars]" value="<?php echo $filtersStarsValue ?>" />
	<input type="hidden" id="filtersLocationzoneHidden" name="filters[locationzones]" value="<?php echo $filtersLocationZonesValue ?>" />
	<input type="hidden" id="filtersMerchantGroupsHidden" name="filters[merchantgroups]" value="<?php echo $filtersMerchantGroupsValue ?>" />
	<input type="hidden" id="filtersServicesHidden" name="filters[services]" value="<?php echo $filtersServicesValue ?>" />
	<input type="hidden" id="filtersMasterTypologiesHidden" name="filters[mastertypologies]" value="<?php echo $filtersMasterTypologiesValue ?>" />
	<input type="hidden" id="filtersPriceminHidden" name="filters[pricemin]" value="<?php echo $filtersPriceMin ?>"/>
	<input type="hidden" id="filtersPricemaxHidden" name="filters[pricemax]" value="<?php echo $filtersPriceMax ?>" />
	<input type="hidden" id="filtersBookingTypesHidden" name="filters[bookingtypes]" value="<?php echo $filtersBookingTypesValue ?>" />
	<input type="hidden" id="filtersOffersHidden" name="filters[offers]" value="<?php echo $filtersOffersValue ?>" />
</form>
</div>

<script type="text/javascript">
duration = <?php echo $duration; ?>;
ajaxFormAction = '<?php echo $formAction; ?>' + '';
priceScaleMin = <?php echo $priceScaleMin; ?>;
priceScaleMax = <?php echo $priceScaleMax; ?>;
priceScaleStep = <?php echo $priceScaleStep; ?>;
filtersPriceMin = <?php echo $filtersPriceMin; ?>;
filtersPriceMax = <?php echo $filtersPriceMax; ?>;


function applyfilterdata(){ 		

		//show order filter
		jQuery('.com_bookingforconnector-sort-item').show();
		
		jQuery("#filtertoggle li").click(function(){
			jQuery(this).toggleClass("active");
			jQuery(this).next("div").stop('true','true').slideToggle("slow",function() {
				if (jQuery.prototype.masonry){
					jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
				}
			});
		});
	
	jQuery('#searchformfilter').ajaxForm({
			target:     '#bfcmerchantlist',
			replaceTarget: true, 
			url:        ajaxFormAction, 
			beforeSerialize:function() {
				try
				{
//					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchForm input[name='filter_order']").val());					
//					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchForm input[name='filter_order_Dir']").val());	
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
			},
			beforeSend: function() {
				jQuery('#bfcmerchantlist').block();
			},
			success: showResponse,
			error: showError
	});
		
		// bug in jQuery slider con mootools da aggiungere questa riga
		jQuery('#sliderfilerting')[0].slide = null;
		jQuery( "#sliderfilerting" ).slider({      
				range: true,      
				min: priceScaleMin,      
				max: priceScaleMax,
				step: priceScaleStep,      
				values: [ filtersPriceMin, filtersPriceMax ],      
				slide: function( event, ui ) {        
					jQuery( "#amountrange" ).html( "&euro;" + ui.values[ 0 ] + " - &euro;" + ui.values[ 1 ] );      
					jQuery( "#filtersPriceminHidden" ).val(ui.values[0] * duration);						
					jQuery( "#filtersPricemaxHidden" ).val(ui.values[1] * duration);      
				},
				change: function( event, ui ) {
					jQuery("#filtersPriceminHidden").closest('form').submit();
				} 
			});
			jQuery( "#amountrange" ).html( "&euro;" + jQuery( "#sliderfilerting" ).slider( "values", 0 ) +  " - &euro;" + jQuery( "#sliderfilerting" ).slider( "values", 1 ) );  
			jQuery( "#filtersPriceminHidden" ).val(jQuery( "#sliderfilerting" ).slider( "values", 0 ) * duration);      
			jQuery( "#filtersPricemaxHidden" ).val(jQuery( "#sliderfilerting" ).slider( "values", 1 ) * duration);      
		


			jQuery('.checkboxstar').on('click',function() {
				updateHiddenValue('.checkboxstar:checked','#filtersStarsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxlocationzones').on('click',function() {
				updateHiddenValue('.checkboxlocationzones:checked','#filtersLocationzoneHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxmastertypologies').on('click',function() {
				updateHiddenValue('.checkboxmastertypologies:checked','#filtersMasterTypologiesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxmerchantgroups').on('click',function() {
				updateHiddenValue('.checkboxmerchantgroups:checked','#filtersMerchantGroupsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxservices').on('click',function() {
				updateHiddenValue('.checkboxservices:checked','#filtersServicesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxbookingtypes').on('click',function() {
				updateHiddenValue('.checkboxbookingtypes:checked','#filtersBookingTypesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxoffers').on('click',function() {
				updateHiddenValue('.checkboxoffers:checked','#filtersOffersHidden')	
				jQuery(this).closest('form').submit();
			});
	
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}

//		jQuery(".divtoggle").hide();
//	});
}


function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }



</script>
