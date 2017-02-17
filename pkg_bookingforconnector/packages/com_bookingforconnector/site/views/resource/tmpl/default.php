<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
$merchant = $resource->Merchant;
$language = $this->language;

$config = $this->config;
$isportal = $config->get('isportal', 1);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');


$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));

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
$center = $resourceLat . "," . $resourceLon;

$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";

if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	$cap = $resource->ZipCode;
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
}else{
	$addressData = $resource->AddressData;
	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
	$cap = BFCHelper::getItem($addressData, 'cap');
	$comune =  BFCHelper::getItem($addressData, 'comune');
	$provincia = BFCHelper::getItem($addressData, 'provincia');
}
if (empty($indirizzo) && empty($comune) ){

	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
		if (empty($indirizzo)){
			$indirizzo = $resource->MrcAddress;
			$cap = $resource->MrcZipCode;
			$comune = $resource->MrcCityName;
			$provincia = $resource->MrcRegionName;
		}
	}else{
		$addressData = $merchant->AddressData;
		$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
		$cap = BFCHelper::getItem($addressData, 'cap');
		$comune =  BFCHelper::getItem($addressData, 'comune');
		$provincia = BFCHelper::getItem($addressData, 'provincia');
	}
}


$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$merchantRules = "";
if(isset($merchant->Rules)){
	$merchantRules = BFCHelper::getLanguage($merchant->Rules, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
}
$resourceRules = "";
if(isset($resource->Rules)){
	$resourceRules = BFCHelper::getLanguage($resource->Rules, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
}

if (!empty($resource->ServiceIdList)){
	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList, $this->language);
}

$routeRating = JRoute::_('index.php?option=com_bookingforconnector&layout=ratings&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1);

$fromSearch =  BFCHelper::getVar('s','0');
$formRoute = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName) . '&s='.$fromSearch);

$searchedRequest =  array(
	'pricetype' => BFCHelper::getStayParam('pricetype'),
	'rateplanId' => BFCHelper::getStayParam('rateplanId'),
	'variationPlanId' => BFCHelper::getStayParam('variationPlanId'),
	'state' => BFCHelper::getStayParam('state'),
	'gotCalculator' => BFCHelper::getBool('calculate'),
);

?>
<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?>">	
	
	<h2 class="com_bookingforconnector_resource-name hideonextra"><?php echo  $resourceName?> </h2>
	<div class="com_bookingforconnector_resource-address hideonextra">
		<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong>
		<?php if(COM_BOOKINGFORCONNECTOR_ENABLEFAVORITES):?>
			 - <a href="javascript:addCustomURlfromfavTranfert('.com_bookingforconnector_resource-name','<?php echo JURI::current() ?>','<?php echo  $resourceName ?>')" class="com_bookingforconnector_resource_addfavorites"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_RESOURCE_ADDFAVORITES') ?></a>
		<?php endif ?>
	</div>	
	<div class="clear"></div>
<!-- Navigation -->	
	<ul class="nav nav-pills nav-justified bfcmenu  hideonextra">
		<li role="presentation" class="active"><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_PHOTO') ?></a></li>
		<?php if (!empty($resourceDescription)):?><li role="presentation" ><a rel=".com_bookingforconnector_resource-description" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<?php if ($isportal && ($merchant->RatingsContext ==2 || $merchant->RatingsContext ==3)):?><li role="presentation" data-toggle="tab"><a rel=".com_bookingforconnector-resource-ratingslist"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_REVIEWS') ?></a></li><?php endif; ?>
		<?php if (($showResourceMap)) :?><li role="presentation" ><a rel="#resource_map" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_MAP') ?></a></li><?php endif; ?>
		<?php if(!$resource->IsCatalog): ?><li role="presentation " class="book"><a rel="#divcalculator" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li><?php endif; ?>
	</ul>

	<div class="resourcecontainer-gallery hideonextra">
		<?php echo  $this->loadTemplate('gallery'); ?>
	</div>
	<script type="text/javascript">
	<!--
	var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
	var cultureCode = '<?php echo $language ?>';
	var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';

	jQuery(document).ready(function(){
		openGoogleMapResource();
		if (cultureCode.length>1)
		{
			cultureCode = cultureCode.substring(0, 2).toLowerCase();
		}
		if (defaultcultureCode.length>1)
		{
			defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
		}
			
	});
	//-->
	</script>
