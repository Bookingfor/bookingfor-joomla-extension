<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document     = JFactory::getDocument();
$language = $document->getLanguage();
if ($merchant==null) return;
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


$uriMerchantthanks = $uriMerchant .'&layout=thanks';
$uriMerchantthanksKo = $uriMerchant .'&layout=errors';


$route = JRoute::_($uriMerchant);

$routeThanks = JRoute::_($uriMerchantthanks);
$routeThanksKo = JRoute::_($uriMerchantthanksKo);

$privacy = BFCHelper::GetPrivacy($language);

$merchantLogo = JURI::root() . 'components/com_bookingforconnector/assets/images/defaults/default-s3.jpeg';
if (!empty($merchant->LogoUrl)){
	$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
}


$checkoutspan = '+1 day';
$checkin = new JDate('now'); 
$startDate = new JDate('now'); 
$checkout = new JDate('now'); 
$paxes = 2;

$pars = BFCHelper::getSearchParamsSession();

if (!empty($pars)){

	if (!empty($pars['checkin'])){
		$checkin = new JDate($pars['checkin']->format('Y-m-d')); 
	}
	if (!empty($pars['checkout'])){
		$checkout = new JDate($pars['checkout']->format('Y-m-d')); 
	}
	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
}

if ($checkin < $startDate){
	$checkin = clone $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

if ($checkin == $checkout){
    $checkout->modify($checkoutspan); 
}

if ($currentView == 'resource' && $currentlayout == 'form' ){
		require JModuleHelper::getLayoutPath('mod_bookingforconnector', 'sidebarpaymentform');
}else{
?>
<div class="mod_bookingforconnector<?php echo $moduleclass_sfx ?> ">
<div class="mod_bookingforconnector-inner">
<?php if ($isportal):?>
	<div class="mod_bookingforconnector-vcard-wrapper">
		<div class="mod_bookingforconnector-vcard-logo"><a href="<?php echo $route?>"><img src="<?php echo $merchantLogo?>" /></a></div>	
		<div class="mod_bookingforconnector-vcard-name">
			<a href="<?php echo $route?>"><?php echo  $merchant->Name?></a>
			<div class="mod_bookingforconnector-vcard-rating">
			<?php for($i = 0; $i < $merchant->Rating ; $i++) { ?>
			  <i class="fa fa-star"></i>
			<?php } ?>
			</div>
		</div>
		<div class="merchant_info">
			<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo $provincia ?>)</span><br />
			<span class="tel"><a  href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes($merchant->Name) ?>','PhoneView')"  id="phone<?php echo $merchant->MerchantId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span> - <?php if ($merchantSiteUrl != ''):?>
				<span class="website"><?php echo JHTML::link(JRoute::_($uriMerchantRedirect,true,-1), JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_SITEGO'),'target="_blank"') ?></span>
			<?php endif;?>
		</div>
	</div>
	<ul class="mod_bookingforconnector-menu">
		<?php if ($merchant->HasResources):?>
				<li class="mod_bookingforconnector-menu-item <?php echo ($layout == 'resources' || $layout == 'resource' || $currentView=='resource' ) ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantResources), '<i class="fa fa-bed" aria-hidden="true"></i> '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_RESOURCES')) ?></li>
		<?php endif;?>
		<?php if ($merchant->HasOnSellUnits):?>
				<li class="mod_bookingforconnector-menu-item <?php echo ($layout == 'onsellunits' || $layout == 'onsellunit') ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantOnsellunits),  '<i class="fa fa-home" aria-hidden="true"></i> '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_ONSELL')) ?></li>
		<?php endif ?>	
		<?php if ($merchant->HasResources):?>
			<?php if ($merchant->HasOffers):?>
				<li class="mod_bookingforconnector-menu-item <?php echo ($layout == 'offers' || $layout == 'offer') ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantOffers),  '<i class="fa fa-percent" aria-hidden="true"></i> '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_OFFERS')) ?></li>
			<?php endif ?>
		<?php endif;?>
		<?php if ($merchant->RatingsContext !== 0) :?>
			<li class="mod_bookingforconnector-menu-item <?php echo ($layout == 'ratings' || $layout == 'rating') ? 'active' : '' ?>"><?php echo JHTML::link(JRoute::_($uriMerchantRatings),  '<i class="fa fa-comments-o" aria-hidden="true"></i> '.JTEXT::_('MOD_BOOKINGFORCONNECTOR_RATINGS')) ?></li>
		<?php endif ?>	
	</ul>
<?php endif;?>
		<?php 
			$formdisplay = "none";
			if ($layout  == 'default' && $currentView=='merchantdetails') {
			$formdisplay = "";
			?>
		<?php 
			}else{
		?>
		<span href="" class="opencontactform"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_OPENFORM') ?></span>
		<?php } ?>	







	<div class="mod_bookingforconnector-contacts" style="display:<?php echo $formdisplay ?>">
	<?php 

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
//$cLanguageList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_LANGUAGESLIST'));

$cultureCode = strtolower(substr($language, 0, 2));
$nationCode = strlen($language) == 5 ? strtolower(substr($language, 3, 2)) : $cultureCode;
$keys = array_keys($cNationList);
$nations = array_values(array_filter($keys, function($item) use($nationCode) {
	return strtolower($item) == $nationCode; 
	}
));
$nation = !empty(count($nations)) ? $nations[0] : $cultureCode;
$culture="";

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$formRoute = "index.php?option=com_bookingforconnector&task=".$task ;

$minCapacityPaxes = 0;
$maxCapacityPaxes = 12;


?>
		<form method="post" id="merchantdetailscontacts" class="form-validate" action="<?php echo $formRoute; ?>">
			<div class="mod_bookingforconnector_form-field">
			<div class="mod_bookingforconnector_form-title"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_FORM_TITLE');?></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *" type="text" value="" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *" type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *" type="email" value="" size="50" name="form[Email]" id="Email" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *" type="text" value="" size="20" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?>" type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?>" type="text" value="" size="20" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?>" type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?>" type="text" value="" size="20" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>">
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','','value', 'text', $nation) ?>
						<br />
					</div><!--/span-->
<?php if(isset($resource)) :?>		
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<?php echo $resource->Name; ?>
					</div><!--/span-->
				<input type="hidden" id="resourceId" name="form[resourceId]" value="<?php echo $resource->ResourceId;?>" > 
<?php 
$minCapacityPaxes = $resource->MinCapacityPaxes;
$maxCapacityPaxes = $resource->MaxCapacityPaxes;
 ?>
<?php endif;  ?>
		<?php if ($merchant->HasResources && $layout !== 'onsellunits' && $layout !== 'onsellunit' && $currentView !== 'onsellunit'):?>
						
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 inline-field-right">
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
							   'numberOfMonths' => 2,
								'minDate' => '\'+0d\'',
								'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
								'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); }',
								'onSelect' => 'function(date) { checkDate'.$checkinId.'(jQuery, jQuery(this), date); }',
								'changeMonth' => 'false',
								'changeYear' => 'false'
							)
						) ?>
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 inline-field-left">
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
									   'numberOfMonths' => 2,
										'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
										'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); }',
										'minDate' => '\'+1d\'',
										'changeMonth' => 'false',
										'changeYear' => 'false'
									)
								) ?>
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TOTPERSONS'); ?> </label>
							<?php echo JHTML::_('select.integerlist',$minCapacityPaxes,$maxCapacityPaxes,1, 'form[Totpersons]', null, 2)?>
					</div><!--/span-->
		<?php endif ?>	
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
						<br />
						<textarea placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?>" name="form[note]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" ></textarea>    
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;">
						<br />
						<label id="mbfcPrivacyTitle">
						<div class="pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('.agreeprivacy').popover('hide');"></i></div>
						<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PRIVACY') ?>
						</label>
						<textarea id="mbfcPrivacyText" name="form[privacy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" readonly ><?php echo $privacy ?></textarea>    
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
						<input name="form[accettazione]" class="checkbox" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR'); ?>">
						<label class="agreeprivacy"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM') ?></label>
					</div>
				</div>
