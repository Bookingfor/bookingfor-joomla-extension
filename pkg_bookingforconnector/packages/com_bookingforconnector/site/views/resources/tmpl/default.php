<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$config = $this->config;
$isportal = $config->get('isportal', 1);
$showdata = $config->get('showdata', 1);
$searchid = -1;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$language = $this->language;

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());

$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
	//-------------------pagina per il redirect di tutti i merchant

	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
	$itemIdMerchant = intval($db->loadResult());
	//-------------------pagina per il redirect di tutti i merchant

	//-------------------pagina per il redirect di tutte le risorse in vendita favorite
}
if($itemId == 0){
	$itemId = $itemIdMerchant;
}

$resourceImageUrl = Juri::root() . "media/com_bookingfor/images/default.png";
$merchantImageUrl = Juri::root() . "media/com_bookingfor/images/DefaultLogoList.jpg";

$resourceLogoPath = BFCHelper::getImageUrlResized('resources',"[img]", 'medium');
$resourceLogoPathError = BFCHelper::getImageUrl('resources',"[img]", 'medium');

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";


$results = $this->items;
?>
<script type="text/javascript">
<!--
var cultureCode = '<?php echo $language ?>';
//-->
</script>
<div id="com_bookingforconnector-items-container-wrapper">
<h1><?php echo $activeMenu->title?></h1>
	  <div class="com_bookingforconnector-items-container" >
<div class="com_bookingforconnector-search-menu">
	<!-- <form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
	<fieldset class="filters">
		<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
		<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
		<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
		<input type="hidden" name="limitstart" value="0" />
	</fieldset>
	</form>
	<div class="com_bookingforconnector-results-sort" style="display:none;">
		<span class="com_bookingforconnector-sort-help"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
		<span class="com_bookingforconnector-sort-item" rel="stay|asc"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_PRICE'); ?></span>
		<span class="com_bookingforconnector-sort-item" rel="rating|desc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_GUEST_RATING'); ?></span>
		<span class="com_bookingforconnector-sort-item" rel="offer|desc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_OFFERS'); ?></span>
	</div> -->
	<div class="com_bookingforconnector-view-changer">
		<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
		<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
	</div>
</div>
<div class="clearfix"></div>
<div class="com_bookingforconnector-search-resources com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">


<?php foreach ($results as $result):?>
	<?php 
	$resourceName = BFCHelper::getLanguage($result->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

	$resourceLat = $result->XPos;
	$resourceLon = $result->YPos;
	
	$showResourceMap = (($resourceLat != null) && ($resourceLon !=null));
	
	$currUriresource = $uri.'&resourceId=' . $result->ResourceId . ':' . BFCHelper::getSlug($resourceName);

	if ($itemId<>0)
		$currUriresource.='&Itemid='.$itemId;
	
	$resourceRoute = JRoute::_($currUriresource);
	$routeRating = JRoute::_($currUriresource.'&layout=rating');
	$routeInfoRequest = JRoute::_($currUriresource.'&layout=inforequestpopup&tmpl=component');
	$routeRapidView = JRoute::_($currUriresource.'&layout=rapidview&tmpl=component');

	$routeMerchant = "";
	if($isportal){
		$currUriMerchant = $uriMerchant. '&merchantId=' . $result->MerchantId . ':' . BFCHelper::getSlug($result->MerchantName);
		if ($itemIdMerchant<>0)
			$currUriMerchant.= '&Itemid='.$itemIdMerchant;
		$routeMerchant = JRoute::_($currUriMerchant);
	}
	
	if(!empty($result->ImageUrl)){
		$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$result->ImageUrl, 'medium');
	}
$result->MinPaxes = $result->MinCapacityPaxes;
$result->MaxPaxes = $result->MaxCapacityPaxes;

