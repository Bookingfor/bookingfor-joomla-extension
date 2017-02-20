<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document     = JFactory::getDocument();
$language     = $document->getLanguage();

$config = $this->config;
$isportal = $config->get('isportal', 1);
$usessl = $config->get('usessl', 0);
$ssllogo = $config->get('ssllogo','');
$formlabel = $config->get('formlabel','');
$sitename = $this->sitename;

$user = JFactory::getUser();

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = intval($db->loadResult());

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());


$resource = $this->item;

//echo "<pre>";
//echo print_r($resource);
//echo "</pre>";

$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$uriResource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
if ($itemId<>0)
	$uriResource .= '&Itemid='.$itemId;

$route = JRoute::_($uriResource);

$merchant = $resource->Merchant;
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchantthanks = $uriMerchant .'&layout=thanks';
$uriMerchantthanksKo = $uriMerchant .'&layout=errors';

//$uriMerchant .='&layout=contacts';
//$route = JRoute::_($uriMerchant);
$routeThanks = JRoute::_($uriMerchantthanks);
$routeThanksKo = JRoute::_($uriMerchantthanksKo);
$routeMerchant = JRoute::_($uriMerchant);
$routeback = JRoute::_($uriResource);
//if($isportal){
//	$routeback =  JURI::root();
//}

$merchantLogo = JURI::base() ."/media/com_bookingfor/images/default.png";
if ($merchant!= null && $merchant->LogoUrl != '') {
		$merchantLogo = BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl ,'logobig');
}

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
//$cLanguageList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_LANGUAGESLIST'));
//$cCCTypeList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCTYPELIST'));
$cCCTypeList = array();


$cultureCode = strtolower(substr($this->language, 0, 2));
$nationCode = strlen($this->language) == 5 ? strtolower(substr($this->language, 3, 2)) : $cultureCode;
$keys = array_keys($cNationList);
$nations = array_values(array_filter($keys, function($item) use($nationCode) {
	return strtolower($item) == $nationCode; 
	}
));
$nation = !empty(count($nations)) ? $nations[0] : $cultureCode;

$culture="";

$minyear = date("y");
$maxyear = $minyear+5;

$formRoute = "index.php?option=com_bookingforconnector&task=sendOrders"; 

$privacy = BFCHelper::GetPrivacy($this->language);
$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);
//$additionalPurpose = null;
$policy = BFCHelper::GetPolicy($resource->ResourceId ,$language);


$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$deposit = 0;
//$order = BFI()->currentOrder;
$order = BFCHelper::getState('currentOrder', 'bfi');
if(empty($order)){
	$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
	$order = BFCHelper::calculateOrder($OrderJson,$language);
//	BFI()->currentOrder = $order;
	BFCHelper::setState($order, 'currentOrder', 'bfi');
}

//model.SearchModel = SiteUtility.GetSearchModel();
$resourceid = intval(BFCHelper::getVar("hdnResourceId"));


//$bookingTypeFrpmForm = BFCHelper::getVar('bookingType');
//
//$bookingTypedefault=0;
//$bookingTypesValues = array();
//$bookingTypes = $this->MerchantBookingTypes;
$bookingTypes = BFCHelper::GetMerchantBookingTypeList($order->SearchModel, $resourceid, $language);
$bookingTypedefault ="";
//$bookingTypesDesc ="";
$bookingTypesoptions = array();
$bookingTypesValues = array();
$bookingTypeFrpmForm = isset($_REQUEST['bookingType'])?$_REQUEST['bookingType']:"";
//$totalWithVariation = $order->TotalDiscountedAmount;

//echo "<pre>";
//echo print_r($order);
//echo "</pre>";

