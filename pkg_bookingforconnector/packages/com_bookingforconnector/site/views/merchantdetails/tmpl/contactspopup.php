<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchant = $this->item;
$sitename = $this->sitename;
$language = $this->language;
$base_url = JURI::root();

$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_CONTACTS_TITLE'),$merchant->Name,$sitename));
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $language));

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

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

$orderType = "a";
$task = "sendContact";

$resource_id = BFCHelper::getVar( 'resourceid' , 0 );
$resourceType = BFCHelper::getVar( 'resourceType' , 0 );
if(!empty($resource_id) && $resourceType == "resource"){
	$resource = BFCHelper::GetResourcesById($resource_id,$language);
	$currentView = 'resource';
	$orderType = "c";
	$task = "sendInforequest";
}
if(!empty($resource_id) && $resourceType =="onsellunit"){
	$resource = BFCHelper::GetResourcesOnSellById($resource_id,$language);
	$currentView = 'onsellunit';
	$orderType = "b";
	$task = "sendOnSellrequest";
}



$formRoute = "index.php?option=com_bookingforconnector&task=".$task; 
$routeThanks = JRoute::_($uriMerchant .'&layout=thanks&tmpl=component&format=raw');
$routeThanksKo = JRoute::_($uriMerchant .'&layout=errors&tmpl=component&format=raw');

$routePrivacy = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PRIVACYURL);
$routeTermsofuse = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL);

$infoSendBtn = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_INFO_SENDBTN'),$sitename,$routePrivacy,$routeTermsofuse);


//$privacy = BFCHelper::GetPrivacy($language);
//$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;

