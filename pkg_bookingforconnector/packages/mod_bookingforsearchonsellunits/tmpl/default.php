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

$document 	= JFactory::getDocument();
$db   = JFactory::getDBO();
$language = JFactory::getLanguage()->getTag();
$currModID = uniqid('searchonsell');

// i valori sono impostati dal modulo
//$XGooglePos = 45.406947; 
//$YGooglePos = 11.892443;

$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);

$pars = BFCHelper::getSearchOnSellParamsSession();
$countAllResource = BFCHelper::getCountAllResourcesOnSell();

$contractTypeId = 0;
$searchType = "0";
$categoryId = 0;
$zoneId = 0;
$cityId = 0;
$zoneIds = '';
$pricemax = '';
$pricemin = '';
$areamin = '';
$areamax = '';
$points = '';
$roomsmin = '';
$roomsmax = '';
$bathsmin = '';
$bathsmax = '';
$services = '';
$isnewbuilding='';
$zoneIdsSplitted = '';
$bedroomsmin = '';
$bedroomsmax = '';

if (!empty($pars)){
	$contractTypeId = $pars['contractTypeId'] ?: 0;
	$categoryId = $pars['unitCategoryId'];
	$zoneId = $pars['zoneId'] ?: 0;
	if(!empty($pars['cityId'])){
		$cityId = $pars['cityId'] ?: 0;
	}
	$searchType = $pars['searchType'] ?: 0;
	if(!empty($pars['zoneIds'])){
		$zoneIds = $pars['zoneIds'];
		$zoneIdsSplitted = explode(",",$zoneIds);
	}
	$pricemax = $pars['pricemax'];
	$pricemin = $pars['pricemin'];
	$areamin = $pars['areamin'];
	$areamax = $pars['areamax'];
	$roomsmin = $pars['roomsmin'];
	$roomsmax = $pars['roomsmax'];
	$bathsmin = $pars['bathsmin'];
	$bathsmax = $pars['bathsmax'];
	$points = $pars['points'];
	$services = $pars['services'];
	if (isset($pars['isnewbuilding']) && !empty($pars['isnewbuilding']) && $pars['isnewbuilding'] =="1") {
		$isnewbuilding = ' checked="checked"';
	}
	$bedroomsmin = $pars['bedroomsmin'];
	$bedroomsmax = $pars['bedroomsmax'];
}
if (!empty($services) ) {
	$filtersServices = explode(",", $services);
}

if (isset($filters)) {
//	if (!empty($filters['stars'])) {
//		$filtersStars = explode(",", $filters['stars']);
//	}
//	if (!empty($filters['zoneIds'])) {
//		$filterszoneIds = explode(",", $filters['zoneIds']);
//	}
//	if (!empty($filters['merchantgroups'])) {
//		$filtersMerchantGroups = explode(",", $filters['merchantgroups']);
//	}
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
	}
//	if (!empty($filters['mastertypologies'])) {
//		$filtersMasterTypologies = explode(",", $filters['mastertypologies']);
//	}
//	if (!empty($filters['pricemin'])) {
//		$filtersPriceMin = 	$filters['pricemin'];// / $duration;
//	}
//	if (!empty($filters['pricemax'])) {
//		$filtersPriceMax = 	$filters['pricemax'];// / $duration;
//	}
//	if (!empty($filters['bookingtypes'])) {
//		$filtersBookingTypes = explode(",", $filters['bookingtypes']);
//	}

}


$listServices = BFCHelper::getServicesForSearchOnSell($language);

$locations = BFCHelper::getLocations();