//getStayFromParameter
	?>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" >
			<div class="com_bookingforconnector-search-resource com_bookingforconnector-item <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="mrcgroup" id="bfcmerchantgroup<?php echo $result->ResourceId; ?>"><span class="bfcmerchantgroup"></span></div>
				<div class="com_bookingforconnector-item-details  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
							<div id="com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" class="carousel" data-ride="carousel" data-interval="false">
								<div class="carousel-inner" role="listbox">
										<div class="item active"><img src="<?php echo $resourceImageUrl; ?>"></div>
								</div>
								<?php if($isportal): ?>
									<a class="com_bookingforconnector-search-resource-logo com_bookingforconnector-logo-grid" href="<?php echo $routeMerchant?>" id="merchantname<?php echo $result->ResourceId?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" id="com_bookingforconnector-logo-grid-<?php echo $result->ResourceId?>" src="<?php echo $merchantLogoUrl; ?>" /></div></a>
								<?php endif; ?>
								<a class="left carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="prev">
									<i class="fa fa-chevron-circle-left"></i>
								</a>
								<a class="right carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="next">
									<i class="fa fa-chevron-circle-right"></i>
								</a>
							</div>
						</div>
						<div class="com_bookingforconnector-search-merchant-details com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div class="com_bookingforconnector-search-merchant-name com_bookingforconnector-item-primary-name">
								<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor" href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $result->ResourceId?>"><?php echo  $resourceName; ?></a>
							</div>
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-address">
								<?php if ($showResourceMap):?>
									<a href="javascript:void(0);" onclick="showMarker(<?php echo $result->ResourceId?>)"><span class="address<?php echo $result->ResourceId?>"></span></a>
								<?php endif; ?>
							</div>
							<?php if($showdata): ?>
								<div class="com_bookingforconnector-merchant-description" id="descr<?php echo $result->ResourceId?>"></div>
							<?php endif; ?>
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-phone-inforequest"> 
								<span class="com_bookingforconnector_phone"><a  href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $result->MerchantId ?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes($result->MerchantName) ?>','PhoneView' )"  class="phone<?php echo $result->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span>
								<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
							</div>
						</div><!--  COL 6-->
						<?php if($isportal): ?>
							<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
								<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list" href="<?php echo $resourceRoute ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogoUrl; ?>" id="com_bookingforconnector-logo-list-<?php echo $result->ResourceId?>" /></a>
							</div> <!--  COL 2-->
						<?php endif; ?>
					</div>
						<div class="clearfix"></div>

						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-1 secondarysectionitem">	 
								<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes">
									<i class="fa fa-user"></i>
									<?php if ($result->MinPaxes == $result->MaxPaxes):?>
										<?php echo  $result->MaxPaxes ?>
									<?php else: ?>
										<?php echo  $result->MinPaxes ?>-<?php echo  $result->MaxPaxes ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector-item-secondary-section-2 secondarysectionitem">
									<div class="com_bookingforconnector-search-resource-details-price com_bookingforconnector-item-secondary-price">
										<span class="com_bookingforconnector-gray-highlight" id="totaldays<?php echo $result->ResourceId?>"></span>
										<div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
											<span id="resourcestaytotal<?php echo $result->ResourceId?>" class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total"><i class="fa fa-spinner fa-spin"></i></span>
											<span class="com_bookingforconnector_strikethrough"><span id="resourcestaydiscount<?php echo $result->ResourceId?>"  class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount"></span></span>
										</div>
									</div>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 com_bookingforconnector-item-secondary-section-3 secondarysectionitem">
								<a href="<?php echo $resourceRoute ?>" class="com_bookingforconnector-item-secondary-more"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
							</div>
						</div>
		</div>
				<div class="clearfix"><br /></div>
	  </div>
	<?php 
	$listsId[]= $result->ResourceId;
	?>
<?php endforeach; ?>
</div>
		<?php if ($this->pagination->get('pages.total') > 1) : ?>
			<div class="text-center">
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			</div>
		<?php endif; ?>
   </div>
   </div>
<?php echo  $this->loadTemplate('googlemap'); ?>

<script type="text/javascript">
<!--

if(typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}


jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	localStorage.setItem('display', 'list');
})

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	localStorage.setItem('display', 'grid');
})

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfc_display === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfc_display == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}

var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var imgPathmerchant = "<?php echo $merchantLogoPath ?>";
var imgPathmerchantError = "<?php echo $merchantLogoPathError ?>";

