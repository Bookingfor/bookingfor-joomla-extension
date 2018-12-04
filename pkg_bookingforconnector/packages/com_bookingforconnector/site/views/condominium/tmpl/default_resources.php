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

$sitename = $this->sitename;
$language = $this->language;
$resources = $this->items;
$currResource = $this->item;
$merchant = $currResource->Merchant;
$listNameAnalytics = $this->listNameAnalytics;
$fromsearchparam = "&lna=".$listNameAnalytics;

$total = $this->pagination->total;

//$db   = JFactory::getDBO();
//$uri  = 'index.php?option=com_bookingforconnector&view=resource';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
////$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemId = intval($db->loadResult());
//
//$itemIdMerchant=0;
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdMerchant = intval($db->loadResult());
//$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
//
//if ($itemIdMerchant<>0)
//	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uri = COM_BOOKINGFORCONNECTOR_URIRESOURCE;
$uriMerchant  = COM_BOOKINGFORCONNECTOR_URIMERCHANTDETAILS.'&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);;
$routeMerchant  = JRoute::_($uriMerchant.$fromsearchparam);

$listsId = array();

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = (int)$merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
	$hasSuperior = ($merchant->Rating%10)>0;
} 

?>

<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-xs-9 ">
			<div class="bfi-search-title">
				<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_RESOURCES_TITLE_TOTAL'), $total) ?>
			</div>
		</div>	
	<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
		<!-- <div class="bfi-col-xs-3 ">
			<div class="bfi-search-view-maps ">
			<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_MAPVIEW') ?></span>
			</div>	
		</div>	 -->
	<?php } ?>
	</div>	
<?php if ($total > 0){ ?>

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

<div id="bfi-list" class="bfi-row bfi-list">
	<?php foreach ($resources as $currKey=>$resource){?>
	<?php 
		$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$merchantName = $resource->MerchantName;

		$resourceLat = $resource->XPos;
		$resourceLon = $resource->YPos;
		
		$resource->MinPaxes = $resource->MinCapacityPaxes;
		$resource->MaxPaxes= $resource->MaxCapacityPaxes;
		
		$showResourceMap = (($resourceLat != null) && ($resourceLon !=null));

		$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
//		if ($itemId<>0)
//			$currUriresource.='&Itemid='.$itemId;
		$resourceRoute = JRoute::_($currUriresource.$fromsearchparam);
	
		$rating = 0;
		$ratingMrc = 0;
//		$rating = $resource->Rating;
//		if ($rating>9 )
//		{
//			$rating = $rating/10;
//		}
//		$ratingMrc = $resource->MrcRating;
//		if ($ratingMrc>9 )
//		{
//			$ratingMrc = $ratingMrc/10;
//		}
		if(!empty($resource->DefaultImg)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$resource->DefaultImg, 'medium');
		}
		$resourceNameTrack =  BFCHelper::string_sanitize($resourceName);
		$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
		$merchantCategoryNameTrack = "";// BFCHelper::string_sanitize($resource->MrcCategoryName);
	?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;' target="_blank" class="eectrack" data-type="Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->ResourceId?>" target="_blank" class="eectrack" data-type="Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $resource->Name ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
							<?php if($isportal){ ?>
							- <a href="<?php echo $routeMerchant?>" class="bfi-subitem-title" class="eectrack" data-type="Merchant" data-id="<?php echo $resource->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $merchantName; ?></a>
							<?php } ?>
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $ratingMrc; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
						</div>
						<div class="bfi-item-address">
							<?php if ($showResourceMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $resource->ResourceId?>)"><span id="address<?php echo $resource->ResourceId?>"></span></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $resource->ResourceId; ?>"></div>
						<div class="bfi-description"><?php echo $resourceDescription ?></div>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-8">
						<?php if ($resource->MaxPaxes>0){?>
							<div class="bfi-icon-paxes">
								<i class="fa fa-user"></i> 
								<?php if ($resource->MaxPaxes==2){?>
								<i class="fa fa-user"></i> 
								<?php }?>
								<?php if ($resource->MaxPaxes>2){?>
									<?php echo ($resource->MinPaxes != $resource->MaxPaxes)? $resource->MinPaxes . "-" : "" ?><?php echo  $resource->MaxPaxes ?>
								<?php }?>
							</div>
						<?php } ?>
					
					</div>
					<div class="bfi-col-sm-4 bfi-text-right">
						<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack" target="_blank" data-type="Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON') ?></a>
					</div>
				</div>
				<!-- end resource details -->

				<div class="bfi-clearfix"></div>
				<!-- end price details -->
			</div>
		</div>
	</div>
	<?php 
	$listsId[]= $resource->ResourceId;
	?>
<?php } ?>
</div>

<?php if ($this->pagination->get('pages.total') > 1) { ?>
	<div class="pagination bfi-pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php } ?>

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

var listToCheck = "<?php echo implode(",", $listsId) ?>";

var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '150'
};

var mg = [];

var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var queryMG = "task=getResourceGroups";
		jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(JSON.parse(data) || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name,bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);
							/*--------getName----*/
							mg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
						} else {
							if (val.IconSrc != null && val.IconSrc != '') {
								mg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
							}
						}
					});	
				}
				getlist();
		},'json');
	}
}

function getlist(){
	var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>&task=GetResourcesByIds";
	if(listToCheck!='')
	

	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
	
				var $indirizzo = "";
				var $cap = "";
				var $comune = "";
				var $provincia = "";
				
				$indirizzo = val.Resource.AddressData;
				$cap = val.Resource.ZipCode;
				$comune = val.Resource.CityName;
				$provincia = val.Resource.RegionName;

				addressData = strAddress.replace("[indirizzo]",$indirizzo);
				addressData = addressData.replace("[cap]",$cap);
				addressData = addressData.replace("[comune]",$comune);
				addressData = addressData.replace("[provincia]",$provincia);
				jQuery("#address"+val.Resource.ResourceId).html(addressData);

				if (val.Resource.TagsIdList!= null && val.Resource.TagsIdList != '')
				{
					var mglist = val.Resource.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.Resource.ResourceId).html($htmlmg);
				}			

				jQuery(".container"+val.Resource.ResourceId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( ".nameAnchor"+val.Resource.ResourceId ).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		},'json');
}

	
jQuery(document).ready(function() {
	getAjaxInformations();

	jQuery('.bfi-maps-static,.bfi-search-view-maps').click(function() {
		jQuery( "#bfi-maps-popup" ).dialog({
			open: function( event, ui ) {
				openGoogleMapSearch();
			},
			height: 500,
			width: 800,
			dialogClass: 'bfi-dialog bfi-dialog-map'
		});
	});

	jQuery(".bfi-description").shorten(shortenOption);

});


//-->
</script>

<?php } else { ?>
<div class="bfi-noresults">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_NORESULT')?>
</div>
<?php } ?>
	<div class="bfi-clearboth"></div>
</div>