if(!empty($bookingTypes)){
	$bookingTypesDescArray = array();
	foreach($bookingTypes as $bt)
	{
		$currDesc = BFCHelper::getLanguage($bt->Name, $language) . "<div class='ccdescr'>" . BFCHelper::getLanguage($bt->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) . "</div>";
		if($bt->AcquireCreditCardData && !empty($bt->Data)){

			$ccimgages = explode("|", $bt->Data);
			$cCCTypeList = array();
			$currDesc .= "<div class='ccimages'>";
			foreach($ccimgages as $ccimgage){
				$currDesc .= '<i class="fa fa-cc-' . strtolower($ccimgage) . '" title="'. $ccimgage .'"></i>&nbsp;&nbsp;';
				$cCCTypeList[$ccimgage] = $ccimgage; // JHTML::_('select.option', $ccimgage, $ccimgage);
			}
			$currDesc .= "</div>";
 		}
//		if($bt->AcquireCreditCardData==1 && !BFCHelper::isUnderHTTPS() ){
//			continue;
//		}

		$bookingTypesoptions[$bt->BookingTypeId.":".$bt->AcquireCreditCardData] =  $currDesc;//  JHTML::_('select.option', $bt->BookingTypeId.":".$bt->AcquireCreditCardData, $currDesc );
		$calculatedBookingType = $bt;
		$calculatedBookingType->Deposit = 0;
		
		if(isset($calculatedBookingType->Value) && !empty($calculatedBookingType->Value)) {
			
			$totalWithVariation = 0;
			foreach ($order->Resources as $data) {
				$orderBookingTypeId = $data->BookingType;
				if ($orderBookingTypeId == $bt->BookingTypeId) {
					$totalWithVariation += $data->TotalDiscounted;
				}
			}

			if($calculatedBookingType->Value!='0' && $calculatedBookingType->Value!='0%' && $calculatedBookingType->Value!='100%')
			{
				if (strpos($calculatedBookingType->Value,'%') !== false) {
					$calculatedBookingType->Deposit = (float)str_replace("%","",$calculatedBookingType->Value) *(float) $totalWithVariation/100;
				}else{
					$calculatedBookingType->Deposit = $calculatedBookingType->Value;
				}
			}
			if($calculatedBookingType->Value==='100%'){
				$calculatedBookingType->Deposit = $totalWithVariation;
			}
		}

		$bookingTypesValues[$bt->BookingTypeId] = $calculatedBookingType;

		if($bt->IsDefault == true ){
			$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
			$deposit = $calculatedBookingType->Deposit;
		}

//		$bookingTypesDescArray[] = BFCHelper::getLanguage($bt->Description, $language);;
	}
//	$bookingTypesDesc = implode("|",$bookingTypesDescArray);
	if(empty($bookingTypedefault)){
		$bt = array_values($bookingTypesValues)[0];
		$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
		$deposit = $bt->Deposit;
	}

	if(!empty($bookingTypeFrpmForm)){
			if (array_key_exists($bookingTypeFrpmForm, $bookingTypesValues)) {
				$bt = $bookingTypesValues[$bookingTypeFrpmForm];
				$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
				$deposit = $bt->Deposit;
			}
	}

}

$idrecaptcha = uniqid("bfirecaptcha");


?>
<?php if (!empty($order) && !empty($order->Resources)):?>

<!-- {emailcloak=off} -->
<div class="com_bookingforconnector_resource-payment-form">
<div class="bf-title-book"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_TITLEFORM') ?></div>
<form method="post" id="resourcedetailsrequest" class="form-validate" action="<?php echo $formRoute; ?>">
	<div class="mailalertform">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *</label>
				<input type="text" value="<?php echo $user->name ; ?>" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *</label>
				<input type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *</label>
				<input type="email" value="<?php echo $user->email; ?>" size="50" name="form[Email]" id="formemail" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_CONFIRM'); ?> *</label>
				<input type="email" value="<?php echo $user->email; ?>" size="50" name="form[EmailConfirm]" id="formemailconfirm" required equalTo="#formemail" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_CONFIRM_REQUIRED'); ?>">
			</div><!--/span-->
			
			
