<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$document 	= JFactory::getDocument();
$language 	= JFactory::getLanguage()->getTag();
$app = JFactory::getApplication();
$sitename = $app->get('sitename');

//$db   = JFactory::getDBO();
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
////$itemIdMerchant = intval($db->loadResult());
//
//$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
//
//if ($itemIdMerchant<>0)
//	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchant  = COM_BOOKINGFORCONNECTOR_URIMERCHANTDETAILS;
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$uriMerchant .='&layout=contacts';
$route = JRoute::_($uriMerchant);
$routeThanks = JRoute::_($uriMerchant .'&layout=thanks');
$routeThanksKo = JRoute::_($uriMerchant .'&layout=errors');

$routePrivacy = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PRIVACYURL);
$routeTermsofuse = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL);

$infoSendBtn = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_INFO_SENDBTN'),$sitename,$routePrivacy,$routeTermsofuse);

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));

$cultureCode = strtolower(substr($language, 0, 2));
$nationCode = strlen($language) == 5 ? strtolower(substr($language, 3, 2)) : $cultureCode;
$keys = array_keys($cNationList);
$nations = array_values(array_filter($keys, function($item) use($nationCode) {
	return strtolower($item) == $nationCode; 
	}
));
$nation = !empty(count($nations)) ? strtoupper($nations[0]) : strtoupper($cultureCode);

$culture="";

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');
$checkout->modify('+1 day');

$formRoute = "index.php?option=com_bookingforconnector&task=sendContact"; 

//$privacy = BFCHelper::GetPrivacy($language);
//$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
$idform = uniqid("merchantdetailscontacts");

?>
<!-- {emailcloak=off} -->
	<form method="post" id="<?php echo $idform ?>" class="form-validate merchantdetailscontacts" action="<?php echo $formRoute; ?>" novalidate="novalidate">
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *</label>
				<input type="text" value="" name="form[Name]" id="Name" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *</label>
				<input type="text" value="" name="form[Surname]" id="Surname" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
		</div>
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *</label>
				<input type="email" value="" name="form[Email]" id="Email"  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *</label>
				<input type="text" value="" name="form[Phone]" id="Phone"  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>" maxlength="255" required class="full-width"> 
			</div><!--/span-->
		</div>
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?> </label>
				<input type="text" value="" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?> </label>
				<input type="text" value="" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
		</div>
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?> </label>
				<input type="text" value="" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?> </label>
				<input type="text" value="" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>" maxlength="255" class="full-width">
			</div><!--/span-->
		</div>
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NATION'); ?> </label>
				<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','class="full-width"','value', 'text', $nation) ?>
			</div><!--/span-->
		</div>
		<div class="bfi-row">   
			<div class="bfi-col-md-4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN'); ?> </label>
						<input type="text" name="form[CheckIn]" id="<?php echo $checkinId ?>" value="<?php echo $checkin->format('d/m/Y') ?>" class="ui-datepicker-simple" />	
			</div><!--/span-->
			<div class="bfi-col-md-4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKOUT'); ?> </label>
						<input type="text" name="form[CheckOut]" id="<?php echo $checkoutId ?>" value="<?php echo $checkout->format('d/m/Y') ?>" class="ui-datepicker-simple" />	
			</div><!--/span-->
			<div class="bfi-col-md-4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TOTPERSONS'); ?> </label>
					<?php echo JHTML::_('select.integerlist',0,10,1, 'form[Totpersons]', null, 2)?>
			</div><!--/span-->
		</div>

		<div class="bfi-row">
            <div class="bfi-col-md-12" style="padding:0;">
              <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?></label>
              <textarea name="form[note]" style="height:200px;" class=""  data-rule-nourl="true"  data-msg-nourl="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOURL_ERROR') ?>" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?>"></textarea>    
            </div>
        </div>
			
		<div class=" bfi-checkbox-wrapper">
			<input name="form[optinemail]" id="optinemailpop" type="checkbox">
			<label for="optinemailpop"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_OPTINEMAIL'),$sitename) ?></label>
		</div>

<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>

		<input type="hidden" id="actionform" name="actionform" value="formlabel" />
		<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
		<input type="hidden" id="orderType" name="form[orderType]" value="a" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language;?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="" />
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
		<input type="hidden" id="redirecterror" name="Redirecterror" value="<?php echo $routeThanksKo;?>" />
		<input type="hidden" id="formCulture" name="form[Culture]" value="<?php echo $language;?>" />
		<?php echo JHtml::_('form.token'); ?>

		<div class="bfi-row bfi-footer-book" >
			<div class="bfi-col-md-10">
			<?php echo $infoSendBtn ?>
			</div>
			<div class="bfi-col-md-2 bfi-footer-send"><button type="submit" class="bfi-btn"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button></div>
		</div>
