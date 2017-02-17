<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.boxed'); 
$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$language = $this->language;

$results = $this->items;
$language = $this->language;
$searchid =  $this->params['searchid'];
$locationzonesdefault =  $this->params['locationzones'];

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$counterdiv=1;
$ordselect = array(
	JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL') ),
	JHTML::_('select.option', 'PriceMin|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC') ),
	JHTML::_('select.option', 'PriceMin|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYDESC')),
	JHTML::_('select.option', 'Created|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDASC') ),
	JHTML::_('select.option', 'Created|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDESC')),
//	JHTML::_('select.option', 'rooms|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_ROOMSASC')),
//	JHTML::_('select.option', 'rooms|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_ROOMSDESC')),
//	JHTML::_('select.option', 'distancefromsea|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEASC')),
//	JHTML::_('select.option', 'distancefromsea|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEDESC'))
);

$onchange = 'onchange="setOrdering(this);"';

if(!$this->params['show_latest']){
	$locationZones = BFCHelper::getLocationZonesBySearch();
}else{
	$ordselect = array(
		JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL') ),
		JHTML::_('select.option', 'MinPrice|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC') ),
		JHTML::_('select.option', 'MinPrice|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYDESC')),
		JHTML::_('select.option', 'Created|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDASC') ),
		JHTML::_('select.option', 'Created|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDESC')),
	);
	$locationZones = BFCHelper::getLastLocationZoneOnsell();
}

$listlocationZones = array();
$listlocationZones[] = JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ALL'));
if(!empty($locationZones)){
	foreach ($locationZones as $lz) {
		$listlocationZones[] = JHTML::_('select.option', $lz);
	}
}

$listsId = array();
//$resourceImageUrl = Juri::base() . "images/default.jpg";
//$merchantImageUrl = Juri::base() . "images/DefaultLogoList.jpg";
//$resourceLogoPath = BFCHelper::getImageUrl('onsellunits',"[img]", 'onsellunit_list_default');
//$merchantLogoPath = BFCHelper::getImageUrl('merchant',"[img]", 'resource_list_merchant_logo');

$resourceImageUrl = Juri::root() . "images/default.png";
$merchantImageUrl = Juri::root() . "images/DefaultLogoList.jpg";

$resourceLogoPath = BFCHelper::getImageUrlResized('onsellunits',"[img]", 'onsellunit_list_default');
$resourceLogoPathError = BFCHelper::getImageUrl('onsellunits',"[img]", 'onsellunit_list_default');

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'resource_list_merchant_logo');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'resource_list_merchant_logo');



//-------------------pagina per i l redirect di tutte le risorsein vendita

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant

//-------------------pagina per il redirect di tutte le risorse in vendita favorite

$uriFav = 'index.php?option=com_bookingforconnector&view=onsellunits&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdFav = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);

$showmap = true;
$total = $this->pagination->total;

if($total<1){
	$showmap = false;
}

?>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
//-->
</script>
<div class="clearboth"></div>
<h1><?php echo $activeMenu->title?></h1>

<?php if (!empty($results)):?>

		<div class="resourcetabmenu">
			<a class="resources" rel="resources"><i class="icon-list-ul"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TAB_LIST') ?></a><?php if (($showmap)) :?><a id="maptab" class="mappa" rel="mappa"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TAB_MAP') ?></a><?php endif?>
		</div>
		<div class="resourcetabcontainer">
			<div id="resources" class="tabcontent">

<!--  -->
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="bfSearchFilterOnsell" id="bfSearchFilterOnsell">
	<!-- <legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend> -->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> filters">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LOCATIONZONES')?>:<br /> 
			<?php echo JHTML::_('select.genericlist', $listlocationZones, 'locationzones', $onchange, 'value', 'text', $locationzonesdefault );?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>: <br />
			<?php echo JHTML::_('select.genericlist', $ordselect, 'orderselect', $onchange, 'value', 'text', ($listOrder.'|'.$listDirn) );?>
		</div>
	</div>

	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
	<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
	<input type="hidden" name="limitstart" value="0" />

	<script type="text/javascript">
	<!--
	function setOrdering(option) {
		var orderselect = jQuery("#bfSearchFilterOnsell select[name='orderselect']").val();
		var vals = orderselect.split("|"); 
		jQuery("#bfSearchFilterOnsell input[name='filter_order']").val(vals[0]);
		jQuery("#bfSearchFilterOnsell input[name='filter_order_Dir']").val(vals[1]);
		jQuery("#bfSearchFilterOnsell").submit();
	}
	//-->
	</script>