<!-- Gallery -->
<div class="clear"></div>

		<?php if((isset($resource->EnergyClass) && $resource->EnergyClass>0 ) || (isset($resource->EpiValue) && $resource->EpiValue>0 ) ): ?>
<!-- Table Details --><br />	
	<table class="table table-striped resourcetablefeature  hideonextra">
		<tr>
			<?php if(isset($resource->EnergyClass) && $resource->EnergyClass>0): ?>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3" <?php if(!isset($resource->EpiValue)) {echo "colspan=\"3\"";}?>>
				<div class="energyClass energyClass<?php echo $resource->EnergyClass?>">
				<?php 
					switch ($resource->EnergyClass) {
						case 0: echo "Non impostato"; break;
						case 1: echo "Non classificabile"; break;
						case 2: echo "Immobile esente"; break;
						case 3: echo "In fase di valutazione"; break;
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
			<?php endif ?>
			<?php if(isset($resource->EpiValue) && $resource->EpiValue>0): ?>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_EPIVALUE') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3" <?php if(!isset($resource->EnergyClass)) {echo "colspan=\"3\"";}?>><?php echo $resource->EpiValue?> <?php echo $resource->EpiUnit?></td>
			<?php endif ?>
		</tr>
	</table>
		<?php endif ?>
	<!-- Sconti -->
	<div id="DiscountAnchor" class=" hideonextra"></div>
<!-- Dettagli --><br />	
	<?php if (!empty($resourceDescription)):?>
	<div class="com_bookingforconnector_resource-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> hideonextra">
		<h4 class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h4>
		<div class="com_bookingforconnector_resource-description-data <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
        <?php echo $resourceDescription ?>		
		</div>
	</div>
	<div class="clear"></div>
	<?php endif; ?>

	<div class="clear"></div>

	<?php if (!empty($services) && count($services) > 0):?>
	<div class="com_bookingforconnector_resource-services <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> hideonextra">
		<h4 class="underlineborder <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></h4>
		<?php $count=0; ?>
		<div class="servicesdata <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
		<?php foreach ($services as $service):?>
			<?php
			if ($count > 0) { 
				echo ',';
			}
			?>			
			<span class="com_bookingforconnector_resource-services-service"><?php echo BFCHelper::getLanguage($service->Name, $this->language) ?></span>
			<?php $count += 1; ?>
		<?php endforeach?>
		</div>
	</div>
	<?php endif; ?>
		<?php if(!$resource->IsCatalog): ?>
			<!-- calc -->
			<a name="calc"></a>
			<div id="divcalculator"><div style="padding:10px;text-align:center;"><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</div></div>
		<?php endif; ?>

<?php if(false): ?>
	<?php if ( ($merchantRules != null && $merchantRules != '') || ($resourceRules != null && $resourceRules != '') ):?>
	<div class="com_bookingforconnector_resource-conditions">
		<h4 class="underlineborder"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CONDITIONS') ?></h4>
		<!-- <?php echo $resourceRules != '' ? $resourceRules : $merchantRules; ?> -->
		<?php echo  $merchantRules; ?>
	</div>
	<?php endif; ?>
<!-- Notes -->
	<?php if (!empty($resourceNotes)):?>
	<div class="com_bookingforconnector_resource-conditions">
		<br />
		<?php echo nl2br($resourceNotes); ?>
	</div>
	<?php endif; ?>
<!-- Notes -->
	<?php 
//	$additionalPurpose = BFCHelper::GetAdditionalPurpose($this->language);
	$policy = BFCHelper::GetPolicy($resource->ResourceId ,$this->language);
	
	if (!empty($policy)):?>
	<div class="com_bookingforconnector_resource-conditions">
		<br />
		<?php echo nl2br($policy); ?>
	</div>
	<?php endif; ?>
<?php endif; ?>

</div>

<?php if (($showResourceMap)) :?>
<div id="resource_map" style="width:100%;height:350px"></div>
	<script type="text/javascript">
	<!--
		var mapUnit;
		var myLatlng;

		// make map
		function handleApiReady() {
			myLatlng = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					maxZoom: 17,
					minZoom:7,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("resource_map"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: mapUnit
			  });
		}

		function openGoogleMapResource() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing&callback=handleApiReady";
				document.body.appendChild(script);
			}else{
				if (typeof mapUnit !== 'object'){
					handleApiReady();
				}
			}
			redrawmap()
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

	//-->

	</script>
