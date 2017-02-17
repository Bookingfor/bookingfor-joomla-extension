<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$config = $this->config;
$isportal = $config->get('isportal', 1);
$showdata = $config->get('showdata', 1);


$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$merchantCategoryId = 0;
$pars = BFCHelper::getSearchParamsSession();
$merchants = "";
$checkin = new JDate('now'); 
$checkout = new JDate('now'); 

if (!empty($pars)){
	$merchantCategoryId = $pars['merchantCategoryId']?: 0;
	$merchants = BFCHelper::getMerchantByCategoryId($merchantCategoryId);
	if(!empty($merchants)){
		$merchants = array_slice($merchants, 0, 50);     
	}
	if (!empty($pars['checkin'])){
		$checkin = new JDate($pars['checkin']->format('Y-m-d')); 
	}
	if (!empty($pars['checkout'])){
		$checkout = new JDate($pars['checkout']->format('Y-m-d')); 
	}


}

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$language = $this->language;

$showRating = true;

$currentgroup = 0;
//$listOrder	= $this->escape($this->state->get('list.ordering'));
//$listDirn	= $this->escape($this->state->get('list.direction'));


$listsId = array();
//-------------------pagina per i l redirect di tutte le risorsein vendita

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

$img = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$imgError = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantLogo =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";
$merchantLogoError =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'medium');

$hidesort = "";

?>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
//-->
</script>
<div class="com_bookingforconnector-items-container">
<?php if (count($merchants)>0) : ?>
<div class="com_bookingforconnector-search-menu">
	<form  action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="filterForm" id="filterForm">
		<fieldset class="filters">
			<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
			<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
			<input type="hidden" name="limitstart" value="0" />
			<input type="hidden" name="startswith" id="startswith" value="<?php //echo $startswith ?>" />
		</fieldset>
	</form>
	<div class="com_bookingforconnector-results-sort">
		<span class="com_bookingforconnector-sort-item"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
		<span class="com_bookingforconnector-sort-item " rel="Name|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_NAMEASC'); ?></span>
		<span class="com_bookingforconnector-sort-item " rel="Name|desc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_NAMEDESC'); ?></span>
	</div>
	<div class="com_bookingforconnector-view-changer">
		<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
		<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
	</div>
</div>
<div class="clearfix"></div>
	<div class="com_bookingforconnector-search-merchants com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
	<?php $listResourceIds = array(); ?>  
	<?php foreach ($merchants as $mrcKey => $merchant): ?>
		<?php 
			$rating = $merchant->Rating;

			$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

			if ($itemIdMerchant<>0)
				$currUriMerchant.='&Itemid='.$itemIdMerchant;
			
			$routeMerchant = JRoute::_($currUriMerchant);
			$routeRating = JRoute::_($currUriMerchant.'&layout=ratings');				
			$routeInfoRequest = JRoute::_($currUriMerchant.'&layout=contactspopup&tmpl=component');
			
			$counter = 0;
			$merchantLat = $merchant->XPos;
			$merchantLon = $merchant->YPos;
			$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
			