$listlocations = array();
$listlocations[] = JHTML::_('select.option', 0, JTEXT::_('MOD_BOOKINGFORSEARCH_ALL'));
if(!empty($locations)){
	foreach ($locations as $lz) {
		if(empty($cityId) && $cityId != 0)
			$cityId = $lz->CityId;

		$listlocations[] = JHTML::_('select.option', $lz->CityId, $lz->Name);
	}
}
$listlocations[] = JHTML::_('select.option', -1000, JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP'));
		
$unitCategories = BFCHelper::GetProductCategoryForSearch($language,2);

$listunitCategories = array();
$listunitCategories[] = JHTML::_('select.option', -1, JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_FEMMINILE'));
$isopenGroup=0;
if(!empty($unitCategories)){
	foreach ($unitCategories as $uc) {
//		if (!empty($uc->ParentCategoryId)){
			$listunitCategories[] = JHTML::_('select.option', $uc->ProductCategoryId,$uc->Name );
//		}
	}
}

$listcontractType = array(
	JHTML::_('select.option', '0', JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE_SELL') ),
	JHTML::_('select.option', '1', JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE_RENT') ),
);

$baths = array(
	JHTML::_('select.option', '|', JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE') ),
	JHTML::_('select.option', '1|1', JText::_('1') ),
	JHTML::_('select.option', '2|2', JText::_('2') ),
	JHTML::_('select.option', '3|3', JText::_('3') ),
	JHTML::_('select.option', '3|', JText::_('>3') )
);


$show_direction = $params->get('show_direction');
$show_title = $params->get('show_title');

$listzoneIds = array();
if($cityId>=-1) {
	$zoneIdsList = BFCHelper::getLocationZonesByLocationId($cityId);
	
//	echo "<pre>";
//	echo print_r($zoneIdsList);
//	echo "</pre>";
	
	$listzoneIds = array();
	if(!empty($zoneIdsList)){
		foreach ($zoneIdsList as $lz) {
			$listzoneIds[] = JHTML::_('select.option', $lz->GeographicZoneId, $lz->Name);
		}
	}
}


?>
<div class="<?php echo $moduleclass_sfx ?>"><!-- per span8 e padding -->
<div class="mod_bookingforsearch" style="position:relative;">
<?php if($show_title) :?><h4 class="bookingfor_title"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TITLE'); ?></h4><?php endif; ?>
<form action="<?php echo $formAction; ?>" method="post" id="searchformonsellunit<?php echo $currModID ?>">
<?php if($show_direction) :?>
<?php 
// reset ricerca servizi
$services="";
?>

	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 divmappaleft" >        
					<div class="totunitonsell"><?php echo sprintf(JTEXT::_('MOD_BOOKINGFORSEARCH_TOTAL'), $countAllResource) ?></div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8" id="searchBlock">        
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE'); ?></label>
					<?php echo JHTML::_('select.genericlist', $listcontractType, 'contractTypeId', array('onchange'=>'checkSelSearch();', 'class'=>'select90percent'), 'value', 'text', $contractTypeId);?>
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
					<?php echo JHTML::_('select.genericlist', $listunitCategories, 'unitCategoryId', array('onchange'=>'checkSelSearch();', 'class'=>'select90percent' ), 'value', 'text', $categoryId);?>
				</div><!--/span-->
			</div><!--/<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE'); ?></label>
					<?php echo JHTML::_('select.genericlist', $listlocations, 'cityId', array('onchange'=>'checkSelSearch();' , 'class'=>'select90percent'), 'value', 'text', $cityId);?>
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6" style="margin-top:5px;">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" id="row-zones">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3" style="text-align:center;"><label style="display: inline-block;vertical-align: middle;line-height: normal;"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_LOCATIONZONE') ?></label></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9">
							<div id="btnZones">
								<input type="radio" name="searchType" id="zoneSearch" value="0"  <?php echo $searchType=="0"? "checked":""; ?>   />
								<label for="zoneSearch" id="lblZoneSearch" class="lblcheckzone"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_ZONES') ?></label>
							</div>
							<div id="btnArea" onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)" >
								<input type="radio" name="searchType" id="mapSearch" value="1"  <?php echo $searchType=="1"? "checked":""; ?>  />
								<label id="lblMapSearch" class="lblcheckzone" ><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DRAW_MAP') ?></label>
							</div>
							<input name="zoneIds" id="zoneIds" type="hidden" value="" />
						</div>
					</div>
				</div><!--/span-->
			</div><!--/<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_PRICE') ?></label>
					<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $pricemin;?>" class="select90percent" > 
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label>&nbsp;</label>
					<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>" class="select90percent" > 
				</div><!--/span-->
			</div><!--/<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_AREA') ?></label>
					<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" class="select90percent"  >
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<label>&nbsp;</label>
					<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $areamax;?>" class="select90percent"  >
				</div><!--/span-->
			</div><!--/<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" id="searchButtonArea">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 offset6" id="divBtn">
						<a  id="aBtn2<?php echo $currModID ?>" class="btn input-medium" href="javascript: void(0);"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
				</div><!--/span10-->
			</div><!--/<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>-->
		</div><!--/span10-->
	</div><!--/<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>-->
	<input type="hidden" value="" name="roomsmin" />
	<input type="hidden" value="" name="roomsmax" />
	<input type="hidden" value="" name="bathsmin" />
	<input type="hidden" value="" name="bathsmax" />
	<input type="hidden" value="" name="bedroomsmin" />
	<input type="hidden" value="" name="bedroomsmax" />
	
<!-- Zone Popup -->
	<div id="zonePopup" class="zone-dialog">
		<div class="dialog-header">
			<div class="header-content">
				<?php echo JText::_('MOD_BOOKINGFORSEARCH_ZONE_POPUP_TITLE') ?>
				<div class="pull-right dialog-closer">x</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="dialog-container">
			<div class="dialog-content">
				<?php if ($cityId >= -1): echo JHTML::_('select.genericlist', $listzoneIds, 'zoneIdsList', array('onchange'=>'checkSelSearch();' , 'class'=>'select90percent multiselect', 'multiple'=>'multiple'), 'value', 'text', $zoneIdsSplitted); endif ?>
			</div>
		</div>
	</div>
<?php else: //show_direction ?>
	<div  id="searchBlock" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div>
			<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE'); ?></label>
			<?php echo JHTML::_('select.genericlist', $listcontractType, 'contractTypeId', array('onchange'=>'checkSelSearch();' , 'class'=>'select90percent' ), 'value', 'text', $contractTypeId);?>
		</div><!--/span-->
		<div>
			<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE'); ?></label>
			<?php echo JHTML::_('select.genericlist', $listlocations, 'cityId', array('onchange'=>'checkSelSearch();' , 'class'=>'select90percent' ), 'value', 'text', $cityId);?>
			<div id="btnZones">
				<input type="radio" name="searchType" id="zoneSearch" value="0" <?php echo $searchType=="0"? "checked":""; ?> />
				<label for="zoneSearch" id="lblZoneSearch"  class="lblcheckzone"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_ZONES') ?></label>
			</div>
			<div onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)" >
				<input type="radio" name="searchType" id="mapSearch" value="1" <?php echo $searchType=="1"? "checked":""; ?> />
				<label id="lblMapSearch"  class="lblcheckzone"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DRAWN_AREA') ?></label>
			</div>
			
			
			<input name="zoneIds" id="zoneIds" type="hidden" value="<?php echo $zoneIds; ?>" />
			
		</div><!--/span-->
		<div>
			<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
			<?php echo JHTML::_('select.genericlist', $listunitCategories, 'unitCategoryId', array('onchange'=>'checkSelSearch();' , 'class'=>'select90percent' ), 'value', 'text', $categoryId);?>
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_PRICE') ?></label>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $pricemin;?>" style="width:90%" > 
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>"  style="width:90%" > 
				</div><!--/span-->
			</div>
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_AREA') ?></label>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" style="width:90%" > 
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $areamax;?>" style="width:90%" > 
				</div><!--/span-->
			</div>
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BEDROOMS') ?></label>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
			<input name="bedroomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bedroomsmin;?>" style="width:90%" > 
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
			<input name="bedroomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bedroomsmax;?>" style="width:90%" > 
				</div><!--/span-->
			</div>
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ROOMS') ?></label>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
			<input name="roomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $roomsmin;?>" style="width:90%" > 
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
			<input name="roomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $roomsmax;?>" style="width:90%" > 
				</div><!--/span-->
			</div>
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BATHS') ?></label>
			<?php echo JHTML::_('select.genericlist', $baths, 'baths', array('onchange'=>'changeBaths();'  , 'class'=>'select90percent') , 'value', 'text', ($bathsmin ."|". $bathsmax));?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
			<input name="bathsmin" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bathsmin;?>" style="width:90%" > 
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
			<input name="bathsmax" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bathsmax;?>" style="width:90%" > 
				</div><!--/span-->
			</div>
		</div><!--/span-->

		<?php if (isset($listServices)) :?>
		<?php  $countServ=0;?>
		<div>
			<!-- <label><?php echo JText::_('MOD_BOOKINGFORSEARCH_SERVICES') ?></label> -->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
				<?php foreach ($listServices as $singleService):?>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<?php $checked = '';
						if (isset($filtersServices) &&  is_array($filtersServices) && in_array($singleService->ServiceId,$filtersServices)){
							$checked = ' checked="checked"';
						}
					?>
						<label class="checkbox"><input type="checkbox" name="services"  class="checkboxservices" value="<?php echo ($singleService->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($singleService->Name, $language) ?></label>
					</div>
				<?php  $countServ++;
				if($countServ%2==0):?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">	
				<?php endif ?>

				<?php endforeach; ?>
			</div>
		</div><!--/span-->
		<?php endif ?>
		<div class="checkbox">  
			<label class="checkbox"><input type="checkbox" name="isnewbuilding" value="1" <?php echo $isnewbuilding ?> /><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ISNEWBUILDING') ?></label>
		</div><!--/span-->

		<div id="searchButtonArea">
			<div class="" id="divBtn">
				&nbsp;<br />
				<a  id="aBtn2<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);"  style="width:90%;"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
			</div>
		</div>
		
		<div class="clearboth"></div>
		<div id="sendalert" class="alert alert-success" style="display:none;margin-top:5px;"></div>
	</div>
<!-- Zone Popup -->
	<div id="zonePopup" class="zone-dialog" style="width:100%">
		<div class="dialog-header">
			<div class="header-content">
				<?php echo JText::_('MOD_BOOKINGFORSEARCH_ZONE_POPUP_TITLE') ?>
				<div class="pull-right dialog-closer">x</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="dialog-container">
			<div class="dialog-content">
				<?php if ($cityId >= -1): echo JHTML::_('select.genericlist', $listzoneIds, 'zoneIdsList', array('onchange'=>'checkSelSearch();' , 'class'=>'select90percent multiselect', 'multiple'=>'multiple'), 'value', 'text', $zoneIdsSplitted); endif ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
	<input type="hidden" value="1" name="newsearch" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
	<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
	<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell" />
	

</form>
<div id="datasend"></div>

<script type="text/javascript">
<!--
var $dialog;
var currentLocation=0;
$Lng = <?php echo $XGooglePosDef?>;
$Lat = <?php echo $YGooglePosDef?>;
$googlemapsapykey = '<?php echo $googlemapsapykey?>';
$startzoom = <?php echo $startzoom?>;


var img1 = new Image(); 
img1.src = "<?php echo JURI::root();?>media/com_bookingfor/images/loader.gif";

function checkSelSearch(){
	var vals=[];
	jQuery("#zonePopup .multiselect option:selected").each(function(i,selected){
		vals[i]=jQuery(this).val();
	});
	jQuery("#zoneIds").val(vals.toString());
	if(jQuery("#cityId").val()!= 0 && jQuery("#cityId").val()>=-1 && vals.length>0){
		jQuery("#lblZoneSearch").text(vals.length+" <?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_SELECTED_ZONES') ?>");
	} else if(jQuery("#cityId").val()!= 0 && jQuery("#cityId").val()>=-1) {
		jQuery("#lblZoneSearch").text("<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_CHOOSE_ZONES') ?>");
	} else {
		jQuery("#lblZoneSearch").text("<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_ZONES') ?>");
	}
	currentLocation=parseInt(jQuery("#cityId").val());
	if(currentLocation<-1){
		jQuery('#btnZones').attr("onclick","");
		jQuery('#btnZones').attr("href","");
		if(jQuery("#points").val() ==""){
			openGoogleMapBFSSell();
		}
	} else if(currentLocation>=-1){
		jQuery('#btnZones').attr("onclick","javascript:openZonesPopup();");
		jQuery('#btnZones').attr("href","javascript:void(0);");
	}else{
		jQuery('#btnZones').attr("onclick","");
		jQuery('#btnZones').attr("href","");
	}
}

function openZonesPopup(){
	jQuery('#zonePopup').show();
}

function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

function changeBaths(){
	var bathsselect = jQuery("#searchformonsellunit<?php echo $currModID ?> select[name='baths']").val();
	var vals = bathsselect.split("|"); 
	jQuery("#searchformonsellunit<?php echo $currModID ?> input[name='bathsmin']").val(vals[0]);
	jQuery("#searchformonsellunit<?php echo $currModID ?> input[name='bathsmax']").val(vals[1]);
}

function sendSearchForm(){
}

function getBottomPosition(elm){
	return jQuery(window).height() - top - elm.height();
}

function resizeZoneTitle(){
	if(jQuery(window).width()>=600){
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').css("line-height",jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9').height()+"px");
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 label').css("line-height",jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9').height()+"px");
	} else{
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').css("line-height","");
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 label').css("line-height","");
	}
			jQuery("#zonePopup").css({
				left:jQuery("#searchBlock").offset().left-jQuery("#searchformonsellunit<?php echo $currModID ?>").offset().left+15
			});
			
			<?php if(!$show_direction) :?>
			if ((jQuery("#searchBlock").height()-jQuery(window).height())>0) {
				jQuery("#zonePopup").css({
					bottom:80+(jQuery("#searchBlock").height()-jQuery(window).height())
				});
			} else {
				jQuery("#zonePopup").css({
					height:"",
					bottom:80
				});
			}
			
			<?php endif ?>
}

