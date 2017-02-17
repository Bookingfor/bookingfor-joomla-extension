<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . '/helpers/htmlHelper.php';

// setting per slider prezzi
$priceScaleMin = 0;
$priceScaleMax = 200;
$priceScaleStep = 50;
/*
$document 	= JFactory::getDocument();
$language 	= $document->getLanguage();

$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();
$uri  = 'index.php?option=com_bookingforconnector&view=search';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND language='. $db->Quote($lang) .' AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);
*/


//zone...
$locationZones = BFCHelper::getLocationZones();
$masterTypologies = BFCHelper::getMasterTypologies();

// elenco ulteriore filtri possibili
$merchantGroups = BFCHelper::getMerchantGroups();

$pars = BFCHelper::getSearchParamsSession();

// se  passo questi dati è una nuova ricerca
$masterTypeId = $pars['masterTypeId'];
$merchantCategoryId = $pars['merchantCategoryId'];

if (!empty($merchantCategoryId)) {
	$singeMerchantCategory = BFCHelper::getMerchantCategory($merchantCategoryId);
	$services  = $singeMerchantCategory->Services;
}


$duration = $pars['duration'];
if (empty($duration)) {
	$duration =1;
}


// è il codice per l'ordinamento a random....
$searchid = BFCHelper::getVar('searchid');
$isMerchantResults = $pars['merchantResults'];

$filtersPriceMin = $priceScaleMin;
$filtersPriceMax = $priceScaleMax;

$filters = BFCHelper::getFilterSearchParamsSession();

if (isset($filters)) {
	if (!empty($filters['stars'])) {
		$filtersStars = explode(",", $filters['stars']);
	}
	if (!empty($filters['locationzones'])) {
		$filtersLocationZones = explode(",", $filters['locationzones']);
	}
	if (!empty($filters['merchantgroups'])) {
		$filtersMerchantGroups = explode(",", $filters['merchantgroups']);
	}
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
	}
	if (!empty($filters['mastertypologies'])) {
		$filtersMasterTypologies = explode(",", $filters['mastertypologies']);
	}
	if (!empty($filters['pricemin'])) {
		$filtersPriceMin = 	$filters['pricemin'] / $duration;
	}
	if (!empty($filters['pricemin'])) {
		$filtersPriceMax = 	$filters['pricemax'] / $duration;
	}

}

$filtersEnabled = BFCHelper::getEnabledFilterSearchParamsSession();
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
// ---------------------------------------------
//			echo "<pre>services:";
//			echo print_r($services);
//			echo "</pre>";
//			echo "<pre>filtersEnabledServices:";
//			echo print_r($filtersEnabledServices);
//			echo "</pre>";