<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptchabfconn');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptchabfconn', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error-bfconn" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>

				<input type="hidden" id="actionform" name="actionform" value="formlabel" />
				<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
				<input type="hidden" id="orderType" name="form[orderType]" value="<?php echo $orderType ?>" />
				<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language;?>" />
				<input type="hidden" id="Fax" name="form[Fax]" value="" />
				<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
				<input type="hidden" id="label" name="form[label]" value="" />
				<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
			</div>
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
		
		jQuery( window ).resize(function() {
		  jQuery('.agreeprivacy').popover('hide');

		});

		jQuery(function($){
			jQuery(".opencontactform").click(function(e) {
				jQuery(this).hide();
				jQuery(".mod_bookingforconnector-contacts").slideDown("slow",function() {
					if (jQuery.prototype.masonry){
						jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
					}
				});
			});

			jQuery('.agreeprivacy').popover({
				title : jQuery("#mbfcPrivacyTitle").html(),
				content : jQuery("#mbfcPrivacyText").val(),
				container: "body",
				placement:"top"
			}); 


			
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
								var response = grecaptcha.getResponse('recaptchabfconn');
								//recaptcha failed validation
								if(response.length == 0) {
									$('#recaptcha-error-bfconn').show();
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
	</div>
</div>	<!-- inner -->
</div>	<!-- module -->
<?php } ?>