</form>

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

		var <?php echo $checkinId?> = null;
		jQuery(function($) {
			<?php echo $checkinId?> = function() { $("#<?php echo $checkinId?>").datepicker({
				defaultDate: "+2d"
				,dateFormat: "dd/mm/yy"
				, numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
				, minDate: '+0d'
				, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); }
				, beforeShow: function(dateText, inst) { 
					jQuery(this).attr("disabled", true);
					jQuery(inst.dpDiv).addClass('bfi-calendar');
					jQuery('#ui-datepicker-div').attr('data-before',"");
					jQuery('#ui-datepicker-div').addClass("bfi-checkin");
					jQuery('#ui-datepicker-div').removeClass("bfi-checkout");
					setTimeout(function() {bfiCalendarCheck()}, 1);
					}
				, onSelect: function(date) { checkDate<?php echo $checkinId?>(jQuery, jQuery(this), date); }
				, changeMonth: false
				, changeYear: false
			})};
			<?php echo $checkinId?>();
		});
		
		var <?php echo $checkoutId?> = null;
		jQuery(function($) {
			<?php echo $checkoutId?> = function() { $("#<?php echo $checkoutId?>").datepicker({
				defaultDate: "+2d"
				,dateFormat: "dd/mm/yy"
				, numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
				, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); }
				, beforeShow: function(dateText, inst) { 
					jQuery(this).attr("disabled", true); 
					jQuery(inst.dpDiv).addClass('bfi-calendar');
					jQuery('#ui-datepicker-div').attr('data-before',"");
					jQuery('#ui-datepicker-div').removeClass("bfi-checkin");
					jQuery('#ui-datepicker-div').addClass("bfi-checkout");
					setTimeout(function() {bfiCalendarCheck()}, 1);
					}
				, minDate: '+1d'
				, changeMonth: false
				, changeYear: false
			})};
			<?php echo $checkoutId?>();
		jQuery(".merchantdetailscontacts .ui-datepicker-simple").click(function() {
			jQuery(".ui-datepicker-calendar td").click(function() {
				if (jQuery(this).hasClass('ui-state-disabled') == false) {
					jQuery(".merchantdetailscontacts .ui-datepicker-simple").each(function() {
						jQuery(this).addClass("activeclass");
					});
					jQuery(".merchantdetailscontacts .ui-datepicker-simple").removeClass("activeclass");
					jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + 5 + "px");
				}
			});
		})
		var bfi_wuiP_width= 400;
		var bfi_wuiP_height= 350;
		if(jQuery(window).width()<bfi_wuiP_width){
			bfi_wuiP_width = jQuery(window).width()*0.9;
		}
		if(jQuery(window).height()<bfi_wuiP_height){
			bfi_wuiP_height = jQuery(window).height()*0.9;
		}

		jQuery('.bfi-agreeprivacy').webuiPopover({
			title : jQuery("#mbfcPrivacyTitle").html(),
			content : bookingfor.nl2br(jQuery("#mbfcPrivacyText").val()),
			width:bfi_wuiP_width,
			height:bfi_wuiP_height,
			container: "body",
			placement:"top",
			style:'bfi-webuipopover'
		}); 
		jQuery('.agreeadditionalPurpose').webuiPopover({
			title : jQuery("#mbfcAdditionalPurposeTitle").html(),
			content :  bookingfor.nl2br(jQuery("#mbfcAdditionalPurposeText").val()),
			width:bfi_wuiP_width,
			height:bfi_wuiP_height,
			container: "body",
			placement:"top",
			style:'bfi-webuipopover'
		}); 
		jQuery( window ).resize(function() {
		  jQuery('.bfi-agreeprivacy').webuiPopover('hide');
		  jQuery('.agreeadditionalPurpose').webuiPopover('hide');
		});
		$("#<?php echo $idform ?>").validate(
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
				var $form = $(form);
				if($form.valid()){
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
					bookingfor.waitBlockUI();
//						jQuery.blockUI({message: ''});
					if ($form.data('submitted') === true) {
						 return false;
					} else {
						// Mark it so that the next submit can be ignored
						$form.data('submitted', true);
						form.submit();
					}
				}
			}
		});
	});
//-->
</script>
