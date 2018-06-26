<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchant = $this->item;
$language = $this->language;

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;

$sitename = $this->sitename;
$document = $this->document;
$document->setTitle($merchant->Name);
$document->setDescription( BFCHelper::getLanguage($merchant->Description, $language));

$user = JFactory::getUser();

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());

$uriMerchant.='&merchantId=' . $this->item->MerchantId . ':' . BFCHelper::getSlug($this->item->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant  = JRoute::_($uriMerchant);
$uriMerchantthanks = $uriMerchant .'&layout=thanks';
$uriMerchantthanksKo = $uriMerchant .'&layout=errors';

$routeThanks = JRoute::_($uriMerchantthanks);
$routeThanksKo = JRoute::_($uriMerchantthanksKo);

$routePrivacy = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PRIVACYURL);
$routeTermsofuse = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL);

$infoSendBtn = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_INFO_SENDBTN'),$sitename,$routePrivacy,$routeTermsofuse);


if ($merchant->RatingsContext !== 1 && $merchant->RatingsContext !== 3 ) {
//redirect almerchant senza possibilità di renensirla
		header ("Location: ". $routeMerchant); 
		$app = JFactory::getApplication();
		$app->close();
}

$name = $user->name;
$email = $user->email;
$ratingError=0;
$merchantId = $this->item->MerchantId;
$jdate  = new JDate('now'); // 3:20 PM, December 1st, 2012
$endjdate  = new JDate('now -1 year'); // 3:20 PM, December 1st, 2012

$listDateArray = array();
while ($jdate > $endjdate) {
	$listDateArray[$jdate->format('Ym01')] = $jdate->format('F Y');
	$jdate->modify('-1 month');
}

$selectdate = true;
$nation="";
$listfiltered="";
$checkin="";
$city="";

switch (strtolower($language)) {
	case "de-de":
			$nation="DE"; ;
		break;
	case "fr-fr":
			$nation="FR"; ;
		break;
	case "en-gb":
			$nation="EN"; ;
		break;
	case "el-gr":
			$nation="GR"; ;
		break;
	case "it-it":
			$nation="IT"; ;
		break;
	case "pl-pl":
			$nation="PL"; ;
		break;
	case "es-es":
			$nation="ES"; ;
		break;
	case "en-us":
			$nation="US"; ;
		break;
}

$list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
$aNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));

$nationsList = JHTML::_('select.genericlist',$aNationList, 'nation','class="bfi-col-md-12"','value', 'text', $nation);

// rimuovo il "Tutti"
unset($list[0]);
$genericlist = JHTML::_('select.genericlist', $list, 'typologyid',null, 'value', 'text', $listfiltered);

$formRoute = "index.php?option=com_bookingforconnector&task=sendRating"; 

//$privacy = BFCHelper::GetPrivacy($language);
//$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);
//
$listDate = JHTML::_('select.genericlist',$listDateArray, 'checkin','','value', 'text', $checkin);

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = (int)$merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
	$hasSuperior = ($MerchantDetail->Rating%10)>0;
} 
?>
<!-- {emailcloak=off} -->

<div class="bfi-content">
	<div class="bfi-title-name"><?php echo  $merchant->Name?> 
		<span class="bfi-item-rating">
			<?php for($i = 0; $i < $rating; $i++) { ?>
			<i class="fa fa-star"></i>
			<?php } ?>
			<?php if ($hasSuperior) { ?>
				&nbsp;S
			<?php } ?>
		</span>
	</div>

<div class="clear"></div>

