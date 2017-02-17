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

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$checkoutspan = '+1 day';
$checkin = new DateTime();
$checkout = new DateTime();
$paxes = 2;
$paxages = array();

$formRoute = "index.php?option=com_bookingforconnector&task=sendScalarRequest"; 

$privacy = BFCHelper::GetPrivacy($this->language);

$merchantCategoryId = 0;
$pars = BFCHelper::getSearchParamsSession();

if (!empty($pars)){

$checkin = $pars['checkin'] ?: new DateTime();
$checkout = $pars['checkout'] ?: new DateTime();

$paxes = $pars['paxes'] ?: 2;
$paxages = $pars['paxages'];
$merchantCategoryId = $pars['merchantCategoryId']?: 0;

if ($pars['checkout'] == null)
	$checkout->modify($checkoutspan); 
}
if ($checkin == $checkout){
	$checkout->modify($checkoutspan); 
}

$merchantCategories = BFCHelper::getMerchantCategoriesForRequest($this->language);

$listmerchantCategories = '';

foreach ($merchantCategories as $mc) {
			$listmerchantCategories .= '<span class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">';
			
			$checked = ($merchantCategoryId ==  $mc->MerchantCategoryId)? 'checked="$checked"' : '';
			$listmerchantCategories .= '<input type="checkbox" name="merchantcategory[' . $mc->MerchantCategoryId . ']" id="merchantcategory' . $mc->MerchantCategoryId . '" ' . $checked . ' />';
			$listmerchantCategories .= '<label for="merchantcategory' . $mc->MerchantCategoryId . '">' . $mc->Name . '</label><br/>';
			$listmerchantCategories .= '</span>';
}
$listTreatments = '';

foreach ($cTreatmentsList as $treatments=>$treatmentsValue) {
			$listTreatments .= '<span class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">';
			$listTreatments .= '<input type="checkbox" name="treatments[' . $treatmentsValue . ']" id="treatments' . $treatments . '"  />';
			$listTreatments .= '<label for="treatments' . $treatmentsValue . '">' . $treatments . '</label><br/>';
			$listTreatments .= '</span>';
}

$nad = 0;
if (empty($paxages)){
	$nad = 2;
}
$nch = 0;
$nse = 0;
$countPaxes = array_count_values($paxages);

$nchs = array_values(array_filter($paxages, function($age) {
	if ($age < (int)BFCHelper::$defaultAdultsAge)
		return true;
	return false;
}));
$nchAges = implode(",",$nchs);
foreach ($countPaxes as $key => $count) {
	if ($key >= BFCHelper::$defaultAdultsAge) {
		if ($key >= BFCHelper::$defaultSenioresAge) {
			$nse += $count;
		} else {
			$nad += $count;
		}
	} else {
		$nch += $count;
	}
}

$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();
$urisearch  = 'index.php?option=com_bookingforconnector&view=search';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $urisearch .'%' ) .' AND language='. $db->Quote($lang) .' AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0)
	$urisearch .='&Itemid='.$itemId;

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
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?> </label>
				<input type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?> </label>
				<input type="text" value="" size="20" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?> </label>
				<input type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?> </label>
				<input type="text" value="" size="20" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>">
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
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN'); ?> </label>
			<?php 
				echo htmlHelper::calendar(
					$checkin->format('m/d/Y'), 
					'form[CheckIn]', 
					$checkinId, 
					'm/d/Y' /*input*/, 
					'd/m/Y' /*output*/, 
					'dd/mm/yy', 
					array('class' => ''), 
					true, 
					array(
						'minDate' => '\'+0d\'',
						'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
						'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); }',
						'onSelect' => 'function(date) { checkDate'.$checkinId.'(jQuery, jQuery(this), date); }'
					)
				) ?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKOUT'); ?> </label>
					<?php 
						echo htmlHelper::calendar(
							$checkout->format('m/d/Y'), 
							'form[CheckOut]', 
							$checkoutId, 
							'm/d/Y' /*input*/, 
							'd/m/Y' /*output*/, 
							'dd/mm/yy', 
							array('class' => ''),
							true, 
							array(
								'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
								'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); }',
								'minDate' => '\'+1d\''
							)
						) ?>
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ADULTS'); ?> </label>
					<?php echo JHTML::_('select.integerlist',0,10,1, 'form[TotPersons]', 'id="TotPersons"', $nad)?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDREN'); ?> </label>
					<?php echo JHTML::_('select.integerlist',0,10,1, 'form[Totchildrens]', 'id="Totchildrens"', $nch)?>
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" id="divchildrenage" style="display:none;">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDRENAGE'); ?> </label>
				<input type="text" value="<?php echo $nchAges ?>" size="20" name="form[ChildrenAge]" id="ChildrenAge" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CATEGORIES'); ?> </label>
				<?php echo $listmerchantCategories ?>
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TREATMENTS'); ?> </label>
				<?php echo $listTreatments ?>
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
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
		<input type="hidden" id="orderType" name="form[orderType]" value="g" />
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
function checkDate<?php echo $checkinId?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate() + 1);
	$("#<?php echo $checkoutId?>").datepicker("option", "minDate", d);
}
			function checkChildrenAge() {
					var val = jQuery('#Totchildrens').val();
					if (val > 0) {
						jQuery('#divchildrenage').show();
					} else {
						jQuery('#divchildrenage').hide();
					}
				}

jQuery(function($)
		{
			$('#Totchildrens').change(function() {
				checkChildrenAge();
			});
			checkChildrenAge();
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