//function waitBlockUI(msg1 ,msg2, img1){
//	jQuery("#zonePopup").hide();
//	jQuery.blockUI({
//		message: '<h1 style="font-size: 15px;">'+msg1+'<br />'+msg2+'</h1><br /><img src="'+img1.src+'" width="48" height="48" alt="" border="0" />', 
//		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B'},
//		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
//		});
//	}

jQuery(window).resize(function() {
	resizeZoneTitle();
});

jQuery(function($)
		{
			resizeZoneTitle();
			jQuery(".zone-dialog .dialog-closer").click(function(e){
				jQuery(this).closest(".zone-dialog").hide();
			});
			
			jQuery('.btn-radio input').click(function(e){
				//if($dialog==null){
					var searchBoxOffset=jQuery("#searchBlock").offset();
					var width=jQuery("#searchBlock").width();
					var height=(jQuery("#searchButtonArea").offset().top)-(searchBoxOffset.top);
//					console.log(searchBoxOffset.top);
			});
			$('#aBtn2<?php echo $currModID ?>').click(function(e) {
				e.preventDefault();
				$("#searchformonsellunit<?php echo $currModID ?>").submit(); 
			});
			
			var searchBoxOffset=jQuery("#searchBlock").offset();
			var width=jQuery("#searchBlock").width();
			var height=(jQuery("#searchButtonArea").offset().top)-(searchBoxOffset.top);
			jQuery('#zonePopup .multiselect').multiSelectToCheckboxes();
			
			checkSelSearch();
			jQuery('.checkboxservices').on('click',function() {
				updateHiddenValue('.checkboxservices:checked','#servicesonsell<?php echo $currModID ?>')	
			});
			
			jQuery("#btnconfirm").click(function(e){
				jQuery("#lblMapSearch").text("<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_DRAWN_AREA') ?>");
			});
			
			var zoneIdsurl = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&view=merchants&format=json&locationId=') ?>"+jQuery('#cityId').val();	
			
			jQuery('#cityId').change(function(){
				var searchBoxOffset=jQuery("#searchBlock").offset();
				var width=jQuery("#searchBlock").width();
				var height=(jQuery("#searchButtonArea").offset().top)-(searchBoxOffset.top);
				if(jQuery(this).val()>=-1){
					jQuery('#zonePopup .dialog-content').empty();
					jQuery.ajax({
						type:"POST",
						url:zoneIdsurl,
						data:{
							locationId:jQuery('#cityId').val()
						},
						dataType:"json",
						success:function(result){
							var select=jQuery("<select>");
							select.addClass("multiselect");
							select.attr('onchange','checkSelSearch();');
							select.attr("multiple","multiple");
							jQuery(result).each(function(i,itm){
								var opt=jQuery("<option>");
								opt.val(itm.LocationZoneID);
								opt.text(itm.Name);
								select.append(opt);
							});
							jQuery("#zonePopup .dialog-content").append(select);
							select.multiSelectToCheckboxes();
						}
					});
				} 
			}); 
			

			$("#searchformonsellunit<?php echo $currModID ?>").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {


                        alert(validator.errorList[0].message);

                        validator.errorList[0].element.focus();
                    }
                },
		        rules:
		        {
		        	pricemin:
					{
		                required: false,
		                digits: true
		            },
		        	pricemax:
					{
		                required: false,
		                digits: true
		            },
		            areamin:
		            {
		                required: false,
		                digits: true
		            },
		            areamax:
		            {
		                required: false,
		                digits: true
		            },
		            roomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            roomsmax:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            }
		        },
		        messages:
		        {
		        	pricemin: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	pricemax: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	areamin: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	areamax: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	roomsmin: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	roomsmax: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	bedroomsmin: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	bedroomsmax: {
		        		required:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		}
		        },
				highlight: function(label) {
			    },
			    success: function(label) {
					$(label).remove();
			    },
				submitHandler: function(form) {
						msg1 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG1'); ?>";
						msg2 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG2'); ?>";
						jQuery("#zonePopup").hide();

						waitBlockUI(msg1, msg2,img1); 
						jQuery("#aBtn2<?php echo $currModID ?>").hide();
						form.submit();
				}

		    });

		});
		
		