<form action="<?php echo $formRoute; ?>" method="post" id="formRating" class="bfi-payment-form">
	<input type="hidden" id="merchantid" name="merchantid" value="<?php echo $merchantId ?>">
	<input type="hidden" id="cultureCode" name="cultureCode" value="<?php echo $language ?>">
	<input type="hidden" id="hashorder" name="hashorder" value="">
	<input type="hidden" id="resourceId" name="resourceId" value="">
	<input type="hidden" id="label" name="label" value="<?php echo $formlabel ?>" />
	<input type="hidden" id="redirect" name="Redirect" value="<?php echo $routeThanks;?>" />
	<input type="hidden" id="redirecterror" name="Redirecterror" value="<?php echo $routeThanksKo;?>" />
	
	<div class="bfi-form-field">
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NAME') ?></label>
				<input name="name" type="text" class="bfi-col-md-12" placeholder="" value="<?php echo $user->name ; ?>" >    
			</div><!--/span-->
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CITY') ?></label>
				<input name="city" type="text" class="bfi-col-md-12" placeholder="" value="" >   
			</div><!--/span-->
		</div><!--/row-->
		<div class="bfi-row">
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_TYPOLOGY') ?></label>
				<?php echo $genericlist; ?>
			</div><!--/span-->
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NATION') ?></label>
				<?php echo $nationsList; ?>
			</div><!--/span-->
		</div><!--/row-->                              
		<div class="bfi-row">
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_EMAIL') ?>*</label>
				<input name="email" id="email" type="text" class="bfi-col-md-12" placeholder="email" value="<?php echo $user->email; ?>" >    
			</div>
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_EMAILCONFIRM') ?>*</label>
				<input name="email2" id="email2" type="text" class="bfi-col-md-12" placeholder="email" value="<?php echo $user->email; ?>" >    
			</div><!--/span-->
		</div><!--/row-->
		<div class="bfi-row">
			<div class="bfi-col-md-6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CHECKINDATEFROM_LABEL') ?></label>
				<?php echo $listDate; ?>
			</div><!--/span-->
		</div><!--/row-->                              

		<br />
		<div class="bfi-row">
			<div class="bfi-col-md-4">
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE1') ?>:
					<input type="hidden" id="hfvalue1" name="hfvalue1" value="6">
					<span id="starscap1">6</span><br />
					<input title="1" type="radio" value="1" name="personale" class="bfi-starreview starswrapper1 required">
					<input title="2" type="radio" value="2" name="personale" class="bfi-starreview starswrapper1">
					<input title="3" type="radio" value="3" name="personale" class="bfi-starreview starswrapper1">
					<input title="4" type="radio" value="4" name="personale" class="bfi-starreview starswrapper1">
					<input title="5" type="radio" value="5" name="personale" class="bfi-starreview starswrapper1">
					<input title="6" type="radio" checked value="6" name="personale" class="bfi-starreview starswrapper1">
					<input title="7" type="radio" value="7" name="personale" class="bfi-starreview starswrapper1">
					<input title="8" type="radio" value="8" name="personale" class="bfi-starreview starswrapper1">
					<input title="9" type="radio" value="9" name="personale" class="bfi-starreview starswrapper1">
					<input title="10" type="radio" value="10" name="personale" class="bfi-starreview starswrapper1">
				<br />
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE2') ?>:
					<input type="hidden" id="hfvalue2" name="hfvalue2" value="6">
					<span id="starscap2">6</span><br />
					<input title="1" type="radio" value="1" name="servizi" class="bfi-starreview starswrapper2 required">
					<input title="2" type="radio" value="2" name="servizi" class="bfi-starreview starswrapper2">
					<input title="3" type="radio" value="3" name="servizi" class="bfi-starreview starswrapper2">
					<input title="4" type="radio" value="4" name="servizi" class="bfi-starreview starswrapper2">
					<input title="5" type="radio" value="5" name="servizi" class="bfi-starreview starswrapper2">
					<input title="6" type="radio" checked value="6" name="servizi" class="bfi-starreview starswrapper2">
					<input title="7" type="radio" value="7" name="servizi" class="bfi-starreview starswrapper2">
					<input title="8" type="radio" value="8" name="servizi" class="bfi-starreview starswrapper2">
					<input title="9" type="radio" value="9" name="servizi" class="bfi-starreview starswrapper2">
					<input title="10" type="radio" value="10" name="servizi" class="bfi-starreview starswrapper2">
				<br />
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE3') ?>:
					<input type="hidden" id="hfvalue3" name="hfvalue3" value="6">
					<span id="starscap3">6</span><br />
					<input title="1" type="radio" value="1" name="pulizia" class="bfi-starreview starswrapper3 required">
					<input title="2" type="radio" value="2" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="3" type="radio" value="3" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="4" type="radio" value="4" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="5" type="radio" value="5" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="6" type="radio" checked value="6" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="7" type="radio" value="7" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="8" type="radio" value="8" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="9" type="radio" value="9" name="pulizia" class="bfi-starreview starswrapper3">
					<input title="10" type="radio" value="10" name="pulizia" class="bfi-starreview starswrapper3">
			</div>
			<div class="bfi-col-md-4">
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE4') ?>:
					<input type="hidden" id="hfvalue4" name="hfvalue4" value="6">
					<span id="starscap4">6</span><br />
					<input title="1" type="radio" value="1" name="comfort" class="bfi-starreview starswrapper4 required">
					<input title="2" type="radio" value="2" name="comfort" class="bfi-starreview starswrapper4">
					<input title="3" type="radio" value="3" name="comfort" class="bfi-starreview starswrapper4">
					<input title="4" type="radio" value="4" name="comfort" class="bfi-starreview starswrapper4">
					<input title="5" type="radio" value="5" name="comfort" class="bfi-starreview starswrapper4">
					<input title="6" type="radio" checked value="6" name="comfort" class="bfi-starreview starswrapper4">
					<input title="7" type="radio" value="7" name="comfort" class="bfi-starreview starswrapper4">
					<input title="8" type="radio" value="8" name="comfort" class="bfi-starreview starswrapper4">
					<input title="9" type="radio" value="9" name="comfort" class="bfi-starreview starswrapper4">
					<input title="10" type="radio" value="10" name="comfort" class="bfi-starreview starswrapper4">
				<br />
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE5') ?>:
					<input type="hidden" id="hfvalue5" name="hfvalue5" value="6">
					<span id="starscap5">6</span><br />
					<input title="1" type="radio" value="1" name="rapporto" class="bfi-starreview starswrapper5 required">
					<input title="2" type="radio" value="2" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="3" type="radio" value="3" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="4" type="radio" value="4" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="5" type="radio" value="5" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="6" type="radio" checked value="6" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="7" type="radio" value="7" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="8" type="radio" value="8" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="9" type="radio" value="9" name="rapporto" class="bfi-starreview starswrapper5">
					<input title="10" type="radio" value="10" name="rapporto" class="bfi-starreview starswrapper5">
			</div>
			<div class="bfi-col-md-4 text-center">
				<div class="com_bookingforconnector_rating_valuation">
					<div ><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION') ?> </div>
					<div class="bfi-rating-value" id="totale">6</div>
					<input type="hidden" id="hftotale" name="hftotale" value="6">
				</div>
			</div>
		</div>

		<br />
		<div class="bfi-row">
			<div class="bfi-col-md-12">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_ADVANTAGES') ?></label>
				<textarea name="pregi" class="" style="height:200px;"></textarea>    
			</div>
		</div>
		<div class="bfi-row">
			<div class="bfi-col-md-12">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DEFECTS') ?></label>
				<textarea name="difetti" class="" style="height:200px;"></textarea>    
			</div>
		</div>

		<div class="bfi-row">
			<div class="bfi-col-md-12 bfi-checkbox-wrapper">
				<input name="form[optinemail]" id="optinemailpop" type="checkbox">
				<label for="optinemailpop"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_FORM_OPTINEMAIL'),$sitename) ?></label>
			</div>
		</div>

		<div class="bfi-row">
			<div class="bfi-col-md-12 bfi-checkbox-wrapper">
				<input type="checkbox" value="true" name="privacyrating" id="privacyrating" required="required">
				<label for="privacyrating"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_PRIVACY'),$sitename); ?></label>    
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

		<div class="bfi-row bfi-footer-book" >
			<div class="bfi-col-md-10">
			<?php echo $infoSendBtn ?>
			</div>
			<div class="bfi-col-md-2 bfi_footer-send"><button type="submit" class="bfi-btn"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button></div>
		</div>
	</div>