?>
<div class="mod_bookingforsearchfilter<?php echo $moduleclass_sfx ?>">
<h3><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_TITLE') ?></h3>
<form action="<?php echo $formAction; ?>" method="post" id="searchformfilter" name="searchformfilter" target="nuovaricerca">
<!-- hidden file -->
	<input type="hidden" value="<?php echo $searchid; ?>" name="searchid" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" value="stay" />
	<input type="hidden" name="filter_order_Dir" value="asc" />
	<input type="hidden" name="format" value="raw" />
	<input type="hidden" name="tmpl" value="component" />

	<?php if ($isMerchantResults) :?>
	<div class="mod_bookingforsearchfilter_star">
		<div><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_STARS') ?></div>
		<?php for ($star = 1; $star <= 5; ++$star) :?>
			<?php if (isset($filtersEnabledStars) &&  is_array($filtersEnabledStars) && in_array($star,$filtersEnabledStars)) :?>
				<?php $checked = '';
					if (isset($filtersStars) &&  is_array($filtersStars) && in_array($star,$filtersStars)){
						$checked = ' checked="checked"';
					}
				?>
					<label class="checkbox inline"><input type="checkbox" name="filtersStars"  class="checkboxstar" value="<?php echo $star ?>" <?php echo $checked ?> /><span class="com_bookingforconnector_merchantdetails-rating com_bookingforconnector_merchantdetails-rating<?php echo $star ?>"><span class="com_bookingforconnector_merchantdetails-ratingText"><?php echo $star ?></span></span></label><br /> 
			<?php endif; ?>
		<?php endfor; ?>
		<hr>
	</div>
	<?php endif ?>
	<div class="mod_bookingforsearchfilter_slider">
		<label for="amount"><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_PRICE') ?></label>
		<div id="amountrange"></div>
		<div id="slider"></div>
		<hr>
	</div>
	<?php if ($locationZones) :?>
	<div class="mod_bookingforsearchfilter_location">
		<div><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_LOCATION') ?></div>
		<?php foreach ($locationZones as $locationZone):?>
			<?php if (isset($filtersEnabledLocationZones) &&  is_array($filtersEnabledLocationZones) && in_array($locationZone->LocationZoneID,$filtersEnabledLocationZones)) :?>
				<?php $checked = '';
					if (isset($filtersLocationZones) &&  is_array($filtersLocationZones) && in_array($locationZone->LocationZoneID,$filtersLocationZones)){
						$checked = ' checked="checked"';
					}
				?>
					<label class="checkbox inline"><input type="checkbox" name="filtersLocationZones"  class="checkboxlocationzones" value="<?php echo ($locationZone->LocationZoneID) ?>" <?php echo $checked ?> /><?php echo $locationZone->Name ?></label><br /> 
			<?php endif; ?>
		<?php endforeach; ?>
		<hr>
	</div>
	<?php endif ?>

	<?php if ($merchantGroups) :?>
	<div class="mod_bookingforsearchfilter_merchantGroups">
		<div><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MERCHANTGROUPS') ?></div>
		<?php foreach ($merchantGroups as $merchantGroup):?>
			<?php if (isset($filtersEnabledMerchantGroups) &&  is_array($filtersEnabledMerchantGroups) && in_array($merchantGroup->MerchantGroupId,$filtersEnabledMerchantGroups)) :?>
				<?php $checked = '';
					if (isset($filtersMerchantGroups) &&  is_array($filtersMerchantGroups) && in_array($merchantGroup->MerchantGroupId,$filtersMerchantGroups)){
						$checked = ' checked="checked"';
					}
				?>
					<label class="checkbox inline"><input type="checkbox" name="filtersMerchantGroups"  class="checkboxmerchantgroups" value="<?php echo ($merchantGroup->MerchantGroupId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($merchantGroup->Name, $language) ?></label><br /> 
			<?php endif; ?>
		<?php endforeach; ?>
		<hr>
	</div>
	<?php endif ?>

	<?php if ($services) :?>
	<div class="mod_bookingforsearchfilter_services">
		<div><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_SERVICES') ?></div>
		<?php foreach ($services as $service):?>
			<?php if (isset($filtersEnabledServices) &&  is_array($filtersEnabledServices) && in_array($service->ServiceId,$filtersEnabledServices)) :?>
				<?php $checked = '';
					if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
						$checked = ' checked="checked"';
					}
				?>
			<?php endif; ?>
					<label class="checkbox inline"><input type="checkbox" name="filtersServices"  class="checkboxservices" value="<?php echo ($service->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($service->Name, $language) ?></label><br /> 
		<?php endforeach; ?>
		<hr>
	</div>
	<?php endif ?>

	<?php if ($masterTypologies) :?>
	<div class="mod_bookingforsearchfilter_mastertypologies">
		<div><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MASTERTYPOLOGIES') ?></div>
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
		<hr>
	</div>
	<?php endif ?>
	<input type="hidden" id="filtersPriceminHidden" name="filters[pricemin]" />
		<input type="hidden" id="filtersPricemaxHidden" name="filters[pricemax]" />
		<input type="hidden" id="filtersStarsHidden" name="filters[stars]" />
		<input type="hidden" id="filtersLocationzoneHidden" name="filters[locationzones]" />
		<input type="hidden" id="filtersMasterTypologiesHidden" name="filters[mastertypologies]" />
		<input type="hidden" id="filtersMerchantGroupsHidden" name="filters[merchantgroups]" />
		<input type="hidden" id="filtersServicesHidden" name="filters[services]" />
		