</form>
<div class="com_bookingforconnector-resourcelist">
<?php foreach ($results as $result):?>
	<?php 
	$resource = $result;
	if(!$this->params['show_latest']){
		$resource->ResourceId = $result->OnSellUnitId;
	}
	$resource->Price = $result->MinPrice;
//	$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
	$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$addressData = $resource->AddressData;
	$resourceLat = $resource->XGooglePos;
	$resourceLon = $resource->YGooglePos;
	$isMapVisible = $resource->IsMapVisible;
	$isMapMarkerVisible = $resource->IsMapMarkerVisible;
	$showResourceMap = false;//(($resourceLat != null) && ($resourceLon !=null) && $isMapVisible && $isMapMarkerVisible);

	if ($itemId<>0){
		$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
	} else {
		$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
	}
	
	$routeInfoRequest = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&layout=inforequestpopup&tmpl=component&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
	$routeRapidView = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&layout=rapidview&tmpl=component&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
		
	?>
	<div class="com_bookingforconnector_search-resource">
		<div class="com_bookingforconnector_merchantdetails-resource" id="container<?php echo $resource->ResourceId?>"> 
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9 borderright minhlist">
						<div class="com_bookingforconnector_merchantdetails-resource-features">
						<!-- img -->
						<a  class="lensimg hidden boxedpopup" id="lensimg<?php echo $resource->ResourceId?>"  href="<?php echo $routeRapidView?>" title="<?php echo  $resourceName ?>">&nbsp;</a>
						<div  class="borderimg" id="borderimg<?php echo $resource->ResourceId?>">&nbsp;</div>
						<div  class="ribbonnew hidden" id="ribbonnew<?php echo $resource->ResourceId?>">&nbsp;</div>
						<a class="com_bookingforconnector_resource-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector_resource-img" src="<?php echo $resourceImageUrl?>"  id="logo<?php echo $resource->ResourceId?>" /></a>
						<!-- end img -->
						
						<!-- sup title -->
							<span class="showcaseresource hidden" id="showcaseresource<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_SHOWCASERESOURCE') ?></span><span class="topresource hidden" id="topresource<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_TOPRESOURCE') ?></span>
							<span class="newbuildingresource hidden" id="newbuildingresource<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_NEWBUILDINGRESOURCE') ?></span>
						<!-- end sup title -->

						<!-- title -->
						<h4 class="com_bookingforconnector_merchantdetails-resource-name">
							<a class="com_bookingforconnector_resource-resource-nameAnchor" href="<?php echo $route ?>" id="nameAnchor<?php echo $resource->ResourceId?>"><?php echo  $resourceName ?></a>
						</h4>
						<!-- end title -->

						<!-- address -->
						<div class="com_bookingforconnector_merchantdetails-resource-address">
							<span id="address<?php echo $resource->ResourceId?>"></span>
							<?php if ($showResourceMap):?>
							- <a href="javascript:void(0);" onclick="showMarker(<?php echo $resource->ResourceId?>)"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_SHOWONMAP') ?></a>
							<?php endif; ?>
						</div>
						<!-- end address -->

						<!-- description -->
						<p class="com_bookingforconnector_merchantdetails-resource-desc com_bookingforconnector_loading" id="descr<?php echo $resource->ResourceId?>"></p>
						<!-- end description -->
				
						</div>
					</div>

					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 optioncontainer">
						<a class="com_bookingforconnector_merchantdetails-name" href="<?php echo $routeMerchant?>" id="merchantname<?php echo $resource->ResourceId?>"><img class="com_bookingforconnector_resource_logomerchant" src="<?php echo $merchantImageUrl?>"  id="logomerchant<?php echo $resource->ResourceId?>" /></a>
						<div class="optionresource">
							<div>
								<span class="com_bookingforconnector_phone"><a  href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $resource->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this)"  id="phone<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span>
							</div>
							<div>
								<a class="boxed com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
							</div>
							<?php if(COM_BOOKINGFORCONNECTOR_ENABLEFAVORITES):?>
								<div>
									<?php if(BFCHelper::IsInFavourites($resource->ResourceId)):?>
										<a class="com_bookingforconnector_fav com_bookingforconnector_favadded " href="<?php echo $routeFav ?>" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADDED')?></a>
									<?php else:?>
										<a class="com_bookingforconnector_fav " href="javascript:addCustomURlfromfavTranfert('#favAnchor<?php echo $resource->ResourceId?>',<?php echo $resource->ResourceId?>,'<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADDED')?>')" id="favAnchor<?php echo $resource->ResourceId?>" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADD')?></a>
									<?php endif ?>
								</div>
							<?php endif ?>
						</div>
					</div>

			</div>			
			<div class="clearboth"></div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_search-merchant-resource nominheight noborder">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9" id="divfeature<?php echo $resource->ResourceId?>">
							<div class="padding1020 minheight34 borderright font16">
								<strong>
									<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
												&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
									<?php else: ?>
											<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
									<?php endif; ?>
								</strong>
							</div>
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
							<a class="btn btn-info pull-right" href="<?php echo $route ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LINK')?></a>
						</div>
					</div>
			</div>
		</div>
	</div>
	<?php 
	$listsId[]= $resource->ResourceId;
	$counterdiv +=1;
	?>