<!-- 			<div class=" inputaddressbutton"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESSBUTTON'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></div>-->			
			
			<div class="inputaddress" style="display:;">
				<div >
					<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?> </label>
					<input type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>">
				</div><!--/span-->
				<div >
					<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?> </label>
					<input type="text" value="" size="20" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>">
				</div><!--/span-->
				<div >
					<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NATION'); ?> </label>
					<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','class="bf_input_select width90percent"','value', 'text', $nation) ?>
				</div><!--/span-->
			</div>
	    </div>
	    <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<div >
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?></label>
				<textarea name="form[note]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:104px;" ></textarea>    
			</div>
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *</label>
				<input type="text" value="" size="20" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>" style="width:100%;">
			</div><!--/span-->
			<div >
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN_ETA_HOUR') ?></label>
				<select name="form[checkin_eta_hour]" class="bf_input_select" >
					<option value="N.D."><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN_ETA_HOUR_NO') ?></option>
					<option value="00.00 - 01.00">00:00 - 01:00</option>
					<option value="01.00 - 02.00">01:00 - 02:00</option>
					<option value="02.00 - 03.00">02:00 - 03:00</option>
					<option value="03.00 - 04.00">03:00 - 04:00</option>
					<option value="04.00 - 05.00">04:00 - 05:00</option>
					<option value="05.00 - 06.00">05:00 - 06:00</option>
					<option value="06.00 - 07.00">06:00 - 07:00</option>
					<option value="07.00 - 08.00">07:00 - 08:00</option>
					<option value="08.00 - 09.00">08:00 - 09:00</option>
					<option value="09.00 - 10.00">09:00 - 10:00</option>
					<option value="10.00 - 11.00">10:00 - 11:00</option>
					<option value="11.00 - 12.00">11:00 - 12:00</option>
					<option value="12.00 - 13.00">12:00 - 13:00</option>
					<option value="13.00 - 14.00">13:00 - 14:00</option>
					<option value="14.00 - 15.00">14:00 - 15:00</option>
					<option value="15.00 - 16.00">15:00 - 16:00</option>
					<option value="16.00 - 17.00">16:00 - 17:00</option>
					<option value="17.00 - 18.00">17:00 - 18:00</option>
					<option value="18.00 - 19.00">18:00 - 19:00</option>
					<option value="19.00 - 20.00">19:00 - 20:00</option>
					<option value="20.00 - 21.00">20:00 - 21:00</option>
					<option value="21.00 - 22.00">21:00 - 22:00</option>
					<option value="22.00 - 23.00">22:00 - 23:00</option>
					<option value="23.00 - 00.00">23:00 - 00:00</option>
					<!-- <option value="00:00 - 01:00 (del giorno dopo)">00:00 - 01:00 (del giorno dopo)</option>
					<option value="01:00 - 02:00 (del giorno dopo)">01:00 - 02:00 (del giorno dopo)</option> -->
				</select>
			</div><!--/span-->
			<div class=" hide">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PASSWORD'); ?> *</label>
				<input type="password" value="<?php echo $user->email; ?>" size="50" name="form[Password]" id="Password"   title="">
			</div><!--/span-->
			<div class="">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?> </label>
				<input type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?> </label>
				<input type="text" value="" size="20" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
	</div>

	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 paymentoptions" style="display:none;" id="bookingTypesContainer">
			<h2><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PAYMENTSYSTEM') ?></h2>
			<p><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PAYMENTSYSTEM_DESCR') ?></p>
			<?php  foreach ($bookingTypesoptions as $key => $value) { ?>
				<label for="form[bookingType]<?php echo $key ?>" id="form[bookingType]<?php echo $key ?>-lbl" class="radio">	
					<input type="radio" name="form[bookingType]" id="form[bookingType]<?php echo $key ?>" value="<?php echo $key ?>" <?php echo $bookingTypedefault == $key ? 'checked="checked"' : "";  ?>  ><?php echo $value ?><div class="ccdescr"></div>
				</label>
			<?php } ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 paymentoptions" id="bookingTypesDescriptionContainer">
			<h2 id="bookingTypeTitle"></h2>
			<span id="bookingTypeDesc"></span>
		</div>
		<!-- <div  style="display:none;" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" id="totaldepositrequested">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
			  <br />
			  <?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT') ?>: &euro; 
			  <span class="totaldeposit" id="totaldeposit"><?php echo $deposit ?></span> 
			</div>
		</div> -->
	</div>
		<div class="clear"></div>