//			$images = array($merchantImageUrl);
			$currMerchantImageUrl = $merchantImageUrl;
			$currmerchantLogoUrl = $merchantLogoUrl;
			if(!empty($merchant->DefaultImg)){
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->DefaultImg, 'medium');
			}
			if(!empty($merchant->LogoUrl)){
				$currmerchantLogoUrl = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logomedium');
			}
			$merchantSiteUrl = '';
			if ($merchant->SiteUrl != '') {
				$merchantSiteUrl =$merchant->SiteUrl;
				if (strpos('http://', $merchantSiteUrl) == false) {
					$merchantSiteUrl = 'http://' . $merchantSiteUrl;
				}
				$merchantSiteUrlstripped = str_replace('http://', "", $merchantSiteUrl);
				if (strpos($merchantSiteUrlstripped,'?') !== false) {
					$tmpurl = explode("?",$merchantSiteUrlstripped);
					$merchantSiteUrlstripped = $tmpurl[0];
				}
			}

		?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" style="padding: 10px !important;">
				<div class="com_bookingforconnector-search-merchant com_bookingforconnector-item  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
					<div class="mrcgroup" id="bfcmerchantgroup<?php echo $merchant->MerchantId; ?>"><span class="bfcmerchantgroup"></span></div>
					<div class="com_bookingforconnector-item-details  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
							<div id="com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" class="carousel" data-ride="carousel" data-interval="false">
								<div class="carousel-inner" role="listbox">
									<div class="item active"><img src="<?php echo $currMerchantImageUrl; ?>"></div>
								</div>
								<?php if($isportal): ?>
									<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-grid eectrack" href="<?php echo $routeMerchant ?>"  data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" src="<?php echo $currmerchantLogoUrl; ?>" id="com_bookingforconnector-logo-grid-<?php echo $merchant->MerchantId?>" /></div></a>
								<?php endif; ?>
								<a class="left carousel-control" href="#com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" role="button" data-slide="prev">
									<i class="fa fa-chevron-circle-left"></i>
								</a>
								<a class="right carousel-control" href="#com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" role="button" data-slide="next">
									<i class="fa fa-chevron-circle-right"></i>
								</a>
							</div>
						</div>
						<div class="com_bookingforconnector-search-merchant-details com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div class="com_bookingforconnector-search-merchant-name com_bookingforconnector-item-primary-name">
								<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor namelist eectrack" href="<?php echo $routeMerchant ?>" data-type="Merchant" id="nameAnchor<?php echo $merchant->MerchantId?>" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"><?php echo  $merchant->Name ?></a> 
								<span class="com_bookingforconnector-search-merchant-rating com_bookingforconnector-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
							<?php if ($isportal):?>
								<div class="ratinggrid">
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-search-merchant-review-value com_bookingforconnector-item-review-value" href="<?php echo $routeRating ?>" id="ratingAnchorGridvalue<?php echo $merchant->MerchantId?>" ></a>
										<a class="com_bookingforconnector-search-merchant-review-count com_bookingforconnector-item-review-count" href="<?php echo $routeRating ?>" id="ratingAnchorGridcount<?php echo $merchant->MerchantId?>" ></a>
									</div>
								</div>
							<?php endif; ?>
							</div>
							<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor namegrid eectrack" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" data-id="<?php echo $merchant->MerchantId?>" data-type="Merchant" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"><?php echo  $merchant->Name ?></a> 
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-address">
								<?php if ($showMerchantMap):?>
								<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><span id="address<?php echo $merchant->MerchantId?>"></span></a>
								<?php endif; ?>
							</div>
							<?php if($showdata): ?>
								<div class="com_bookingforconnector-merchant-description" id="descr<?php echo $merchant->MerchantId?>"></div>
							<?php endif; ?>
							<div class="ratinglist">
								<?php if ($isportal):?>
								<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
									<a class="com_bookingforconnector-search-merchant-review-value com_bookingforconnector-item-review-value" href="<?php echo $routeRating ?>" id="ratingAnchorvalue<?php echo $merchant->MerchantId?>" ></a>
									<a class="com_bookingforconnector-search-merchant-review-count com_bookingforconnector-item-review-count" href="<?php echo $routeRating ?>" id="ratingAnchorcount<?php echo $merchant->MerchantId?>" ></a>
								</div>
								<?php endif; ?>&nbsp;
							</div>
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-phone-inforequest"> 
								<span class="com_bookingforconnector_phone">
								<a  href="javascript:void(0);" 
									onclick="getData(urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes( $merchant->Name) ?>','PhoneView' )"  id="phone<?php echo $merchant->MerchantId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a>
								</span> - 					
								<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
								<?php if ($showdata && $merchantSiteUrl != ''):?> 
								- <?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=redirect&view=merchantdetails&tmpl=component&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name),true,-1), JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_SITEGO') ,'target="_blank"') ?>
								<?php endif; ?>
							</div>
						</div>

						<?php if($isportal): ?>
							<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
								<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list eectrack" href="<?php echo $routeMerchant ?>" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-type="Merchant" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"><img class="com_bookingforconnector-logo" src="<?php echo $currmerchantLogoUrl; ?>" id="com_bookingforconnector-logo-list-<?php echo $merchant->MerchantId?>" /></a>
							</div>
						<?php endif; ?>
					</div>
					<div class="clearfix"></div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10 com_bookingforconnector-item-secondary-section-1 secondarysectionitem" style="padding-left:20px!important;">
							&nbsp;
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 com_bookingforconnector-item-secondary-section-3 secondarysectionitem">
								<a  href="<?php echo $routeMerchant?>" style="color: #fff;" class=" com_bookingforconnector-item-secondary-more eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON') ?></a>
						</div>
					</div>

			</div><!-- row -->
			</div><!-- first div -->
		  <?php $listsId[]= $merchant->MerchantId; ?>
	  <?php endforeach; ?>
	</div>