$minCapacityPaxes = 0;
$maxCapacityPaxes = 12;
$checkoutspan = '+1 day';
$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');
$paxes = 2;
$pars = BFCHelper::getSearchParamsSession();
if (!empty($pars)){

	$checkin = isset($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
	$checkout = isset($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');

	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['merchantCategoryId'])) {
		$merchantCategoryId = $pars['merchantCategoryId'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	if (empty($pars['checkout'])){
		$checkout->modify($checkoutspan); 
	}
}
$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$idform = uniqid("merchantdetailscontacts");
?>
<div align="center" class="bfi-form-contacts bfi-content">
	<form method="post" id="<?php echo $idform ?>" class="form-validate merchantdetailscontacts" action="<?php echo $formRoute; ?>" novalidate="novalidate">
		<div class="bfi-form-field">
				<div class="bfi_form-title"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_OPENFORM') ?></div>
				<?php if(isset($resource)) {?>		
					<div class="">
						<?php echo $resource->Name; ?>
					</div><!--/span-->
				<?php } ?>	

				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *" type="text" value="" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *" type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *" type="email" value="" size="50" name="form[Email]" id="Email" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *" type="text" value="" size="50" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?>" type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?>" type="text" value="" size="50" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?>" type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?>" type="text" value="" size="50" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>">
				</div>
				<div class="bfi_form_txt">
					<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','','value', 'text', $nation) ?>
				</div>
<?php if(isset($resource)) {?>		
				<div class="bfi-hide">
					<?php echo $resource->Name; ?>
				</div><!--/span-->
				<input type="hidden" id="resourceId" name="form[resourceId]" value="<?php echo $resource->ResourceId;?>" > 
<?php 
$minCapacityPaxes = $resource->MinCapacityPaxes;
$maxCapacityPaxes = $resource->MaxCapacityPaxes;
if(empty($maxCapacityPaxes)) {
	$maxCapacityPaxes = 10;
}

 ?>
<?php }  ?>
		<?php if ($merchant->HasResources && $layout !== 'onsellunits' && $layout !== 'onsellunit' && $resourceType !== 'onsellunit'){?>
				<div class="bfi-row">   
					<div class="bfi-col-md-6 bfi-inline-field-right">
						<div class="bfi-inline-field"><label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN'); ?> </label></div>
						<input type="text" name="form[CheckIn]" id="<?php echo $checkinId ?>" value="<?php echo $checkin->format('d/m/Y') ?>" class="ui-datepicker-simple" />	
					</div>
					<div class="bfi-col-md-6 bfi-inline-field-left">
						<div class="bfi-inline-field"><label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKOUT'); ?></label></div>
						<input type="text" name="form[CheckOut]" id="<?php echo $checkoutId ?>" value="<?php echo $checkout->format('d/m/Y') ?>" class="ui-datepicker-simple" />	
					</div>
				</div>
				<div class="bfi-inline-field-pers"><label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TOTPERSONS'); ?> </label></div>
				<div class="bfi_form_txt">
					<?php echo JHTML::_('select.integerlist',$minCapacityPaxes,$maxCapacityPaxes,1, 'form[Totpersons]', 'class="bfi_input_select"', 2)?>
				</div>
		<?php } ?>	
		<div class="bfi-row">
            <div class="bfi-col-md-12" style="padding:0;">
              <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?></label>
              <textarea name="form[note]" style="height:200px;" class="" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?>"></textarea>    
            </div>
        </div>
		<div class=" bfi-checkbox-wrapper">
			<input name="form[optinemail]" id="optinemailpop" type="checkbox">
			<label for="optinemailpop"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_OPTINEMAIL'),$sitename) ?></label>
		</div>
<br />

	<?php
	$idrecaptcha = uniqid("bfirecaptcha");

	JPluginHelper::importPlugin('captcha');
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger('onInit',$idrecaptcha);
	$recaptcha1 = $dispatcher->trigger('onDisplay', array(null, $idrecaptcha, 'class="bfi-recaptcha"'));
	echo (isset($recaptcha1[0])) ? $recaptcha1[0] : '';
	?>
	<div id="recaptcha-error-<?php echo $idrecaptcha ?>" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>

			<input type="hidden" name="actionform" value="formlabel" />
			<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
			<input type="hidden" name="form[orderType]" value="<?php echo $orderType ?>" />
			<input type="hidden" name="form[cultureCode]" value="<?php echo $language;?>" />
			<input type="hidden" name="form[Fax]" value="" />
			<input type="hidden" name="form[VatCode]" value="" />
			<input type="hidden" name="form[label]" value="" />
			<input type="hidden" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
			<input type="hidden" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
			<input type="hidden" name="form[Culture]" value="<?php echo $language;?>" />
			

		<div class="bfi-row bfi-footer-book" >
			<div class="bfi-col-md-10">
			<?php echo $infoSendBtn ?>
			</div>
			<div class="bfi-col-md-2 bfi_footer-send"><button type="submit" class="bfi-btn"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button></div>
		</div>
	</div>
	</form>
</div>

<script type="text/javascript">
<!--
jQuery(function($){
<?php if ($merchant->HasResources && $layout !== 'onsellunits' && $layout !== 'onsellunit' && $resourceType !== 'onsellunit'){?>
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
					setTimeout(function() {
						bfiCalendarCheck();
					}, 1);
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
					setTimeout(function() {
						bfiCalendarCheck();
					}, 1);
					}
				, minDate: '+1d'
				, changeMonth: false
				, changeYear: false
			})};
			<?php echo $checkoutId?>();
		});
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
		jQuery(".merchantdetailscontacts .ui-datepicker-simple").click(function() {
			jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + 5 + "px");
			jQuery(".merchantdetailscontacts .ui-datepicker-simple").each(function() {
				jQuery(this).removeClass("activeclass");
			});
			jQuery(this).addClass("activeclass");

		});
<?php } ?>	

		jQuery(".bfi-opencontactform").click(function(e) {
			jQuery(this).hide();
			jQuery(".bfi-contacts").slideDown("slow",function() {
				if (jQuery.prototype.masonry){
					jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
				}
			});
		});

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
				if (typeof grecaptcha === 'object') {
					var response = grecaptcha.getResponse(window.bfirecaptcha['<?php echo $idrecaptcha ?>']);
					//recaptcha failed validation
					if(response.length == 0) {
						$('#recaptcha-error-<?php echo $idrecaptcha ?>').show();
						return false;
					}
					//recaptcha passed validation
					else {
						$('#recaptcha1-error-<?php echo $idrecaptcha ?>').hide();
					}					 
				}
//				form.submit();
            // Use Ajax to submit form data
					$("#<?php echo $idform ?>").ajaxSubmit({
						beforeSubmit: function(arr, $form, options) {
							//jQuery.blockUI({message: ''});
							bookingfor.waitBlockUI();
							$("#<?php echo $idform ?>").html('<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SENDINGFORM')  ?>');
						},
						success:    function(result) {
							jQuery.unblockUI();
							$("#<?php echo $idform ?>").html(result);
							if (typeof(ga) !== 'undefined') {
								ga('send', 'event', 'Bookingfor – Request', 'FormPopup', 'Sent');
							}
						}
					}); 
			}

		});	
	});
//-->
</script>
