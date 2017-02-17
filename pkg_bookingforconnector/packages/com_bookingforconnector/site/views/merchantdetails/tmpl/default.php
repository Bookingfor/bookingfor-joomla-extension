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

$config = $this->config;
$isportal = $config->get('isportal', 1);
$posx = $config->get('posx', 0);
$posy = $config->get('posy', 0);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');

//$this->document->setDescription( BFCHelper::getLanguage($merchant->Description, $this->language));
$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_TITLE'),$merchant->Name, $sitename));

$merchantRules ='';
if(!empty($merchant->Rules)){
$merchantRules = BFCHelper::getLanguage($merchant->Rules, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
}

$showResourcePlanimetria=false;
$showResourceVideo=false;


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

$fromSearch =  BFCHelper::getVar('s');
//for search resource
$formRoute = JRoute::_('index.php?option=com_bookingforconnector&format=search&tmpl=component&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name) . '&s='.$fromSearch) ;
?>
<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t<?php echo BFCHelper::showMerchantRatingByCategoryId($merchant->MerchantTypeId)?>">
	<?php echo  $this->loadTemplate('head'); ?>
<!-- Navigation -->	
	<ul class="nav nav-pills nav-justified bfcmenu ">
		<li role="presentation" class="active"><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_PHOTO') ?></a></li>
		<?php if (!empty($resourceDescription)):?><li role="presentation" ><a rel=".com_bookingforconnector_resource-description" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?><li role="presentation" data-toggle="tab"><a rel=".com_bookingforconnector-resource-ratingslist"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_REVIEWS') ?></a></li><?php endif; ?>
		<?php if (($showMap)) :?><li role="presentation" ><a rel="#merchant_map" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_MAP') ?></a></li><?php endif; ?>
		<?php if ($merchant->HasResources):?><li role="presentation " class="book"><a rel="#divcalculator" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li><?php endif; ?>
	</ul>
	<div class="resourcecontainer-gallery">
	  <?php echo  $this->loadTemplate('gallery'); ?>
	</div>

   <div class="com_bookingforconnector_merchant-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<h4 class="underlineborder <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_DESC') ?></h4>
		<div class="com_bookingforconnector_resource-description-data <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
        <?php echo  BFCHelper::getLanguage($merchant->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>		
		</div>
	</div>
	
<?php if ($merchant->HasResources):?>
	<a name="calc"></a>
	<div id="divcalculator"><div style="padding:10px;text-align:center;"><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</div></div>
<?php endif; ?>	

	<?php 
	$services = [];
	if (!empty($merchant->ServiceIdList)){
		$services = BFCHelper::GetServicesByIds($merchant->ServiceIdList,$this->language);
	}
	?>
	<?php if (!empty($services) && count($services ) > 0):?>
	<div class="com_bookingforconnector_resource-services <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<h4 class="underlineborder <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></h4>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
		<?php 
		$count=0;
		?>
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
	<br /><br />
	<?php if (($showMap)) :?>
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
					maxZoom: 17,
					minZoom:7,
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
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing&callback=handleApiReadyMerchant";
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
	<br>
<br>
<?php endif; ?>

<?php if ($merchant->HasResources):?>
	<div id="firstresources">Loading....</div>
<?php endif; ?>
	
<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
	<br /><br />
	<div class="com_bookingforconnector-resource-ratingslist">
		<?php echo  $this->loadTemplate('ratings'); ?>
	</div>
<?php endif; ?>
</div>
<script type="text/javascript">

var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';

jQuery(function($) {
	jQuery('.bfcmenu li a').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});
	$('.moduletable-insearch').show();
	//load first resourses
<?php if ($merchant->HasResources):?>
//	var pagelist = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&layout=resourcesajax&tmpl=component&format=raw&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name)) ?>";
//		$("#firstresources").load(pagelist, function() {
//	});
	$("#firstresources").hide();
	
	jQuery("#divcalculator").load('<?php echo $formRoute?>', function() {
	});

<?php endif ?>


	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
	};
	jQuery(".com_bookingforconnector_resource-description-data").shorten(shortenOption);

//// sostituzione ricerca======================
//
//	selTipo = jQuery('select[name=merchantCategoryId] > option:first-child');
//    if (selTipo.length ) {
//		selTipo.text('<?php echo addslashes($merchant->Name) ?>');
//		selTipo.val("id|<?php echo $merchant->MerchantId ?>");
//		var sel = jQuery("select[name=merchantCategoryId]")
//		sel.val(selTipo.val());
//	}

});
</script>