<?php endif ?>

<?php if ($this->pagination->get('pages.total') > 1) : ?>
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>

</div>
<div class="clearboth"></div>
<br />
<script type="text/javascript">
<!--
if(typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}

jQuery(document).ready(function() {
	jQuery('.com_bookingforconnector-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#filterForm .filterOrder').val(vals[0]);
	  jQuery('#filterForm .filterOrderDirection').val(vals[1]);
	  jQuery('#filterForm').submit();
	});
});
jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery('.com_bookingforconnector-view-changer-list').addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2');
	localStorage.setItem('displaymerchants', 'list');
});

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery('.com_bookingforconnector-view-changer-grid').addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	localStorage.setItem('displaymerchants', 'grid');
});

if (localStorage.getItem('displaymerchants')) {
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

	function setOrdering(option) {
		var form = option.form;
		var order_dir = option.value;
		var vals = order_dir.split("|"); 
		form.filter_order.value = vals[0];
		form.filter_order_Dir.value = vals[1];
		form.submit();
	}
	function setOrderingAlphabet(startswith) {
		jQuery("#filterForm #startswith").val(startswith)
		jQuery("#filterForm").submit();
	}
	
//-->
</script>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";
var listToCheck = "<?php echo implode(",", $listsId) ?>";

var imgPath = "<?php echo $merchantImagePath ?>";
var imgPathError = "<?php echo $merchantImagePathError ?>";

var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'tag24') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'tag24') ?>";
var cultureCodeMG = '<?php echo $language ?>';
var defaultcultureCodeMG = '<?php echo BFCHelper::$defaultFallbackCode ?>';

var strRatingNoResult = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_NO_RESULT')?>";
var strRatingBased = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_BASED')?>";
var strRatingValuation = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_VALUATION')?>";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};

var mg = [];

function getAjaxInformations(){
	var queryMG = "task=getMerchantGroups";

//	jQuery.getJSON(urlCheck + "?" + queryMG, function(data) {
	jQuery.post(urlCheck, queryMG, function(data) {
			jQuery.each(data || [], function(key, val) {
					if (val.ImageUrl!= null && val.ImageUrl!= '') {
						var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
						var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
						/*--------getName----*/
						var $name = getXmlLanguage(val.Name,cultureCodeMG,defaultcultureCodeMG);
						/*--------getName----*/
						mg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
					} else {
						if (val.IconSrc != null && val.IconSrc != '') {
							mg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
						}
					}
			});	

			getlist();
	},'json');
}

