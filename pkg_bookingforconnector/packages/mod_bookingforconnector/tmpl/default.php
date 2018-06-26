<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document     = JFactory::getDocument();
$language = JFactory::getLanguage()->getTag();
if ($merchant==null) return;

$app = JFactory::getApplication();
$sitename = $app->get('sitename');

$addressData = $merchant->AddressData;
$contacts = $merchant->ContactData;
$layout = BFCHelper::getString('layout','default');

$XGooglePos = null;
$YGooglePos = null;

if ((($merchant->XGooglePos != null && $merchant->XGooglePos != '') && ($merchant->YGooglePos != null && $merchant->YGooglePos != ''))) {
	$XGooglePos = $merchant->XGooglePos;
	$YGooglePos = $merchant->YGooglePos;
}

$merchantSiteUrl = '';
if (isset($merchant->SiteUrl) && $merchant->SiteUrl != '') {
	$merchantSiteUrl =$merchant->SiteUrl;
	if (strpos('http://', $merchantSiteUrl) == false) {
		$merchantSiteUrl = 'http://' . $merchantSiteUrl;
	}
}

$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";
$phone = "";
$fax ="";

	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
		if (!empty($merchant->SiteUrl)) {
			$merchantSiteUrl =$merchant->SiteUrl;
			if (strpos('http://', $merchantSiteUrl) == false) {
				$merchantSiteUrl = 'http://' . $merchantSiteUrl;
			}
		}
		$phone = $merchant->Phone;
		$fax = $merchant->Fax;

	}else{
		$addressData = $merchant->AddressData;
//		$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
//		$cap = BFCHelper::getItem($addressData, 'cap');
//		$comune =  BFCHelper::getItem($addressData, 'comune');
//		$provincia = BFCHelper::getItem($addressData, 'provincia');
		$indirizzo = $addressData->Address;
		$cap = $addressData->ZipCode;
		$comune = $addressData->CityName;
		$provincia = $addressData->RegionName;
		if (!empty($addressData->SiteUrl)) {
			$merchantSiteUrl =$addressData->SiteUrl;
			if (strpos('http://', $merchantSiteUrl) == false) {
				$merchantSiteUrl = 'http://' . $merchantSiteUrl;
			}
		}
		$phone = $addressData->Phone;
		$fax = $addressData->Fax;

	}


$MerchantType = $merchant->MerchantTypeId;

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchantResources = $uriMerchant .'&layout=resources&limitstart=0';
$uriMerchantRateplanslist = $uriMerchant .'&layout=rateplanslist';
$uriMerchantOffers = $uriMerchant .'&layout=offers&limitstart=0';
$uriMerchantPackages = $uriMerchant .'&layout=packages&limitstart=0';
$uriMerchantOnsellunits = $uriMerchant .'&layout=onsellunits&limitstart=0';
$uriMerchantRatings = $uriMerchant .'&layout=ratings';
$uriMerchantContacts = $uriMerchant .'&layout=contacts';
$uriMerchantRedirect = $uriMerchant .'&layout=redirect&tmpl=component';



$route = JRoute::_($uriMerchant);


$merchantLogo = JURI::root() . 'components/com_bookingforconnector/assets/images/defaults/default-s3.jpeg';
if (!empty($merchant->LogoUrl)){
	$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
}
$hasSuperior = !empty($merchant->RatingSubValue);
$rating = (int)$merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
	$hasSuperior = ($MerchantDetail->Rating%10)>0;
} 

