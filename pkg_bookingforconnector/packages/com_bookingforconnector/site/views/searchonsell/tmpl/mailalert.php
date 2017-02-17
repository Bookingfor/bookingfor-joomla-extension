<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.module.helper');

$language = $this->language;
$doc = $this->document;

// load scripts
$doc->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');

// load scripts
$doc->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
$doc->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
$doc->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
$doc->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		


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
$bedroomsmin;
$bedroomsmax;

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
/***setting***/
//echo "<pre>this";
//echo print_r($this);
//echo "</pre>";

// Menu parameters
$input = JFactory::getApplication()->input;
$menuitemid = $input->getInt( 'Itemid' );  // this returns the menu id number so you can reference parameters
$menu =   JSite::getMenu();
if ($menuitemid) {
   $menuparams = $menu->getParams( $menuitemid );
   $enabled = $menuparams->get('enabled');
   $type = $menuparams->get('type');
   $label = $menuparams->get('label');
   $processAlert = $menuparams->get('processAlert');
}


//$enabled = $this->params->get('enabled');
//$type = $this->params->get('type');
//$label = $this->params->get('label');
//$processAlert = $this->params->get('processAlert');


$listServices = BFCHelper::getServicesForSearchOnSell();

$locationZones = BFCHelper::getLocations();
$listlocationZones = array();
$listlocationZones[] = JHTML::_('select.option', 0, JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ALL'));
foreach ($locationZones as $lz) {
	$listlocationZones[] = JHTML::_('select.option', $lz->LocationID, $lz->Name);
}
	$listlocationZones[] = JHTML::_('select.option', -1, JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ZONE_SEARCHBYMAP'));

$unitCategories = BFCHelper::getUnitCategories();
$listunitCategories = array();
$listunitCategories[] = JHTML::_('select.option', -1, JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ALL_FEMMINILE'));
$isopenGroup=0;
foreach ($unitCategories as $uc) {
//	if (!empty($uc->CategoryParentID)){
	if (!empty($uc->ParentCategoryId)){
		$listunitCategories[] = JHTML::_('select.option', $uc->CategoryID, BFCHelper::getLanguage($uc->Name, $language) );
	}else{
		if ($isopenGroup==1){
			$listunitCategories[] = JHTML::_('select.optgroup','');
			$isopenGroup=0;
		}
		$listunitCategories[] = JHTML::_('select.optgroup', BFCHelper::getLanguage($uc->Name, $language) );
		$isopenGroup=1;
	}
}
		if ($isopenGroup==1){
			$listunitCategories[] = JHTML::_('select.option','</OPTGROUP>');
			$isopenGroup=0;
		}

$listcontractType = array(
	JHTML::_('select.option', '0', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_CONTRACTTYPE_SELL') ),
	JHTML::_('select.option', '1', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_CONTRACTTYPE_RENT') ),
);

$baths = array(
	JHTML::_('select.option', '|', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ),
	JHTML::_('select.option', '1|1', JText::_('1') ),
	JHTML::_('select.option', '2|2', JText::_('2') ),
	JHTML::_('select.option', '3|3', JText::_('3') ),
	JHTML::_('select.option', '3|', JText::_('>3') )
);

?>
<div id="bfcOnsellunitslist">
<form id="mailalertformonsellunit">
<div class="mailalertbody">
<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_SUBSCRIBE_BODY') ?>
</div>
	<div id="divMailAlert" class="mailalertform">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_CONTRACTTYPE'); ?></label>
				<?php echo JHTML::_('select.genericlist', $listcontractType, 'contractTypeId', array(), 'value', 'text', $contractTypeId);?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ZONE'); ?></label>
				<?php echo JHTML::_('select.genericlist', $listlocationZones, 'zoneId', array(), 'value', 'text', $zoneId);?>
				<?php if(!empty($XGooglePos) && !empty($YGooglePos)) :?>	  
					<a class="lightboxlink" onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)">
						<img src="<?php echo JURI::root();?>images/global-search-icon.jpg" width="25" />
					</a>
				<?php endif; ?>
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ACCOMODATION'); ?></label>
				<?php echo JHTML::_('select.genericlist', $listunitCategories, 'unitCategoryId', array(), 'value', 'text', $categoryId);?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_PRICE') ?></label>
				<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_FROM') ?>" value="<?php echo $pricemin;?>" class="input-small" > 
				<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>" class="input-small" > 
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_AREA') ?></label>
				<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" class="input-small"  >
				<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_TO') ?>" value="<?php echo $areamax;?>" class="input-small"  >
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ROOMS') ?></label>
				<input name="roomsmin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_FROM') ?>" value="<?php echo $roomsmin;?>" class="input-small"  >
				<input name="roomsmax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_TO') ?>" value="<?php echo $roomsmax;?>" class="input-small"  >
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_BATHS') ?></label>
				<?php echo JHTML::_('select.genericlist', $baths, 'baths', array('onchange'=>'changeBaths();' ) , 'value', 'text', ($bathsmin ."|". $bathsmax));?>
				<input name="bathsmin" type="hidden" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_FROM') ?>" value="<?php echo $bathsmin;?>" class="input-small"  >
				<input name="bathsmax" type="hidden" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_TO') ?>" value="<?php echo $bathsmax;?>" class="input-small"  >
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_BEDROOMS') ?></label>
				<input name="bedroomsmin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_FROM') ?>" value="<?php echo $roomsmin;?>" class="input-small"  >
				<input name="bedroomsmax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE_TO') ?>" value="<?php echo $roomsmax;?>" class="input-small"  >
			</div><!--/span-->
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   

			<?php if (isset($listServices)) :?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<!-- <label><?php echo JText::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_SERVICES') ?></label> -->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
					<?php foreach ($listServices as $singleService):?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
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
			<?php endif; ?>

			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<div>
					<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_EMAIL') ?>*</label>
					<input name="Email" type="text" placeholder="email" value="<?php echo $email;?>" >    
				</div>
	         	<input  type="checkbox" value="true" name="confirmprivacy" id="confirmprivacy" checked="checked" />
				<label for="confirmprivacytrue"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_CONFIRMPRIVACY') ?></label>
			</div>
		</div>

			<div class="pull-right" id="divBtndatacustomer">
				&nbsp;<br />
				<a  id="aBtndatacustomer" class="btn" href="javascript: void(0);"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ENABLE'); ?></a>
			</div>
		</div>
		<div id="sendalert" class="alert alert-success" style="display:none;margin-top:5px;"></div>

		<div style="display:none;">
			<!-- sospesi -->
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME') ?>*</label>
				<input name="Name" type="text" placeholder="" value="<?php echo $firstName;?>" >    
			</div>
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME') ?>*</label>
				<input name="Surname" type="text" placeholder=""  value="<?php echo $lastName;?>" >    
			</div>
			<div>
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_PHONE') ?>*</label>
				<input name="phone" type="text" placeholder="" value="<?php echo $phone;?>" >   
			</div>
		</div>
	<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
	<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
	<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell" />
	<input type="hidden" value="<?php echo $processAlert ?>" name="processAlert" id="processAlert" />
	<input type="hidden" value="<?php echo $enabled ?>" name="enabled" id="enabled" />
	<input type="hidden" value="<?php echo $label ?>" name="label" id="label" />
</form>
<script type="text/javascript">
<!--

var urlAlert = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&task=sendAlertOnSell') ?>?";	
var msgAlertOk = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_SENDOK'); ?>";
var msgAlertKo = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_SENDKO'); ?>";

jQuery(function($)
		{

			$('#aBtndatacustomer').click(function(e) {
				e.preventDefault();
				$("#mailalertformonsellunit").submit(); 
			});

			jQuery('.checkboxservices').on('click',function() {
				updateHiddenValue('.checkboxservices:checked','#servicesonsell')	
			});

			$("#mailalertformonsellunit").validate(
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
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmax:
		            {
		                required: false,
		                digits: true
		            },		        	Name: "required",
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
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	pricemax: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	areamin: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	areamax: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	roomsmin: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	roomsmax: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	bedroomsmin: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},
		        	bedroomsmax: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_DIGIT') ?>"
		        		},		        	Name: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME_ERROR') ?>",
		        	Surname: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME_ERROR') ?>",
		        	phone: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ERROR_REQUIRED') ?>",
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
//								setTimeout(function() {
//									divAlert.fadeOut('slow');
//								}, 4000); 
						 }
					 });
					return false;

				}

			});
		});


	//-->
	</script>



</form>
</div>