<div style="display:none;" id="ccInformations" class="borderbottom">
		<h2><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCSUBTITLE') ?></h2>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCTYPE'); ?> </label>
				<?php echo JHTML::_('select.genericlist',$cCCTypeList, 'form[cc_circuito]','class=""','value', 'text', null) ?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNAME'); ?> </label>
				<input type="text" value="" size="50" name="form[cc_titolare]" id="cc_titolare" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNUMBER'); ?> </label>
				<input type="text" value="" size="50" maxlength="50" name="form[cc_numero]" id="cc_numero" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCVALIDATION'); ?></label>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYMONTH'); ?>
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 ccdateinput">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
							<input type="text" value="" size="2" maxlength="2" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5" name="form[cc_mese]" id="cc_mese" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
							<span class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 " style="text-align:center;" >/</span>
							<input type="text" value="" size="2" maxlength="2" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5" name="form[cc_anno]" id="cc_anno" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
						</div>
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYYEAR'); ?>
					</div><!--/span-->
				</div><!--/row-->
			</div>
		</div>
		<br />
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">   
			  <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
				 <?php echo $ssllogo ?>
			  </div>
		</div>

</div>	
<?php 
if($merchant->AcceptanceCheckIn != "-" && $merchant->AcceptanceCheckOut != "-" && !empty($merchant->OtherDetails) ){
?>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">   
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 infomerchant" >
			<h2><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_INFO_TITLE') ?></h2>
			<br />
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> borderbottom padding20px0">
				<?php if($merchant->AcceptanceCheckIn != "-"){ ?>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ARRIVO') ?></b>&nbsp;</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><?php echo $merchant->AcceptanceCheckIn ?></div>
				<?php } ?>
				<?php if($merchant->AcceptanceCheckOut != "-"){ ?>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PARTENZA') ?></b>&nbsp;</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><?php echo $merchant->AcceptanceCheckOut ?></div>
				<?php } ?>
			</div>
			<?php if(!empty($merchant->OtherDetails) ){ ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> borderbottom padding20px0">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_INFO') ?></b></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11 applyshorten" ><?php echo BFCHelper::getLanguage($merchant->OtherDetails, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'))  ?></div>
			</div>
				<?php } ?>
		</div>
		</div>
<?php 
}
?>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
				<input name="form[accettazionepolicy]" class="checkbox" id="agreepolicy" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR_POLICY'); ?>">
				<label class="shownextelement"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_POLICY') ?></label>
				<textarea name="form[policy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $policy ?></textarea>
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
					<input name="form[accettazione]" class="checkbox" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR'); ?>">
					<label class="shownextelement"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM') ?></label>
					<textarea name="form[privacy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $privacy ?></textarea>    
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="display:<?php echo empty($additionalPurpose)?"none":"";?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
				<input name="form[accettazioneadditionalPurpose]" class="checkbox" id="agreeadditionalPurpose" aria-invalid="true" aria-required="true" required type="checkbox" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR_ADDITIONALPURPOSE'); ?>">
				<label class="shownextelement"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRMADDITIONALPURPOSE') ?></label>
				<textarea name="form[additionalPurpose]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $additionalPurpose ?></textarea>    
			</div>
		</div>

		<input type="hidden" id="actionform" name="actionform" value="<?php echo $formlabel ?>" />
		<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
		<input type="hidden" id="orderType" name="form[orderType]" value="a" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language;?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="<?php echo $formlabel ?>" />
		<input type="hidden" id="resourceId" name="form[resourceId]" value="<?php echo $resource->ResourceId;?>" > 
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
		<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
		<input type="hidden" id="stayrequest" name="form[stayrequest]" value="<?php //echo $stayrequest;?>" />
		<input type="hidden" id="staysuggested" name="form[staysuggested]" value="<?php //echo $staysuggested;?>" />
		<input type="hidden" id="isgateway" name="form[isgateway]" value="0" />
		<input type="hidden" name="form[hdnOrderData]" id="hdnOrderData" value='[<?php echo json_encode($order) ?>]' />
		<input type="hidden" name="form[hdnOrderDataCart]" id="hdnOrderDataCart" value='<?php echo json_encode($order) ?>' />
		<input type="hidden" name="form[bookingtypeselected]" id="bookingtypeselected" value='<?php echo json_encode($order) ?>' />
<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>


		

		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> bf-footer-book" >
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10 " style="padding-top: 10px !important; padding-left: 20px !important;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FOOTERFORM') ?></div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 "><button type="submit" id="btnbfFormSubmit" style="display:none;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button></div>
		</div>

<?php
$selectedSystemType = array_values(array_filter($bookingTypesValues, function($bt) use($bookingTypedefault) {return $bt->BookingTypeId == $bookingTypedefault;}))
?>
<?php 
	$rdetailgrouped = array();
	foreach ($order->Resources as $data) {
		$id = $data->BookingType;
		if (isset($rdetailgrouped[$id])) {
			$rdetailgrouped[$id][] = $data;
		} else {
			$rdetailgrouped[$id] = array($data);
		}
	}
	foreach ($rdetailgrouped as $key=>$rdetailgroup) // foreach $rdetail
	{
		$tmpOrder = new stdClass;
		$tmpOrder->Resources = $rdetailgroup;
		$tmpOrder->SearchModel = $order->SearchModel;
?>
		<input type="hidden" id="tmpstaysuggested<?php echo $key ?>" value='[<?php echo json_encode($tmpOrder);?>]' />
<?php 

	}

?>

<script type="text/javascript">
<!--
var completeStay = <?php echo json_encode($order); ?>;
var selectedSystemType = "<?php echo $selectedSystemType[0]->PaymentSystemRefId; ?>";
jQuery(function($)
		{		
			var svcTotal = 0;
			var allItems = jQuery.makeArray(jQuery.map(jQuery.grep(completeStay.Resources, function(svc) {
				return svc.Tag == "ExtraServices";
			}), function(svc) {
				svcTotal += svc.TotalDiscounted;
				return {
					"id": "" + svc.PriceId + " - Service",
					"name": svc.Name,
					"category": "Services",
					"brand": "<?php echo $merchant->Name?>",
					"price": (svc.TotalDiscounted / svc.CalculatedQt).toFixed(2),
					"quantity": svc.CalculatedQt
				};
			}));
			/*
			jQuery.each(allItems, function(svc) {
				svcTotal += prc.TotalDiscounted;
			});
			*/
			allItems.push({
				"id": "<?php echo $resource->ResourceId?> - Resource",
				"name": "<?php echo $resource->Name?>",
				"category": "<?php echo $resource->MerchantCategoryName?>",
				"brand": "<?php echo $merchant->Name?>",
				"variant": completeStay.RefId ? completeStay.RefId.toUpperCase() : "",
				"price": completeStay.TotalDiscounted - svcTotal,
				"quantity": 1
			});
			
			<?php if($this->analyticsEnabled): ?>
			callAnalyticsEEc("addProduct", allItems, "checkout", "", {
				"step": 1
			});
			<?php endif;?>
			jQuery("#btnbfFormSubmit").show();

			$(".inputaddressbutton").click(function(){
//				$(".inputaddress").toggle();
				if(jQuery(".inputaddress").is(":visible")){
					jQuery(".inputaddress").slideUp("slow");
					jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
				  } else {
					jQuery(".inputaddress").slideDown("slow");
					jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
				  }
			});





			$(".shownextelement").click(function(){
				$(this).next().toggle();
			});
			
			<?php if(!empty($bookingTypesValues)) : ?>
			bookingTypesValues = <?php echo json_encode($bookingTypesValues) ?>;// don't use quotes
			<?php endif; ?>
			$("#resourcedetailsrequest").validate(
		    {
				rules: {
					"form[cc_mese]": {
					  required: true,
					  range: [1, 12]
					},
					"form[cc_anno]": {
					  required: true,
					  range: [<?php echo $minyear ?>, <?php echo $maxyear ?>]
					},
					"form[cc_numero]": {
					  required: true,
					  creditcard: true
					},
//					"form[ConfirmEmail]": {
//					  email: true,
//					  required: true,
//					  equalTo: "form[Email]"
//					},
				},
		        messages:
		        {
		        	"form[cc_mese]": "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYMONTH_ERROR') ?>",
		        	"form[cc_anno]": "<?php echo  sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYYEAR_ERROR'),$minyear,$maxyear) ?>",
		        	"form[cc_numero]": "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNUMBER_ERROR') ?>",
//		            email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_EMAIL_ERROR') ?>",
//		            "form[ConfirmEmail]": {
//								required: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED') ?>",
//								equalTo: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED') ?>"
//							}
		        },

				invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        //errorPlacement: function(error, element) { //just nothing, empty  },
//				errorPlacement: function(error, element) {
//					// Append error within linked label
//					$( element )
//						.closest( "form" )
//							.find( "label[for='" + element.attr( "id" ) + "']" )
//								.append( error );
//				},
//				errorElement: "span",
				highlight: function(label) {
			    	$(label).removeClass('error').addClass('error');
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
					$(label).remove();
//					$(label).hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {
					var $form = $(form);
					if (typeof grecaptcha === 'object') {
						var response = grecaptcha.getResponse(window.bfirecaptcha['<?php echo $idrecaptcha ?>']);
						//recaptcha failed validation
						if(response.length == 0) {
							$('#recaptcha-error-<?php echo $idrecaptcha ?>').show();
							return false;
						}
						//recaptcha passed validation
						else {
							$('#recaptcha-error-<?php echo $idrecaptcha ?>').hide();
						}					 
					}
					jQuery.blockUI({message: ''});
					if ($form.data('submitted') === true) {
						 return false;
					} else {
						// Mark it so that the next submit can be ignored
						$form.data('submitted', true);
						var svcTotal = 0;
						
						<?php if($this->analyticsEnabled): ?>
						callAnalyticsEEc("addProduct", allItems, "checkout", "", {
							"step": 2,
						});
						
						callAnalyticsEEc("addProduct", allItems, "checkout_option", "", {
							"step": 2,
							"option": selectedSystemType
						});
						<?php endif; ?>
						form.submit();
					}
				}

			});
			$("input[name='form[bookingType]']").change(function(){
				var currentSelected = $(this).val().split(':')[0];
				selectedSystemType = Object.keys(bookingTypesValues).indexOf(currentSelected) > -1 ? bookingTypesValues[currentSelected].PaymentSystemRefId : "";
				checkBT();
			});
			var bookingTypeVal= $("input[name='form[bookingType]']");
			var container = $('#bookingTypesContainer');
			if(bookingTypeVal.length>1 && container.length>0){
					container.show();
			}
			function checkBT(){
					var ccInfo = $('#ccInformations');
					if (ccInfo.length>0) {
						try
						{
							var currCC = $("input[name='form[bookingType]']:checked");
							if (!currCC.length) {
								currCC = $("input[name='BookingType']")[0];
								$(currCC).prop("checked", true);
							}
							var cc = $(currCC).val();
							var ccVal = cc.split(":");
							var reqCC = ccVal[1];
							if (reqCC) { 
								ccInfo.show();
							} else {
								ccInfo.hide();
							}
							var idBT = ccVal[0];

							$("#bookingtypeselected").val(idBT);

							$.each(bookingTypesValues, function(key, value) {
								if(idBT == value.BookingTypeId){
									$("#bookingTypeTitle").html(value.Name);
									$("#bookingTypeDesc").html(value.Description);
									if(value.Deposit!=null && value.Deposit!='0'){
										
										$("#totaldepositrequested"+idBT).show();
										$("#footer-deposit"+idBT).html(bookingfor.number_format(value.Deposit, 2, '.', ''));

//										$("#bf-summary-footer-deposit").show();
//										$("#footer-deposit").html(value.Deposit);

										$("#isgateway").val("0");
										if(value.DeferredPayment=='0' || value.DeferredPayment==false){
											$("#isgateway").val(value.IsGateway ? "1" : "0");
										}
										return false;
									}else{
										$("#isgateway").val("0");
										$("#totaldepositrequested").hide();
//										$("#bf-summary-footer-deposit").hide();
									}
								}
							});

							$(".bf-bBookingType").hide();
							$(".bf-summary-footer").hide();
							$(".bf-bBookingType"+idBT).show();
							$(".bf-summary-footer"+idBT).show();
							$("#hdnOrderData").val($("#tmpstaysuggested"+idBT).val());

							
						}
						catch (err)
						{
						}

					}
			}
			checkBT();
	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
	};
	jQuery(".applyshorten").shorten(shortenOption);

		});

	//-->
	</script>	
</form>

</div>

<?php else:?>		
	<div class="errorbooking" id="errorbooking">
		<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong><br />
		<a href="<?php echo $routeback ?>">back</a>
	</div>
<?php endif;?>