?>
<div class="bfi-modbookingforconnector <?php echo $moduleclass_sfx ?> ">
<div class="bfi-mod-bookingforconnector-inner">
<?php if ($isportal){?>
	<div class="bfi-mod-bookingforconnector-vcard-wrapper">
		<div class="bfi-vcard-logo"><a href="<?php echo $route?>"><img src="<?php echo $merchantLogo?>" /></a></div>	
		<div class="bfi-vcard-name bfi-text-center">
			<a href="<?php echo $route?>"><?php echo  $merchant->Name?></a>
			<div class="bfi-item-rating">
			<?php for($i = 0; $i < $rating ; $i++) { ?>
			  <i class="fa fa-star"></i>
			<?php } ?>
			<?php if ($hasSuperior) { ?>
				&nbsp;S
			<?php } ?>
			</div>
		</div>
		<div class="bfi-merchant-simple bfi-text-center">
			<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo $provincia ?>)</span><br />
			<span class="tel"><a  href="javascript:void(0);" onclick="bookingfor.getData(bfi_variable.bfi_urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + bfi_variable.bfi_cultureCode,this,'<?php echo  addslashes($merchant->Name) ?>','PhoneView')"  id="phone<?php echo $merchant->MerchantId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span> - <?php if ($merchantSiteUrl != ''):?>
				<span class="website"><?php echo JHTML::link(JRoute::_($uriMerchantRedirect,true,-1), JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_SITEGO'),'target="_blank"') ?></span>
			<?php endif;?>
		</div>
	</div>
	<ul class="bfi-merchant-menu">
		<?php if ($merchant->HasResources){?>
				<li class="bfi-merchant-menu-item <?php echo ($layout == 'resources' || $layout == 'resource' || $currentView=='resource' ) ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantResources), ' '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_RESOURCES')) ?></li>
		<?php }?>
		<?php if ($merchant->HasOnSellUnits){?>
				<li class="bfi-merchant-menu-item <?php echo ($layout == 'onsellunits' || $layout == 'onsellunit') ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantOnsellunits),  ' '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_ONSELL')) ?></li>
		<?php } ?>	
		<?php if ($merchant->HasResources){?>
				<li class="bfi-merchant-menu-item <?php echo ($layout == 'offers' || $layout == 'offer') ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantOffers),  ' '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_OFFERS')) ?></li>
		<?php }?>
		<?php if ($merchant->RatingsContext !== 0) {?>
			<li class="bfi-merchant-menu-item <?php echo ($layout == 'ratings' || $layout == 'rating') ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantRatings),  ' '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_RATINGS')) ?></li>
		<?php } ?>	
	</ul>
<?php }?>
		<?php 
			$formdisplay = "none";
			if ($layout  == 'default' && $currentView=='merchantdetails') {
			$formdisplay = "";
			?>
		<?php 
			}else{
		?>
		<span href="" class="bfi-opencontactform bfi-btn bfi-alternative"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_OPENFORM') ?></span>
		<?php } ?>	
	<div class="bfi-contacts" style="display:<?php echo $formdisplay ?>">
	<?php 
$resourceType = strtolower($currentView);
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

$formRoute = "index.php?option=com_bookingforconnector&task=".$task ;

$formRoute = "index.php?option=com_bookingforconnector&task=".$task; 
$routeThanks = JRoute::_($uriMerchant .'&layout=thanks');
$routeThanksKo = JRoute::_($uriMerchant .'&layout=errors');

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

	$checkin = !empty($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
	$checkout = !empty($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');

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
              <textarea name="form[note]" style="height:200px;" class="bfi-col-md-12" placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?>"></textarea>    
            </div>
        </div>
		<div class="bfi-row">
			<div class=" bfi-col-md-12 bfi-checkbox-wrapper">
				<input name="form[optinemail]" id="optinemail" type="checkbox">
				<label for="optinemail"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_OPTINEMAIL'),$sitename) ?></label>
			</div>
        </div>
<br />
			
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
			<input type="hidden" id="orderType" name="form[orderType]" value="<?php echo $orderType ?>" />
			<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language;?>" />
			<input type="hidden" id="Fax" name="form[Fax]" value="" />
			<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
			<input type="hidden" id="label" name="form[label]" value="" />
			<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
			<input type="hidden" id="redirecterror" name="Redirecterror" value="<?php echo $routeThanksKo;?>" />
			<input type="hidden" id="formCulture" name="form[Culture]" value="<?php echo $language;?>" />
			<div class="bfi-footer-book" >
				<?php echo $infoSendBtn ?>
			</div>
			<div class=""><button type="submit" class="bfi-btn" style="width: 100%;" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button></div>

	</div>
</form>

<script type="text/javascript">
<!--
jQuery(function($){
		jQuery(".opencontactform").click(function(e) {
			jQuery(this).hide();
			jQuery(".mod_bookingforconnector-contacts").slideDown("slow",function() {
				if (jQuery.prototype.masonry){
					jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
				}
			});
		});
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
//					bookingfor.waitBlockUI();
//						jQuery.blockUI({message: ''});
					if ($form.data('submitted') === true) {
						 return false;
					} else {
						// Mark it so that the next submit can be ignored
						$form.data('submitted', true);
						form.submit();
					}
				}

//				form.submit();
			}

		});
	});
//-->
</script>
	</div>
</div>	<!-- inner -->
</div>	<!-- module -->