<?php endforeach; ?>
</div>
				
<!--  -->				
				<?php if ($this->pagination->get('pages.total') > 1) : ?>
					<div class="pagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
				<?php endif; ?>
			</div>
			<div id="mappa" class="tabcontent">
				<?php
				if ($showmap) {
					echo  $this->loadTemplate('googlemap');
					echo  $this->loadTemplate('googlemap_resources');
				}
				?>
			</div>
		</div>
		<div class="clearboth"></div>



<script type="text/javascript">
<!--
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var imgPathmerchant = "<?php echo $merchantLogoPath ?>";
var imgPathmerchantError = "<?php echo $merchantLogoPathError ?>";

var imgPath = "<?php echo $resourceLogoPath ?>";
var imgPathError = "<?php echo $resourceLogoPathError ?>";

var strAddressSimple = "<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>:</strong> ";
var strAddress = "<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>:</strong> [indirizzo] - [cap] - [comune] ([provincia])";
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var onsellunitDaysToBeNew = '<?php echo BFCHelper::$onsellunitDaysToBeNew ?>';
var nowDate =  new Date();
var newFromDate =  new Date();
newFromDate.setDate(newFromDate.getDate() - onsellunitDaysToBeNew); 

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
	
	var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>&task=GetResourcesOnSellByIds";

	var imgPathresized =  imgPath.substring(0,imgPath.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";
	imgPath = imgPath.replace(imgPathresized , "" );

	var imgPathmerchantresized =  imgPathmerchant.substring(0,imgPathmerchant.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";
	imgPathmerchant = imgPathmerchant.replace(imgPathmerchantresized , "" );

	jQuery.post(urlCheck, query, function(data) {
			jQuery.each(data || [], function(key, val) {

				imgLogo="<?php echo $resourceImageUrl ?>";
				imgLogoError="<?php echo $resourceImageUrl ?>";
				
				imgMerchantLogo="<?php echo $merchantImageUrl ?>";
				imgMerchantLogoError="<?php echo $merchantImageUrl ?>";

				if (val.ImageUrl!= null && val.ImageUrl!= '') {
					// new system with preresized images
					var ImageUrl = val.ImageUrl.substr(val.ImageUrl.lastIndexOf('/') + 1);
					imgLogo = imgPath.replace("[img]", val.ImageUrl.replace(ImageUrl, imgPathresized + ImageUrl ) );

					// old system with resized images on the fly
					imgLogoError = imgPathError.replace("[img]", val.ImageUrl );
				}
				else if (val.Logo != '') {
					var ImageUrl = val.Logo.substr(val.Logo.lastIndexOf('/') + 1);
					imgLogo = imgPathmerchant.replace("[img]", val.Logo.replace(ImageUrl, imgPathmerchantresized + ImageUrl ) );
					
					// old system with resized images on the fly
					imgLogoError = imgPathmerchantError.replace("[img]", val.Logo);
				}
				if (val.Logo!= null && val.Logo != '') {
					var ImageUrl = val.Logo.substr(val.Logo.lastIndexOf('/') + 1);
					imgMerchantLogo = imgPathmerchant.replace("[img]", val.Logo.replace(ImageUrl, imgPathmerchantresized + ImageUrl ) );
					
					// old system with resized images on the fly
					imgMerchantLogoError = imgPathmerchantError.replace("[img]", val.Logo);
//					imgMerchantLogo = imgPathmerchant.replace("[img]", val.Logo);		
				}

				var addressData ="";
				var arrData = new Array();
				if (val.IsAddressVisible)
				{
					if(val.AddressData!= null && val.AddressData!=''){
						arrData.push(val.AddressData);
					}
				}
				if(val.LocationZone!= null && val.LocationZone!=''){
					arrData.push(val.LocationZone);
				}
				if(val.nomezona!= null && val.nomezona!=''){
					arrData.push(val.nomezona);
				}
				addressData = arrData.join(" - ");
				addressData = strAddressSimple + addressData;
				/*
				if (val.AddressData == '' &&  val.Merchant.AddressData != '') {
						xmlDoc = jQuery.parseXML(val.Merchant.AddressData);
						$xml = jQuery(xmlDoc);
						//$addressdata = $xml.find("addressdata")
						$indirizzo = $xml.find("indirizzo:first");
						addressData = strAddress.replace("[indirizzo]",$indirizzo.text())
						$cap = $xml.find("cap:first");
						addressData = addressData.replace("[cap]",$cap.text())
						$comune = $xml.find("comune:first");
						addressData = addressData.replace("[comune]",$comune.text())
						$provincia = $xml.find("provincia:first");
						addressData = addressData.replace("[provincia]",$provincia.text())
				}else{
						addressData = strAddressSimple + addressData;
				}*/

				/*
				if (val.Merchant.ContactData != '') {
					xmlDoc = jQuery.parseXML(val.Merchant.ContactData);
					$xml = jQuery(xmlDoc);
					//$addressdata = $xml.find("addressdata")
					$telefono1 = $xml.find("telefono1:first");
					if ($telefono1.text() != '') {
						jQuery("#phone"+val.Resource.ResourceId).addClass("com_bookingforconnector_phone");
						jQuery("#phone"+val.Resource.ResourceId).append($telefono1.text() + " - ");
					}
				}
				*/
				jQuery("#address"+val.ResourceId).append(addressData);
				jQuery("#logo"+val.ResourceId).attr('src',imgLogo);
				jQuery("#logo"+val.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgLogoError + "';");
					var tmpHref = jQuery("#merchantname"+val.ResourceId).attr("href");
					if (!tmpHref.endsWith("-"))
					{
						tmpHref += "-";
					}
					jQuery("#merchantname"+val.ResourceId).attr("href", tmpHref + make_slug(val.MerchantName));
					jQuery("#logomerchant"+val.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#logomerchant"+val.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");

				var descr = getXmlLanguage(val.Description,cultureCode,defaultcultureCode);;
				descr = nl2br(jQuery("<p>" + nomore1br(descr) + "</p>").text());

				jQuery("#descr"+val.ResourceId).append(descr);

				if(val.Created!= null){
					var parsedDate = new Date(parseInt(val.Created.substr(6)));
					var jsDate = new Date(parsedDate); //Date object				
					var isNew = jsDate > newFromDate;

					if (isNew)
						{
							jQuery("#ribbonnew"+val.ResourceId).removeClass("hidden");
						}
				}

				/* highlite seller*/
				if(val.HighlightExpiration!= null){
					var highlightExpirationDate = new Date(parseInt(val.HighlightExpiration.substr(6)));
					var jsHighlightExpirationDate = new Date(highlightExpirationDate); //Date object				
					var isHighlight = jsHighlightExpirationDate >= nowDate;
					if (isHighlight)
						{
							jQuery("#container"+val.ResourceId).addClass("com_bookingforconnector_highlight");
						}
				}

				/*Top seller*/
				if(val.ForegroundExpiration!= null){
					var foregroundExpirationDate = new Date(parseInt(val.ForegroundExpiration.substr(6)));
					var jsForegroundExpirationDate = new Date(foregroundExpirationDate); //Date object				
					var isForeground = jsForegroundExpirationDate >= nowDate;
					if (isForeground)
						{
							jQuery("#topresource"+val.ResourceId).removeClass("hidden");
							//jQuery("#lensimg"+val.ResourceId).removeClass("hidden");
							jQuery("#borderimg"+val.ResourceId).addClass("hidden");
						}
				}

				/*Showcase seller*/
				if(val.ShowcaseExpiration!= null){
					var showcaseExpirationDate = new Date(parseInt(val.ShowcaseExpiration.substr(6)));
					var jsShowcaseExpirationDate = new Date(showcaseExpirationDate); //Date object				
					var isShowcase = jsShowcaseExpirationDate >= nowDate;
					if (isShowcase)
						{
							jQuery("#topresource"+val.ResourceId).addClass("hidden");
							jQuery("#showcaseresource"+val.ResourceId).removeClass("hidden");
							jQuery("#lensimg"+val.ResourceId).removeClass("hidden");
							jQuery("#borderimg"+val.ResourceId).addClass("hidden");
						}
				}
				
				/*Top seller*/
				if(val.IsNewBuilding){
					jQuery("#newbuildingresource"+val.ResourceId).removeClass("hidden");
				}
				
				if(val.Rooms!=null && val.Rooms>0){
					var sp = jQuery("<div />", { "id": 'Span_' + val.ResourceId, html: val.Rooms + " vani" })
					sp.addClass("padding1020 minheight34 borderright font16 com_bookingforconnector_merchantdetails-resource-rooms");
					jQuery("#divfeature"+val.ResourceId).append(sp);
				}
				if(val.Area!=null && val.Area>0){
					var sp = jQuery("<div />", { "id": 'Span_' + val.ResourceId, html: val.Area + " mq"})
					sp.addClass("padding1020 minheight34 borderright font16 com_bookingforconnector_merchantdetails-resource-area");
					jQuery("#divfeature"+val.ResourceId).append(sp);
				}
				jQuery("#descr"+val.ResourceId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.ResourceId).shorten(shortenOption);

				jQuery("#container"+val.ResourceId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.ResourceId ).attr("href");
					}
				});

		});	
	}, "json");
}

jQuery(document).ready(function() {
	jQuery("#titlemast").html("<span class=\"titlemast\"><?php echo $activeMenu->title?></b>"); 
	if (listToCheck!=="")
	{
			jQuery(".tabcontent:first").show(); 
			jQuery(".resourcetabmenu a").click(function() {
				jQuery('.tabcontent').hide();
				var activeTab = jQuery(this).attr("rel"); 
				jQuery(".resourcetabmenu a").removeClass("selected");
				jQuery("#"+activeTab).fadeIn();
				jQuery(this).addClass("selected");
				if (activeTab=='mappa')
				{
					openGoogleMaponsellList()
			//				google.maps.event.trigger(sharedMap, 'resize');
			//				sharedMap.setCenter(new google.maps.LatLng(<?php echo $mappa; ?>));
				}

			});

		getAjaxInformations();

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

	}
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


<?php endif; ?>