</form>
	<div class="bfi-clearboth"></div>
<?php
				BFCHelper::bfi_get_template('shared/merchant_small_details.php',array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant)); 
?>

</div>
<script type="text/javascript">

    	function setRating(field){
	     jQuery('.starswrapper'+field).rating({
		  split: 2,
		  focus: function(value, link){
		    jQuery('#starscap'+field).html(value || '');
		  },
		  blur: function(value, link){
		    jQuery('#starscap'+field).html(jQuery('#hfvalue'+field).val() || '');
		  },
		  callback: function(value, link){ 
		    jQuery('#starscap'+field).html(value || '');
			 jQuery('#hfvalue'+field).val(value)
			sommatoria();
		  }
		});
      }
	   
	   function sommatoria(){
	     var sommatotale = 0;
	     for (i=1;i<6 ;i++ ) {
		    sommatotale += parseInt(jQuery('#hfvalue'+i).val());
	     }
	     jQuery('#totale').html(Math.round( (sommatotale/5) *100 ) / 100);
	     jQuery('#hftotale').val(Math.round( (sommatotale/5) *100 ) / 100);
      }
      
      jQuery(document).ready(function () {
      	  for (i=1;i<6 ;i++ ) {
		    setRating(i);
	     }
		    jQuery("#formRating").validate(
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
		            email:
		            {
		                required: true,
		                email: true
		            },
					email2: {
						  equalTo: "#email"
					},
					privacyrating : "required"
		        },
		        messages:
		        {
		        	privacyrating: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_ERROR_REQUIRED') ?>",
		            email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED') ?>",
		            email2: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED') ?>"
		        },
				errorClass: "bfi-error",
		        highlight: function(label) {
			    	jQuery(label).closest('.control-group').removeClass('bfi-error').addClass('bfi-error');
			    },
			    success: function(label) {
			    	label
			    		.closest('.control-group').removeClass('bfi-error').addClass('success');
			    		//.text('ok!').addClass('valid')
			    },
			   submitHandler: function(form) {
					var $form = jQuery(form);
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
						
	//					jQuery('#formRating').ajaxSubmit({
	//						beforeSubmit: function(arr, $form, options) {
	//							jQuery('#msgKo').hide()
	//							$form.toggle();
	//						},
	//						success:   processJson
	//					}); 
//						 jQuery.blockUI();
						 bookingfor.waitBlockUI();
						 form.submit();
					}


			   }
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
	  });
</script>