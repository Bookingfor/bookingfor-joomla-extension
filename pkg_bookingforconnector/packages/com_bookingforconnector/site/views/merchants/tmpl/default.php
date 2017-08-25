<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$language = $this->language;
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

//$startswith = $this->startswith;

$filter_order	= $this->escape($this->state->get('list.ordering'));
$filter_order_Dir	= $this->escape($this->state->get('list.direction'));

$currSorting= $filter_order . "|" . $filter_order_Dir;

$total = $this->pagination->total;

$listsId = array();
//-------------------pagina per i l redirect di tutte le risorsein vendita

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'medium');

$merchants = $this->items;

$url=JFactory::getURI()->toString();
$formAction=$url;

?>
<?php if (count($merchants)>0) { ?>
<div class="bfi-content">
<div class="bfi-search-menu">
	<form action="<?php echo $formAction; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
			<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $filter_order ?>" />
			<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $filter_order_Dir ?>" />
			<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
			<input type="hidden" name="startswith" id="startswith" value="<?php //echo $startswith ?>" />
			<input type="hidden" name="limitstart" value="0" />
	</form>
	<div class="bfi-results-sort">
		<span class="bfi-sort-item"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
		<span class="bfi-sort-item <?php echo $currSorting=="Name|asc" ? "bfi-sort-item-active": "" ; ?>" rel="Name|asc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_NAMEASC'); ?></span>
		<span class="bfi-sort-item <?php echo $currSorting=="Name|desc" ? "bfi-sort-item-active": "" ; ?>" rel="Name|desc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_NAMEDESC'); ?></span>
	</div>
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
	<?php $listResourceIds = array(); ?>  
	<?php foreach ($merchants as $merchant){ ?>
		<?php 
			$rating = $merchant->Rating;
			if ($rating>9 )
			{
				$rating = $rating/10;
			} 
			$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

			if ($itemIdMerchant<>0)
				$currUriMerchant.='&Itemid='.$itemIdMerchant;
			
			$routeMerchant = JRoute::_($currUriMerchant);
			$routeRating = JRoute::_($currUriMerchant.'&layout=ratings');				
			
			$counter = 0;
			$merchantLat = $merchant->XPos;
			$merchantLon = $merchant->YPos;
			$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
			$showMerchantMap = false;
			$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
			
			if(!empty($merchant->DefaultImg)){
				$merchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->DefaultImg, 'medium');
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
			$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
			$merchantDescription = BFCHelper::getLanguage($merchant->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

		?>


	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $routeMerchant ?>" style='background: url("<?php echo $merchantImageUrl; ?>") center 25% / cover;'><img src="<?php echo $merchantImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-10">
						<div class="bfi-item-title">
							<a href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" target="_blank"><?php echo  $merchantName ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
						</div>
						<div class="bfi-item-address">
							<?php if ($showMerchantMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><?php }?><span id="address<?php echo $merchant->MerchantId?>"></span><?php if ($showMerchantMap){?></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $merchant->MerchantId; ?>"></div>
						<div class="bfi-description"><?php echo $merchantDescription ?></div>
					</div>
					<div class="bfi-col-sm-2 bfi-text-right">
						<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
								<div class="bfi-avg">
								<?php if ($merchant->MrcAVGCount>0){
									$totalInt = BFCHelper::convertTotal(number_format((float)$merchant->MrcAVG, 1, '.', ''));

									?>
									<a class="bfi-avg-value" href="<?php echo $routeRating ?>" ><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt] . " " . number_format((float)$merchant->MrcAVG, 1, '.', '') ?></a><br />
									<a class="bfi-avg-count" href="<?php echo $routeRating ?>" ><?php echo sprintf(__('%s reviews' , 'bfi'),$merchant->MrcAVGCount) ?></a>
								<?php } ?>
								</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
					<div class=" bfi-text-right">
							<a href="<?php echo $routeMerchant ?>" class="bfi-btn"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON') ?></a>
					</div>
				<div class="bfi-clearfix"></div>
			</div>
		</div>
	</div>
		<?php $listsId[]= $merchant->MerchantId; ?>
	<?php } ?>
</div>
</div>

<?php if ($this->pagination->get('pages.total') > 1) { ?>
	<div class="text-center">
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>
<?php } ?>

<script type="text/javascript">
<!--
if(typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}

jQuery(document).ready(function() {
	jQuery('.bfi-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
	  jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
	  jQuery('#bookingforsearchFilterForm').submit();
	});
});

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
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'tag24') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'tag24') ?>";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};

var mg = [];

var loaded=false;

function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var queryMG = "task=getMerchantGroups";
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
	var query = "merchantsId=" + listToCheck + "&language=<?php echo $language ?>&task=GetMerchantsByIds";
	if(listToCheck!='')
	
	var imgPath = "<?php echo $merchantImagePath ?>";
	var imgPathError = "<?php echo $merchantImagePathError ?>";

	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
				
				if (val.AddressData != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					$indirizzo = val.AddressData.Address;
					$cap = val.AddressData.ZipCode;
					$comune = val.AddressData.CityName;
					$provincia = val.AddressData.RegionName;

					merchAddress = strAddress.replace("[indirizzo]",$indirizzo);
					merchAddress = merchAddress.replace("[cap]",$cap);
					merchAddress = merchAddress.replace("[comune]",$comune);
					merchAddress = merchAddress.replace("[provincia]",$provincia);
					jQuery("#address"+val.MerchantId).append(merchAddress);
				}
				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.MerchantId).html($htmlmg);
				}
				jQuery("#container"+val.MerchantId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.MerchantId ).attr("href");
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
<?php if(!isset($nopopupmap)):  ?>
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
<?php endif; ?>
	jQuery(".bfi-description").shorten(shortenOption);

});

//-->
</script>
<?php } ?>