function getlist(){
	var query = "merchantsId=" + listToCheck + "&language=<?php echo $language ?>&task=GetMerchantsByIds";
	if(listToCheck!='') {
		allObjects = [];
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
			jQuery.each(data || [], function(key, val) {
				$html = '';
				allObjects.push({
					"id": val.MerchantId + " - Merchant",
					"name": val.Name,
					"category": val.MainCategoryName,
					"brand": val.Name,
					"position": key
				});
				if (val.ImageData!= null && val.ImageData!= '') {
					var imgSliderData = '';
					var ImageData = val.ImageData.split(',');
					var start = 0;
                jQuery.each(ImageData,function(index){
                  // new system with preresized images
					  imgLogo = imgPath.replace("[img]", jQuery.trim(ImageData[index]));

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
                jQuery('#com_bookingforconnector-search-merchant-carousel'+val.MerchantId).carousel("pause").removeData();
                jQuery('#com_bookingforconnector-search-merchant-carousel'+val.MerchantId+' .carousel-inner').html(imgSliderData);
                jQuery('#com_bookingforconnector-search-merchant-carousel'+val.MerchantId).carousel('pause');
				}

				if (val.AddressData != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					xmlDoc = jQuery.parseXML(val.AddressData);
					if(xmlDoc!=null){
						$xml = jQuery(xmlDoc);
						$indirizzo = $xml.find("indirizzo:first").text();
						$cap = $xml.find("cap:first").text();
						$comune = $xml.find("comune:first").text();
						$provincia = $xml.find("provincia:first").text();
					}else{
						$indirizzo = val.AddressData.Address;
						$cap = val.AddressData.ZipCode;
						$comune = val.AddressData.CityName;
						$provincia = val.AddressData.RegionName;
					}
					merchAddress = strAddress.replace("[indirizzo]",$indirizzo);
					merchAddress = merchAddress.replace("[cap]",$cap);
					merchAddress = merchAddress.replace("[comune]",$comune);
					merchAddress = merchAddress.replace("[provincia]",$provincia);
					jQuery("#address"+val.MerchantId).html('<i class="fa fa-map-marker" aria-hidden="true"></i> ' + merchAddress);
				}

				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '<span class="bfcmerchantgroup">';
					jQuery.each(mglist, function(key, mgid) {
						$htmlmg += mg[mgid];
					});
					$htmlmg += '</span>';
					jQuery("#bfcmerchantgroup"+val.MerchantId).html($htmlmg);
				}

<?php if($showdata): ?>
				if (val.Description!= null && val.Description != ''){
					$html += nl2br(jQuery("<p>" + val.Description + "</p>").text());
				}

				jQuery("#descr"+val.MerchantId).append($html);
				
				jQuery("#descr"+val.MerchantId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.MerchantId).shorten(shortenOption);
<?php endif; ?>
				jQuery("#container"+val.MerchantId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.MerchantId ).attr("href");
					}
				});

				if (val.RatingsContext!= null && (val.RatingsContext == '1' || val.RatingsContext == '2' || val.RatingsContext == '3')){
					if (val.Avg != null && val.Avg != '' ) {
						jQuery("#ratingAnchorvalue"+val.MerchantId).html(number_format(val.Avg.Average, 1, '.', ''));
						jQuery("#ratingAnchorcount"+val.MerchantId).html(strRatingBased.replace("%s", val.Avg.Count));
					}
				}else{
					jQuery("#ratingAnchorvalue"+val.MerchantId).hide();
					jQuery("#ratingAnchorcount"+val.MerchantId).hide();		
				}
			});
jQuery('[data-toggle="tooltip"]').tooltip(); 	
<?php if($this->analyticsEnabled): ?>
			ga('send','event','Bookingfor - Search','NoResults','{<?php echo $checkin->format('d/m/Y'); ?> - <?php echo $checkout->format('d/m/Y'); ?>}');
			callAnalyticsEEc("addImpression", allObjects, "list", "No Results Merchant List");
<?php endif; ?>
		},'json');

	}
}

jQuery(document).ready(function() {
	getAjaxInformations()

	jQuery('a.boxedpopup').on('click', function (e) {
		var width = jQuery(window).width()*0.9;
		var height = jQuery(window).height()*0.9;
			if(width>800){width=870;}
			if(height>600){height=600;}

		e.preventDefault();
		var page = jQuery(this).attr("href")
		var pagetitle = jQuery(this).attr("title")
		var $dialog = jQuery('<div id="boxedpopupopen"></div>')
			.html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
			.dialog({
				autoOpen: false,
				modal: true,
				height:height,
				width: width,
				fluid: true, //new option
				title: pagetitle
			});
		$dialog.dialog('open');
	});

});
	function showResponse(responseText, statusText, xhr, $form)  { 
		jQuery('#bfcmerchantlist').unblock();
		if(typeof getAjaxInformations === 'function' ) {
			getAjaxInformations();
		}
			// reset map
			mapSearch = undefined;
			oms =  undefined;

			// Attach modal behavior to document
			if (typeof(SqueezeBox) !== 'undefined'){
				SqueezeBox.initialize({});
				SqueezeBox.assign($$('#bfcmerchantlist  a.boxed'), { //change the divid (#contentarea) as to the div that you use for refreshing the content
					parse: 'rel'
				});
			}

			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
	}
	function showError(responseText, statusText, xhr, $form)  { 
		jQuery('#bfcmerchantlist').html('<?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_NORESULT') ?>')
		jQuery('#bfcmerchantlist').unblock();
	}

//-->
</script>