jQuery.fn.multiselect = function() {
    jQuery(this).each(function() {
        var checkboxes = jQuery(this).find("input:checkbox");
        checkboxes.each(function() {
            var checkbox = jQuery(this);
            // Highlight pre-selected checkboxes
            if (checkbox.prop("checked"))
                checkbox.parent().addClass("multiselect-on");
 
            // Highlight checkboxes that the user selects
            checkbox.click(function() {
                if (checkbox.prop("checked"))
                    checkbox.parent().addClass("multiselect-on");
                else
                    checkbox.parent().removeClass("multiselect-on");
            });
        });
    });
};

var methods = {
        init: function() {
            var $ul = jQuery("<ul/>").insertAfter(this);
			$ul.addClass(jQuery(this).attr("class"));
            var baseId = "_" + jQuery(this).attr("id");
            jQuery(this).children("option").each(function(index) {
                var $option = jQuery(this);
                var id = baseId + index;
                var $li = jQuery("<li/>").appendTo($ul);
                var $label = jQuery("<label for='" + id + "' class='aligncheckbox' >" + $option.text() + "</label>").appendTo($li);
				var $checkbox = jQuery("<input type='checkbox' id='" + id + "'/>").prependTo($label).change(function() {
                    if (jQuery(this).is(":checked")) {
                        $option.attr("selected", "selected");
                    } else {
                        $option.removeAttr("selected");
                    }
					checkSelSearch();
                });
                if ($option.is(":selected")) {
                    $checkbox.attr("checked", "checked");
                }

//                $checkbox.after("<label for='" + id + "' style='display:inline;'>" + $option.text() + "</label>");
            });
            jQuery(this).hide();
        }
    };

    jQuery.fn.multiSelectToCheckboxes = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.multiSelectToCheckboxes');
        }

    };

	//-->
	</script>
	<div id="divBFSSell" style="width:100%; height:400px; display:none;">
		<div style="width:100%; height:50px; position:relative;">
			<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_TITLE'); ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>"> 
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<ul class="nav nav-pills">
						<li><a class="btn select-figure" id="btndrawpoligon" onclick="javascript: drawPoligon()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_AREA'); ?></a></li>
						<li><a class="btn select-figure" id="btndrawcircle" onclick="javascript: drawCircle()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_CIRCLE'); ?></a></li>
					</ul>				
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6 text-right">
					<input type="text" id="addresssearch" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_ADDRESS_BUTTON') ?>" />
					<div id="btnCompleta" class="input-prepend input-append" style="display:none;">
						<a class="btn btn-delete" id="btndelete" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_DELETE'); ?></a>
						<a class="btn" id="btnconfirm" type="button" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_SUBMIT'); ?></a>
						<span class="add-on" id="spanArea"></span>
					</div>
				
				</div><!--/span-->
			</div>

		</div>
		<div id="map_canvasBFSSell" class="map_canvasBFSSell" style="width:100%; height:350px;"></div>
		<div class="map-tooltip"><strong><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_USERGUIDE'); ?></strong></div>
</div>
</div>
</div>
