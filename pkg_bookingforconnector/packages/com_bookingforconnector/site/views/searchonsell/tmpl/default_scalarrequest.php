<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
//$cLanguageList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_LANGUAGESLIST'));
$cTreatmentsList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TREATMENTSLIST'));

$cultureCode = strtolower(substr($this->language, 0, 2));
$nationCode = strlen($this->language) == 5 ? strtolower(substr($this->language, 3, 2)) : $cultureCode;
$keys = array_keys($cNationList);
$nations = array_values(array_filter($keys, function($item) use($nationCode) {
	return strtolower($item) == $nationCode; 
	}
));
$nation = !empty(count($nations)) ? strtoupper($nations[0]) : strtoupper($cultureCode);
$culture="";
$contractTypeId = 0;
$categoryId = 0;
$zoneId = 0;
$pricemax='';
$pricemin='';
$areamin='';
$areamax='';
$roomsmin='';
$roomsmax='';
$bathsmin='';
$bathsmax='';
$services='';


$formRoute = "index.php?option=com_bookingforconnector&task=sendRequestOnSell"; 

$privacy = BFCHelper::GetPrivacy($this->language);

$pars = BFCHelper::getSearchOnSellParamsSession();

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
	$services = $pars['services'];

}

$merchantCategories = BFCHelper::getMerchantCategoriesForRequest($this->language);

$listmerchantCategories = '';

if (!empty($services) ) {
	$filtersServices = explode(",", $services);
}

$listServices = BFCHelper::getServicesForSearchOnSell();

$locationZones = BFCHelper::getLocations();

$listlocationZones = array();
if(!empty( $locationZones )){
	foreach ($locationZones as $lz) {
		$listlocationZones[] = JHTML::_('select.option', $lz->CityId, $lz->Name);
	}
}

$unitCategories = $this->typologies;

$listunitCategories = array();

if(!empty( $unitCategories )){
	foreach ($unitCategories as $uc) {
		$listunitCategories[] = JHTML::_('select.option', $uc->MasterTypologyId, BFCHelper::getLanguage($uc->Name, $lang) );
	}
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

$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();
$urisearch  = 'index.php?option=com_bookingforconnector&view=searchonsell';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $urisearch .'%' ) .' AND language='. $db->Quote($lang) .' AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0){
	$urisearch .='&Itemid='.$itemId;
}

$urisearch .= '&layout=thanks';
$routeThanks = JRoute::_($urisearch);
?>
<form method="post" id="formScalarRequest" class="form-validate" action="<?php echo $formRoute; ?>">
	<div id="divMailContacts" class="mailalertform">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *</label>
				<input type="text" value="" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *</label>
				<input type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *</label>
				<input type="email" value="" size="50" name="form[Email]" id="Email" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *</label>
				<input type="text" value="" size="20" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NATION'); ?> </label>
				<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','class="'. COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL .'10"','value', 'text', $nation) ?>
			</div><!--/span-->
		</div>
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
				<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $pricemin;?>" class="input-small"> 
				<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $pricemax;?>" class="input-small" > 
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_AREA') ?></label>
				<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $areamin;?>" class="input-small">
				<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $areamax;?>" class="input-small"  >
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_ROOMS') ?></label>
				<input name="roomsmin" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $roomsmin;?>" class="input-small">
				<input name="roomsmax" type="text" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $roomsmax;?>" class="input-small"  >
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_BATHS') ?></label>
				<?php echo JHTML::_('select.genericlist', $baths, 'baths', array('onchange'=>'changeBaths();' ) , 'value', 'text', ($bathsmin ."|". $bathsmax));?>
				<input name="bathsmin" type="hidden" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $bathsmin;?>" class="input-small"  >
				<input name="bathsmax" type="hidden" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_MAILALERT_DEFAULTVALUE') ?>" value="<?php echo $bathsmax;?>" class="input-small"  >
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_MAXRESPONSE'); ?> </label>
					<?php echo JHTML::_('select.integerlist',20,60,20, 'form[Maxresponse]', 'id="Maxresponse"', 20)?>
			</div><!--/span-->
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <br />
              <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?></label>
              <textarea name="form[note]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" ></textarea>    
            </div>
        </div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <br />
              <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PRIVACY') ?></label>
              <textarea name="form[privacy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" readonly ><?php echo $privacy ?></textarea>    
            </div>
			<input name="form[accettazione]" class="checkbox" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR'); ?>">
			<label for="agree"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM') ?></label>
        </div>

		<input type="hidden" id="actionform" name="actionform" value="formlabel" />
		<input type="hidden" id="orderType" name="form[orderType]" value="h" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $this->language;?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="" />
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
		<input type="hidden" id="formCulture" name="form[Culture]" value="<?php echo $this->language;?>" />
		<p class="formRed"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SCALARREQUESTNOTES') ?></p>
<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>
		<button type="submit" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button>
		</div>
<script type="text/javascript">
<!--
function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

function changeBaths(){
	var bathsselect = jQuery("#formScalarRequest select[name='baths']").val();
	var vals = bathsselect.split("|"); 
	jQuery("#formScalarRequest input[name='bathsmin']").val(vals[0]);
	jQuery("#formScalarRequest input[name='bathsmax']").val(vals[1]);
}

jQuery(function($)
		{
			jQuery('.checkboxservices').live('click',function() {
				updateHiddenValue('.checkboxservices:checked','#servicesonsell')	
			});

			$("#formScalarRequest").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        //errorPlacement: function(error, element) { //just nothing, empty  },
				highlight: function(label) {
			    	//$(label).removeClass('error').addClass('error');
			    	//$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
//					$(label).remove();
					//label.hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {
					if (typeof grecaptcha === 'object') {
						var response = grecaptcha.getResponse();
						//recaptcha failed validation
						if(response.length == 0) {
							$('#recaptcha-error').show();
							return false;
						}
						//recaptcha passed validation
						else {
							$('#recaptcha-error').hide();
						}					 
					}
					 form.submit();
				}

			});
		});


	//-->
	</script>	
</form>