<?php else: ?>

	<script type="text/javascript">
	<!--
		function openGoogleMapResource() {
		}

	//-->
	</script>

<?php endif; ?>
<br>
<br>
<?php if ($isportal && ($merchant->RatingsContext ==2 || $merchant->RatingsContext ==3)):?>
	<div class="com_bookingforconnector-resource-ratingslist">
	  <?php echo  $this->loadTemplate('ratings'); ?>
	</div>
<?php endif; ?>	
	<script type="text/javascript">
	<!--
if(typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}

	jQuery('.bfcmenu li a').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});

//	jQuery(".bfcmenu").affix({offset: {top: 150} }); 
//
//	jQuery(".bfcmenu").on('affix.bs.affix', function(){
//		jQuery(this).width(jQuery(this).parent().width());
//	});


	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
	};
	jQuery(".com_bookingforconnector_resource-description-data").shorten(shortenOption);

	<?php if(!$resource->IsCatalog): ?>
	var rateplansTags = [];
	jQuery(document).ready(function(){

//		load calculator by ajax
jQuery("#divcalculator").load('<?php echo $formRoute?>' + '&format=calc&tmpl=component&<?php echo http_build_query($searchedRequest)?>', function() {
//		jQuery('#calculatorForm').ajaxSubmit(getAjaxOptions());
// jQuery('#calculatorForm').submit();

});



//// sostituzione ricerca======================
//	selTipo = jQuery('select[name=merchantCategoryId] > option:first-child');
//    if (selTipo.length ) {
//		selTipo.text('<?php echo addslashes($merchant->Name) ?>');
//		selTipo.val("id|<?php echo $merchant->MerchantId ?>");
//		var sel = jQuery("select[name=merchantCategoryId]")
//		sel.val(selTipo.val());
//	}

		
		$htmlDiscount = '';
		var tmpResourceId = <?php echo ($resource->HasRateplans ? $resource->ResourceId : $resource->TypologyId) ?>;
		var queryDiscount = "resourcesId=" + tmpResourceId +"&hasRateplans=<?php echo $resource->HasRateplans?'1':'0' ?>&merchantId=<?php echo $merchant->MerchantId ?>&language=<?php echo $language ?>&task=GetDiscountsByResourceId";
		
//		jQuery.getJSON(urlCheck + "?" + queryDiscount, function(data) {
		jQuery.post(urlCheck, queryDiscount, function(data) {
				$htmlDiscount = '';
				dataVar = [];
				if(data && data.RatePlans){
					jQuery.each(data.RatePlans || [], function(key, ratePlan) {
						if (ratePlan.Enabled )
						{
							if (ratePlan.IsOffer && jQuery.inArray( ratePlan.Tags + "-", rateplansTags )<0 )
							{
								dataVar[ratePlan.RatePlanId] = ratePlan;
								rateplansTags.push(ratePlan.Tags + "-");
							}
							jQuery.each(ratePlan.VariationPlans || [], function(key, variationPlan) {
								if (variationPlan.Visible )
								{
									dataVar[variationPlan.VariationPlanId] = variationPlan;
								}
		//							dataVar.push(variationPlan);
							});
						}
					
					});
				}
				jQuery.each(dataVar , function(key, val) {
					if (val!= null ) {
						
						var name = bookingfor.getXmlLanguage(val.Name,cultureCode,defaultcultureCode);;
						name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
						$htmlDiscount += '<div class="com_bookingforconnector_discount_title">' + name+ '</div>';
						var descr = bookingfor.getXmlLanguage(val.Description,cultureCode,defaultcultureCode);;
						descr = bookingfor.nl2br(jQuery("<p>" + descr + "</p>").text());
						$htmlDiscount += '<div class="com_bookingforconnector_discount_value">' + descr + '</div>';
					}
						
		});
					
		jQuery("#DiscountAnchor").html($htmlDiscount);
		if ($htmlDiscount.length<10)
		{
		jQuery("#DiscountAnchor").hide()
		}else{
			jQuery("#DiscountAnchor").prepend('<span class="checkboxofferslabel"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_OFFER')?></span><br />');
		}
		},'json');

	});
	//-->
	<?php endif; ?>
	</script>