</form>
</div>
<script type="text/javascript">
var duration = <?php echo $duration; ?>;

jQuery(window).load(function(){
		
	jQuery('#searchformfilter').ajaxForm({
			target:     '#bfcmerchantlist',
			replaceTarget: true, 
			url:        '<?php echo $formAction ?>' + '', 
			beforeSend: function() {
				jQuery('#bfcmerchantlist').block();
			},
			success: showResponse,
			error: showError
	});
		
		// bug in jQuery slider con mootools da aggiungere questa riga
		jQuery('#slider')[0].slide = null;
		jQuery( "#slider" ).slider({      
				range: true,      
				min: <?php echo $priceScaleMin; ?>,      
				max: <?php echo $priceScaleMax; ?>,
				step: <?php echo $priceScaleStep; ?>,      
				values: [ <?php echo $filtersPriceMin; ?>, <?php echo $filtersPriceMax; ?> ],      
				slide: function( event, ui ) {        
					jQuery( "#amountrange" ).html( "&euro;" + ui.values[ 0 ] + " - &euro;" + ui.values[ 1 ] );      
					jQuery( "#filtersPriceminHidden" ).val(ui.values[0] * duration);						
					jQuery( "#filtersPricemaxHidden" ).val(ui.values[1] * duration);      
				},
				change: function( event, ui ) {
					jQuery("#filtersPriceminHidden").closest('form').submit();
				} 
			});
			jQuery( "#amountrange" ).html( "&euro;" + jQuery( "#slider" ).slider( "values", 0 ) +  " - &euro;" + jQuery( "#slider" ).slider( "values", 1 ) );  
			jQuery( "#filtersPriceminHidden" ).val(jQuery( "#slider" ).slider( "values", 0 ) * duration);      
			jQuery( "#filtersPricemaxHidden" ).val(jQuery( "#slider" ).slider( "values", 1 ) * duration);      
		


			jQuery('.checkboxstar').live('click',function() {
				updateHiddenValue('.checkboxstar:checked','#filtersStarsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxlocationzones').live('click',function() {
				updateHiddenValue('.checkboxlocationzones:checked','#filtersLocationzoneHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxmastertypologies').live('click',function() {
				updateHiddenValue('.checkboxmastertypologies:checked','#filtersMasterTypologiesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxmerchantgroups').live('click',function() {
				updateHiddenValue('.checkboxmerchantgroups:checked','#filtersMerchantGroupsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxservices').live('click',function() {
				updateHiddenValue('.checkboxservices:checked','#filtersServicesHidden')	
				jQuery(this).closest('form').submit();
			});
		});

function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }
function showResponse(responseText, statusText, xhr, $form)  { 
    jQuery('#bfcmerchantlist').unblock();
	// for normal html responses, the first argument to the success callback 
    // is the XMLHttpRequest object's responseText property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'xml' then the first argument to the success callback 
    // is the XMLHttpRequest object's responseXML property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'json' then the first argument to the success callback 
    // is the json data object returned by the server 
 
//    alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
//        '\n\nThe output div should have already been updated with the responseText.'); 
}
function showError(responseText, statusText, xhr, $form)  { 
    jQuery('#bfcmerchantlist').html('<?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_NORESULT') ?>')
	jQuery('#bfcmerchantlist').unblock();
	// for normal html responses, the first argument to the success callback 
    // is the XMLHttpRequest object's responseText property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'xml' then the first argument to the success callback 
    // is the XMLHttpRequest object's responseXML property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'json' then the first argument to the success callback 
    // is the json data object returned by the server 
 
//    alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
//        '\n\nThe output div should have already been updated with the responseText.'); 
}



</script>
