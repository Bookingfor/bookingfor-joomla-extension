<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$merchants = BFCHelper::getMerchantsSearch('',0,1,null,null);
if(!empty($merchants)){
$merchant = array_values($merchants)[0]; 

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchantthanks = $uriMerchant .'&layout=thanks';

$uriMerchant .='&layout=contacts';
$route = JRoute::_($uriMerchant);
$routeThanks = JRoute::_($uriMerchantthanks);

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
//$cLanguageList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_LANGUAGESLIST'));
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

$checkin = new DateTime();
$checkout = new DateTime();
$checkout->modify('+1 day');

$formRoute = "index.php?option=com_bookingforconnector&task=sendContact"; 

$privacy = BFCHelper::GetPrivacy($this->language);

?>
<!-- {emailcloak=off} -->
<form method="post" id="merchantdetailscontacts" class="form-validate" action="<?php echo $formRoute; ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *</label>
				<input type="text" value="" name="form[Name]" id="Name" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *</label>
				<input type="text" value="" name="form[Surname]" id="Surname" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *</label>
				<input type="email" value="" name="form[Email]" id="Email"  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *</label>
				<input type="text" value="" name="form[Phone]" id="Phone"  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?> </label>
				<input type="text" value="" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?> </label>
				<input type="text" value="" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?> </label>
				<input type="text" value="" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?> </label>
				<input type="text" value="" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NATION'); ?> </label>
				<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','class="full-width"','value', 'text', $nation) ?>
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN'); ?> </label>
			<?php 
				echo htmlHelper::calendar(
					$checkin->format('m/d/Y'), 
					'form[CheckIn]', 
					$checkinId, 
					'm/d/Y' /*input*/, 
					'd/m/Y' /*output*/, 
					'dd/mm/yy', 
					array('class' => 'full-width'), 
					true, 
					array(
						'minDate' => '\'+0d\'',
						'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
						'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); }',
						'onSelect' => 'function(date) { checkDate'.$checkinId.'(jQuery, jQuery(this), date); }'
					)
				) ?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKOUT'); ?> </label>
					<?php 
						echo htmlHelper::calendar(
							$checkout->format('m/d/Y'), 
							'form[CheckOut]', 
							$checkoutId, 
							'm/d/Y' /*input*/, 
							'd/m/Y' /*output*/, 
							'dd/mm/yy', 
							array('class' => 'full-width'),
							true, 
							array(
								'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
								'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); }',
								'minDate' => '\'+1d\''
							)
						) ?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TOTPERSONS'); ?> </label>
					<?php echo JHTML::_('select.integerlist',0,10,1, 'form[Totpersons]', null, 2)?>
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
		<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
		<input type="hidden" id="orderType" name="form[orderType]" value="a" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $this->language;?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="" />
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
		<input type="hidden" id="formCulture" name="form[Culture]" value="<?php echo $this->language;?>" />
<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>

		<button type="submit" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button>
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

jQuery(function($)
		{
			$("#merchantdetailscontacts").validate(
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

	function showResponse(responseText, statusText, xhr, $form)  { 
	}
	function showError(responseText, statusText, xhr, $form)  { 
	}

	//-->
	</script>	
</form>
<?php 
	}
  ?>