var imgPath = "<?php echo $resourceLogoPath ?>";
var imgPathError = "<?php echo $resourceLogoPathError ?>";
var strAddressSimple = " ";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var strRatingNoResult = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_NO_RESULT')?>";
var strRatingBased = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_BASED')?>";
var strRatingValuation = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_VALUATION')?>";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};


function getAjaxInformations(){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}
	
	var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>&task=GetResourcesByIds";
	
	var imgPathresized =  imgPath.substring(0,imgPath.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";

	var imgPathmerchantresized =  imgPathmerchant.substring(0,imgPathmerchant.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";

//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {
			jQuery.each(data || [], function(key, val) {

					$html = '';

					imgLogo="<?php echo $resourceImageUrl ?>";
					imgLogoError="<?php echo $resourceImageUrl ?>";
					
					imgMerchantLogo="<?php echo $merchantImageUrl ?>";
					imgMerchantLogoError="<?php echo $merchantImageUrl ?>";

				if (val.Resource.ImageData!= null && val.Resource.ImageData!= '') {
					var imgSliderData = '';
					var ImageData = val.Resource.ImageData.split(',');
					var start = 0;
                jQuery.each(ImageData,function(index){
                  // new system with preresized images
					  imgLogo = imgPath.replace("[img]", ImageData[index]);

					  // old system with resized images on the fly
					  imgLogoError = imgPathError.replace("[img]", ImageData[index]);
					  if(start == 0) {
					    imgSliderData = imgSliderData + '<div class="item active"><img src="'+imgLogo+'"></div>';					  
					  }
					  else {
					    imgSliderData = imgSliderData + '<div class="item"><img src="'+imgLogo+'"></div>';
				     }
				     start++;
                });
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId).carousel("pause").removeData();
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId+' .carousel-inner').html(imgSliderData);
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId).carousel('pause');
				}

					if (val.Merchant.LogoUrl != null && val.Merchant.LogoUrl  != '') {
						// new system with preresized images
						var ImageUrl = val.Merchant.LogoUrl.substr(val.Merchant.LogoUrl.lastIndexOf('/') + 1);
						imgMerchantLogo = imgPathmerchant.replace("[img]", val.Merchant.LogoUrl.replace(ImageUrl, imgPathmerchantresized + ImageUrl ) );
						
						// old system with resized images on the fly
						imgMerchantLogoError = imgPathmerchantError.replace("[img]", val.Merchant.LogoUrl);
	//					imgMerchantLogo = imgPathmerchant.replace("[img]", val.Merchant.LogoUrl );		
					}

					jQuery("#com_bookingforconnector-logo-grid-"+val.Resource.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#com_bookingforconnector-logo-grid-"+val.Resource.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");

					jQuery("#com_bookingforconnector-logo-list-"+val.Resource.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#com_bookingforconnector-logo-list-"+val.Resource.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");
					
					addressData = val.Resource.AddressData;
					if ((val.Resource.AddressData == '' || val.Resource.AddressData == null) &&  val.Merchant.AddressData != '') {
						
						var merchAddress = "";
						var $indirizzo = "";
						var $cap = "";
						var $comune = "";
						var $provincia = "";
						
						xmlDoc = jQuery.parseXML(val.Merchant.AddressData);
						if(xmlDoc!=null){
							$xml = jQuery(xmlDoc);
							$indirizzo = $xml.find("indirizzo:first").text();
							$cap = $xml.find("cap:first").text();
							$comune = $xml.find("comune:first").text();
							$provincia = $xml.find("provincia:first").text();
						}else{
							$indirizzo = val.Merchant.AddressData.Address;
							$cap = val.Merchant.AddressData.ZipCode;
							$comune = val.Merchant.AddressData.CityName;
							$provincia = val.Merchant.AddressData.RegionName;
						}
						addressData = strAddress.replace("[indirizzo]",$indirizzo);
						addressData = addressData.replace("[cap]",$cap);
						addressData = addressData.replace("[comune]",$comune);
						addressData = addressData.replace("[provincia]",$provincia);
//							xmlDoc = jQuery.parseXML(val.Merchant.AddressData);
//							$xml = jQuery(xmlDoc);
//							//$addressdata = $xml.find("addressdata")
//							$indirizzo = $xml.find("indirizzo:first");
//							addressData = strAddress.replace("[indirizzo]",$indirizzo.text())
//							$cap = $xml.find("cap:first");
//							addressData = addressData.replace("[cap]",$cap.text())
//							$comune = $xml.find("comune:first");
//							addressData = addressData.replace("[comune]",$comune.text())
//							$provincia = $xml.find("provincia:first");
//							addressData = addressData.replace("[provincia]",$provincia.text())
					}else{
							addressData = strAddressSimple + addressData;
					}

					jQuery(".address"+val.Resource.ResourceId).html(addressData);
					jQuery(".logo"+val.Resource.ResourceId).attr('src',imgLogo);
					jQuery(".descr"+val.Resource.ResourceId).removeClass("com_bookingforconnector_loading");

<?php if($showdata): ?>
				if (val.Resource.Description!= null && val.Resource.Description != ''){
					$html += nl2br(jQuery("<p>" + val.Resource.Description + "</p>").text());
				}
				jQuery("#descr"+val.Resource.ResourceId).data('jquery.shorten', false);
				jQuery("#descr"+val.Resource.ResourceId).html($html);
				
				jQuery("#descr"+val.Resource.ResourceId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.Resource.ResourceId).shorten(shortenOption);
<?php endif; ?>
jQuery( "#nameAnchor"+val.Resource.ResourceId ).html(val.Resource.Name);
					
					if (val.Merchant.RatingsContext!= null && (val.Merchant.RatingsContext == '2' || val.Merchant.RatingsContext == '3')){
						$htmlAvg = '';
						if (val.Resource.Avg != null && val.Resource.Avg != '' ) {
							$htmlAvg += strRatingValuation;
							$htmlAvg += '<div class="bfcvaluation average">' + number_format(val.Resource.Avg.Average, 1, '.', '') + '</div>';
							$htmlAvg += '<div class="bfcvaluationcount votes">' + strRatingBased.replace("%s", val.Resource.Avg.Count) + '</div>';
						}else{
							$htmlAvg += strRatingNoResult;
						}
						jQuery(".ratingAnchor"+val.Resource.ResourceId).html($htmlAvg);
					}else{
						jQuery(".ratingAnchor"+val.Resource.ResourceId).parent().hide();					
					}
					
					//price
					
					jQuery("#resourcestaytotal"+val.Resource.ResourceId).html("&nbsp; ");

				jQuery("#container"+val.Resource.ResourceId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.Resource.ResourceId ).attr("href");
					}
				});
		});	
	},'json');
}
jQuery(document).ready(function() {
	getAjaxInformations()
	jQuery('.mod_bookingformaps-static').click(function() {
     jQuery( "#mod_bookingformaps-popup" ).dialog({
       open: function( event, ui ) {
       openGoogleMapSearch();
    },
    height: 500,
    width: 800,
    });
  });
	jQuery('.com_bookingforconnector-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
	  jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
//	  jQuery('#bookingforsearchFilterForm').submit();
	  jQuery('#searchformfilter').submit();
	});
	jQuery(".offerslabel").click(
		function(){
				var discountId = jQuery(this).attr('rel'); 
				var hasRateplans = jQuery(this).attr('rel1'); 
				if (jQuery.inArray(discountId,offersLoaded)===-1)
				{
					getDiscountAjaxInformations(discountId,hasRateplans);
					offersLoaded.push(discountId);
				}
				jQuery(".divoffers"+discountId).slideToggle( "slow" );
			}
		);
});

	jQuery(window).resize(function() {
		var bpOpen = jQuery("#boxedpopupopen");
			var wWidth = jQuery(window).width();
			var dWidth = wWidth * 0.9;
			var wHeight = jQuery(window).height();
			var dHeight = wHeight * 0.9;
			if(dWidth>800){dWidth=870;}
			if(dHeight>600){dHeight=600;}
				bpOpen.dialog("option", "width", dWidth);
				bpOpen.dialog("option", "height", dHeight);
				bpOpen.dialog("option", "position", "center");
	});

//-->
</script>
