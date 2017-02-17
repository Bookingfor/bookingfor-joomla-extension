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
$lang = JFactory::getLanguage()->getTag();

// i valori sono impostati dal modulo
//$XGooglePos = 45.406947; 
//$YGooglePos = 11.892443;

$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($lang) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);

$pars = BFCHelper::getSearchOnSellParamsSession();

$contractTypeId = 0;
$categoryId = 0;
$zoneId = 0;
$pricemax;
$pricemin;
$areamin;
$areamax;
$points;
$roomsmin;
$roomsmax;
$bathsmin;
$bathsmax;
$services;

if (!empty($pars)){
	$contractTypeId = $pars['contractTypeId'] ?: 0;
	$categoryId = $pars['unitCategoryId'];
	$zoneId = $pars['zoneId'] ?: 0;
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
}
if (!empty($services) ) {
	$filtersServices = explode(",", $services);
}

if (isset($filters)) {
//	if (!empty($filters['stars'])) {
//		$filtersStars = explode(",", $filters['stars']);
//	}
//	if (!empty($filters['locationzones'])) {
//		$filtersLocationZones = explode(",", $filters['locationzones']);
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


$listServices = BFCHelper::getServicesForSearchOnSell();

$locationZones = BFCHelper::getLocations();
$listlocationZones = array();
$listlocationZones[] = JHTML::_('select.option', 0, JTEXT::_('MOD_BOOKINGFORSEARCH_ALL'));
foreach ($locationZones as $lz) {
	$listlocationZones[] = JHTML::_('select.option', $lz->LocationID, $lz->Name);
}
	$listlocationZones[] = JHTML::_('select.option', -1, JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP'));

$unitCategories = BFCHelper::getUnitCategories();
$listunitCategories = array();
$listunitCategories[] = JHTML::_('select.option', -1, JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_FEMMINILE'));
$isopenGroup=0;
foreach ($unitCategories as $uc) {
	if (!empty($uc->CategoryParentID)){
		$listunitCategories[] = JHTML::_('select.option', $uc->CategoryID, BFCHelper::getLanguage($uc->Name, $lang) );
	}else{
		if ($isopenGroup==1){
			$listunitCategories[] = JHTML::_('select.optgroup','');
			$isopenGroup=0;
		}
		$listunitCategories[] = JHTML::_('select.optgroup', BFCHelper::getLanguage($uc->Name, $lang) );
		$isopenGroup=1;
	}
}
		if ($isopenGroup==1){
			$listunitCategories[] = JHTML::_('select.option','</OPTGROUP>');
			$isopenGroup=0;
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


?>
<div class="mod_bookingforsearch<?php echo $moduleclass_sfx ?>">
<?php if($show_title) :?><h4 class="bookingfor_title"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TITLE'); ?></h4><?php endif; ?>
<form action="<?php echo $formAction; ?>" method="post" id="searchformonsellunit">
<?php if($show_direction) :?>
<?php 
// reset ricerca servizi
$services="";
?>

	<div class="row-fluid">
		<div class="span12">        
			<div class="row-fluid">   
				<div class="span4">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE'); ?></label>
					<?php echo JHTML::_('select.genericlist', $listcontractType, 'contractTypeId', array('onchange'=>'checkSelSearch();' ), 'value', 'text', $contractTypeId);?>
				</div><!--/span-->
				<div class="span8">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE'); ?></label>
					<?php echo JHTML::_('select.genericlist', $listlocationZones, 'zoneId', array('onchange'=>'checkSelSearch();' ), 'value', 'text', $zoneId);?>
					<?php if(!empty($XGooglePos) && !empty($YGooglePos)) :?>	  
						<a class="lightboxlink imgopengooglemap" onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)"><img src="<?php echo JURI::root();?>images/map.png" width="32" /></a>
					<?php endif; ?>
				</div><!--/span-->
			</div><!--/row-->
		</div><!--/span10-->
	</div><!--/row-fluid-->
	<div class="row-fluid">
		<div class="span12">
			<div class="row-fluid">
				<div class="span4">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
					<?php echo JHTML::_('select.genericlist', $listunitCategories, 'unitCategoryId', array('onchange'=>'checkSelSearch();' ), 'value', 'text', $categoryId);?>
				</div><!--/span-->
				<div class="span3">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_PRICE_MAX') ?></label>
					<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>" class="input-small" > 
				</div><!--/span-->
				<div class="span3">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_AREA_MIN') ?></label>
					<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" class="input-small"  >
				</div><!--/span-->
				<div class="span2">        
					<div class="pull-right" id="divBtn">
						&nbsp;<br />
						<a  id="aBtn2" class="btn" href="javascript: void(0);"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
					</div>
				</div><!--/span2-->
			</div><!--/row-fluid-->
		</div><!--/span10-->
	</div><!--/row-fluid-->
	<input type="hidden" value="" name="roomsmin" />
	<input type="hidden" value="" name="roomsmax" />
	<input type="hidden" value="" name="bathsmin" />
	<input type="hidden" value="" name="bathsmax" />


	<?php else: ?>
		<div>
			<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE'); ?></label>
			<?php echo JHTML::_('select.genericlist', $listcontractType, 'contractTypeId', array('onchange'=>'checkSelSearch();' ), 'value', 'text', $contractTypeId);?>
		</div><!--/span-->
		<div>
			<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE'); ?></label>
			<?php echo JHTML::_('select.genericlist', $listlocationZones, 'zoneId', array('onchange'=>'checkSelSearch();','class'=>'selectsearch' ), 'value', 'text', $zoneId);?>
			<?php if(!empty($XGooglePos) && !empty($YGooglePos)) :?>	  
				<a class="lightboxlink imgopengooglemap" onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)">
					<img src="<?php echo JURI::root();?>images/map.png" width="32" />
				</a>
			<?php endif; ?>
		</div><!--/span-->
		<div>
			<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
			<?php echo JHTML::_('select.genericlist', $listunitCategories, 'unitCategoryId', array('onchange'=>'checkSelSearch();' ), 'value', 'text', $categoryId);?>
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_PRICE') ?></label>
			<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $pricemin;?>" class="input-small" > 
			<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>" class="input-small" > 
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_AREA_MIN') ?></label>
			<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" class="input-small"  >
			<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $areamax;?>" class="input-small"  >
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ROOMS') ?></label>
			<input name="roomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $roomsmin;?>" class="input-small"  >
			<input name="roomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $roomsmax;?>" class="input-small"  >
		</div><!--/span-->
		<div>
			<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BATHS') ?></label>
			<?php echo JHTML::_('select.genericlist', $baths, 'baths', array('onchange'=>'changeBaths();' ) , 'value', 'text', ($bathsmin ."|". $bathsmax));?>
			<input name="bathsmin" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bathsmin;?>" class="input-small"  >
			<input name="bathsmax" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bathsmax;?>" class="input-small"  >
		</div><!--/span-->

		<?php if (isset($listServices)) :?>
		<div>
			<!-- <label><?php echo JText::_('MOD_BOOKINGFORSEARCH_SERVICES') ?></label> -->
			<div class="row-fluid">
				<?php foreach ($listServices as $singleService):?>
					<div class="span6">
					<?php $checked = '';
						if (isset($filtersServices) &&  is_array($filtersServices) && in_array($singleService->ServiceId,$filtersServices)){
							$checked = ' checked="checked"';
						}
					?>
						<label class="checkbox inline"><input type="checkbox" name="services"  class="checkboxservices" value="<?php echo ($singleService->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($singleService->Name, $language) ?></label>
					</div>
				<?php endforeach; ?>
			</div>
		</div><!--/span-->
		<?php endif ?>


		<div class="" id="divBtn">
			&nbsp;<br />
			<a  id="BtnMailAlert" class="btn " href="javascript: void(0);"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MAILALERT'); ?></a>
			<a  id="aBtn2" class="btn pull-right" href="javascript: void(0);"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
		</div>
		<div class="clearboth"></div>
		<div id="divMailAlert" class="datacustomer" style="display:none;">
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME') ?>*</label>
				<input name="Name" type="text" placeholder="" value="<?php echo $firstName;?>" >    
			</div>
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME') ?>*</label>
				<input name="Surname" type="text" placeholder=""  value="<?php echo $lastName;?>" >    
			</div>
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_EMAIL') ?>*</label>
				<input name="Email" type="text" placeholder="email" value="<?php echo $email;?>" >    
			</div>
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_PHONE') ?>*</label>
				<input name="phone" type="text" placeholder="" value="<?php echo $phone;?>" >   
			</div>
            <div>
	         	<input  type="checkbox" value="true" name="confirmprivacy" id="confirmprivacy" />
				<label for="confirmprivacytrue"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_CONFIRMPRIVACY') ?></label>
			</div>
			<div class="pull-right" id="divBtndatacustomer">
				&nbsp;<br />
				<a  id="aBtndatacustomer" class="btn" href="javascript: void(0);"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ENABLE'); ?></a>
			</div>
		</div>
		<div id="sendalert" class="alert alert-success" style="display:none;margin-top:5px;"></div>

	<?php endif; ?>
	<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
	<input type="hidden" value="1" name="newsearch" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" value="<?php echo $lang ?>" name="cultureCode" />
	<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
	<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell" />
	
</form>
<div id="datasend"></div>

<script type="text/javascript">
<!--

$XGooglePos = <?php echo $XGooglePos?>;
$YGooglePos = <?php echo $YGooglePos?>;
var urlAlert = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&task=sendAlertOnSell') ?>?";	
var msgAlertOk = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MAILALERT_SENDOK'); ?>";
var msgAlertKo = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MAILALERT_SENDKO'); ?>";

var img1 = new Image(); 
img1.src = "<?php echo JURI::root();?>media/com_bookingfor/images/loader.gif";

function checkSelSearch(){
	if(jQuery("#zoneId").val()==="-1"){
		if(jQuery("#points").val() ==""){
			openGoogleMapBFSSell()
		}
	}
}

function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

function changeBaths(){
	var bathsselect = jQuery("#searchformonsellunit select[name='baths']").val();
	var vals = bathsselect.split("|"); 
	jQuery("#searchformonsellunit input[name='bathsmin']").val(vals[0]);
	jQuery("#searchformonsellunit input[name='bathsmax']").val(vals[1]);
}

function sendSearchForm(){
}

function waitBlockUI(msg1 ,msg2, img1){
	jQuery.blockUI({
		message: '<h1 style="font-size: 15px;">'+msg1+'<br />'+msg2+'</h1><br /><img src="'+img1.src+'" width="48" height="48" alt="" border="0" />', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B'},
		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		});
	}

jQuery(function($)
		{
			$('#aBtn2').click(function(e) {
				e.preventDefault();
				$("#searchformonsellunit").submit(); 
			});
			$('#aBtndatacustomer').click(function(e) {
				e.preventDefault();
				$("#searchformonsellunit").submit(); 
			});

			$('#BtnMailAlert').click(function(e) {
				e.preventDefault();
				 $("#sendalertKo").hide();
				 $("#sendalertOk").hide();
				$("#divMailAlert").slideToggle("slow");
				jQuery("#aBtn2").toggle("slow");
			});
		
			jQuery('.checkboxservices').live('click',function() {
				updateHiddenValue('.checkboxservices:checked','#servicesonsell')	
			});

			$("#searchformonsellunit").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
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
		        	Name: "required",
		        	Surname: "required",
		        	phone: "required",
		            Email:
		            {
		                required: true,
		                email: true
		            },
		        	confirmprivacy : "required"
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
		        	Name: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME_ERROR') ?>",
		        	Surname: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME_ERROR') ?>",
		        	phone: "<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        	confirmprivacy: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CONFIRM_ERROR') ?>",
		            Email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL_ERROR') ?>"
		        },
		        //errorPlacement: function(error, element) { //just nothing, empty  },
				highlight: function(label) {
			    	//$(label).removeClass('error').addClass('error');
			    	//$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
					$(label).remove();
					//label.hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {

					// send data to alertsystem
					if ($("#divMailAlert").is(':visible'))
					{

						$.ajax({
							 type: "POST",
							 url: urlAlert,
							 data: $(form).serialize(),
							 success: function (data) {
//								$("#datasend").html(data);
								
								$("#divMailAlert").hide();
								jQuery("#aBtn2").toggle("slow");
								var divAlert =  $("#sendalert");
								
								if (data) // ok
								{
									divAlert.removeClass('alert-error').addClass("alert-success");
									divAlert.html(msgAlertOk);
								}else{  //ko
									divAlert.removeClass('alert-success').addClass("alert-error");
									divAlert.html(msgAlertKo);
								}
								divAlert.show();
								setTimeout(function() {
									divAlert.fadeOut('slow');
								}, 4000); 
							 }
						 });
						return false;
					}else{
						//else normal submit
						
						msg1 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG1'); ?>";
						msg2 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG2'); ?>";
						waitBlockUI(msg1, msg2,img1); 
	//					jQuery("#aBtn2").addClass("hide");
	//					jQuery("#divBtn").addClass("loading").queue(function(){
	//						form.submit();
	//					});
						jQuery("#aBtn2").hide();
						form.submit();
					}
				}


		    });

		});


	//-->
	</script>
	<div id="divBFSSell" style="width:100%; height:400px; display:none;">
		<div style="width:100%; height:50px;">
			<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_TITLE'); ?>
			<div class="input-prepend input-append">
				<a class="btn" id="btndrawpoligon" href="javascript: void(0);" onclick="javascript: drawPoligon()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_AREA'); ?></a>
				<a class="btn" id="btndrawcircle" onclick="javascript: drawCircle()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_CIRCLE'); ?></a>
			</div>
			<div id="searchAdress" class="input-prepend input-append pull-right">
				<span class="add-on"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_ADDRESS_TITLE'); ?> </span>
				<input type="text" id="addresssearch" style="z-index:10000;"/>
				<input class="btn" type="button" value="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_ADDRESS_BUTTON'); ?>" onclick="codeAddress()">
			</div>
			<div id="btnCompleta" class="input-prepend input-append pull-right" style="display:none;">
				<a class="btn" id="btndelete" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_DELETE'); ?></a>
				<a class="btn" id="btnconfirm" type="button" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_SUBMIT'); ?></a>
				<span class="add-on" id="spanArea"></span>
			</div>
		</div>
		<div id="map_canvasBFSSell" style="width:100%; height:350px;"></div>
	</div>
</div>
