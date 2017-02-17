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

$resource = $this->item;
$merchant = $resource->Merchant;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchantthanks = $uriMerchant .'&layout=thanks&tmpl=component';

$uriMerchant .='&layout=contacts';
$route = JRoute::_($uriMerchant);
$routeThanks = JRoute::_($uriMerchantthanks);

$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";

if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	$cap = $resource->ZipCode;
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
}else{
	$addressData = $resource->AddressData;
	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
	$cap = BFCHelper::getItem($addressData, 'cap');
	$comune =  BFCHelper::getItem($addressData, 'comune');
	$provincia = BFCHelper::getItem($addressData, 'provincia');
}
if (empty($indirizzo) && empty($comune) ){

	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
		if (empty($indirizzo)){
			$indirizzo = $resource->MrcAddress;
			$cap = $resource->MrcZipCode;
			$comune = $resource->MrcCityName;
			$provincia = $resource->MrcRegionName;
		}
	}else{
		$addressData = $merchant->AddressData;
		$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
		$cap = BFCHelper::getItem($addressData, 'cap');
		$comune =  BFCHelper::getItem($addressData, 'comune');
		$provincia = BFCHelper::getItem($addressData, 'provincia');
	}
}

$formRoute = "index.php?option=com_bookingforconnector&task=sendInforequest"; 

$privacy = BFCHelper::GetPrivacy($this->language);

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$checkin = new DateTime();
$checkout = new DateTime();
$checkout->modify('+1 day');

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));

$cultureCode = strtolower(substr($this->language, 0, 2));
$nationCode = strlen($this->language) == 5 ? strtolower(substr($this->language, 3, 2)) : $cultureCode;
$keys = array_keys($cNationList);
$nations = array_values(array_filter($keys, function($item) use($nationCode) {
	return strtolower($item) == $nationCode; 
	}
));
$nation = !empty(count($nations)) ? strtoupper($nations[0]) : strtoupper($cultureCode);

?>
	<h2 class="com_bookingforconnector_merchant-name"><?php echo  $resourceName?> 
		<span class="com_bookingforconnector_resource-rating com_bookingforconnector_resource-rating<?php echo  $merchant->Rating ?>">
			<!-- <span class="com_bookingforconnector_resource-ratingText">Rating <?php echo  $merchant->Rating ?></span> -->
		</span>
	</h2>
	<div class="com_bookingforconnector_resource-address">
		<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong>
	</div>	
	<div class="clear"></div>
<form method="post" id="merchantdetailscontacts" class="form-validate" action="<?php echo $formRoute; ?>">
	<div id="divMailContacts" class="mailalertform">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *" type="text" value="" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *" type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *" type="email" value="" size="50" name="form[Email]" id="Email" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *" type="text" value="" size="50" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?>" type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?>" type="text" value="" size="50" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?>" type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?>" type="text" value="" size="50" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>" style="width:95%">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
						<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','','value', 'text', $nation) ?>
			</div><!--/span-->
		</div>
		<?php if ($merchant->HasResources):?>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>4">
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
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>4">
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
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>4">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_TOTPERSONS'); ?> </label>
					<?php echo JHTML::_('select.integerlist',0,10,1, 'form[Totpersons]', null, 2)?>
			</div><!--/span-->
		</div>
		<?php endif ?>	

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <br />
						<textarea placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?>" name="form[note]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" ></textarea>    
            </div>
        </div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <br />
						<label id="mbfcPrivacyTitle"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PRIVACY') ?></label>
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
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>

		<input type="hidden" id="actionform" name="actionform" value="formlabel" />
		<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" /> 
		<input type="hidden" id="orderType" name="form[orderType]" value="c" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $this->language;?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="" />
		<input type="hidden" id="resourceId" name="form[resourceId]" value="<?php echo $resource->ResourceId;?>" /> 
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
		<input type="hidden" id="formCulture" name="form[Culture]" value="<?php echo $this->language;?>" />

		<button type="submit" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button>
	</div>
<script type="text/javascript">
<!--
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


	//-->
	</script>	
</form>
