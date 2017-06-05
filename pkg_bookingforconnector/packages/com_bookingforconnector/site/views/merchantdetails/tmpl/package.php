<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));

$merchant = $this->item;

$db   = JFactory::getDBO();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchantthanks = $uriMerchant .'&layout=thanks';
$uriMerchantError = $uriMerchant .'&layout=errors';

$uriMerchant .='&layout=contacts';
$route = JRoute::_($uriMerchant);
$routeThanks = JRoute::_($uriMerchantthanks);
$routeThanksKo = JRoute::_($uriMerchantError);

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

$formRoute = "index.php?option=com_bookingforconnector&task=sendOffer"; 

$privacy = BFCHelper::GetPrivacy($this->language);
$currModID = uniqid('package');

?>
<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t<?php echo  $this->item->MerchantTypeId?>">
	<?php if ($this->items != null): 
		$offer = $this->items;
	?> 
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $offer->Name?> </h2>
	<div class="clear"></div>
	
	<ul class="nav nav-pills nav-justified bfcmenu ">
		<li role="presentation" class="active"><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_PHOTO') ?></a></li>
		<?php if (!empty($offer->Description)):?><li role="presentation" ><a rel=".com_bookingforconnector_resource-description" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<li role="presentation" class="book"><a rel="#divMailOffers" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li>
	</ul>
	
	<div class="resourcecontainer-gallery">
		<?php echo  $this->loadTemplate('gallery'); ?>
	</div>

	<?php if (!empty($offer->Description)):?>
	<div class="com_bookingforconnector_resource-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<h4 class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h4>
		<div class="com_bookingforconnector_resource-description-data <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
        <?php echo $offer->Description ?>		
		</div>
	</div>
	<?php endif; ?>
	<div class="clear"></div>
	<?php if ($offer->Value != null && $offer->Value > 0):?>
		<div class="com_bookingforconnector-merchantlist-merchant-price">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_PRICE')?>: &euro; <?php echo number_format($offer->Value,2, ',', '.')?>
		</div>
	<?php endif; ?>

<a name="divcalculator"></a>	
<form method="post" id="merchantdetailsoffers" class="form-validate highlight-form" action="<?php echo $formRoute; ?>">
	<div id="divMailOffers" class="mailalertform">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *" type="text" value="" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *" type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *" type="email" value="" size="50" name="form[Email]" id="Email" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *" type="text" value="" size="20" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?>" type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?>" type="text" value="" size="20" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?>" type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<input placeholder="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?>" type="text" value="" size="20" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NATION'); ?> </label>
				<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','class="bf_input_select "','value', 'text', $nation) ?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
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

		</div>
<?php 
$nad = 2;
$nch = 0;
$nchs = array(0,0,0,0,0,0,0);
$adults = array(
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') )
);

$children = array(
    JHTML::_('select.option', '0', JText::_('0') ),
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') )
);

$childrenAges = array(
    JHTML::_('select.option', '0', JText::_('0') ),
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') ),
    JHTML::_('select.option', '7', JText::_('7') ),  
    JHTML::_('select.option', '8', JText::_('8') ),
    JHTML::_('select.option', '9', JText::_('9') ),
    JHTML::_('select.option', '10', JText::_('10') ),  
    JHTML::_('select.option', '11', JText::_('11') ),  
    JHTML::_('select.option', '12', JText::_('12') ),  
    JHTML::_('select.option', '13', JText::_('13') ),  
    JHTML::_('select.option', '14', JText::_('14') ),  
    JHTML::_('select.option', '15', JText::_('15') ),  
    JHTML::_('select.option', '16', JText::_('16') ),  
    JHTML::_('select.option', '17', JText::_('17') ),  
);

$seniores = array(
    JHTML::_('select.option', '0', JText::_('0') ),
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') )
);
?>			
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>6">
					<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ADULTS'); ?></span><br />
					<?php echo JHTML::_('select.genericlist', $adults, 'adults', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', $nad, 'adults'.$currModID);?>
				</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>6 mod_bookingforsearch-children">
					<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDREN'); ?></span><br />
					<?php echo JHTML::_('select.genericlist', $children, 'children', array('onchange'=>'quoteChanged'.$currModID.'();checkChildren'.$currModID.'();','class' => 'inputmini'), 'value', 'text', $nch, 'children'.$currModID);?>
					<div id="childrenages<?php echo $currModID ?>" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="display:none;">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
							<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGE'); ?></span><br />
							<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages1', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini inlineblock'), 'value', 'text', !empty($nchs[0]) ? $nchs[0] : 0, 'childages1'.$currModID) ;?>
							<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages2', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini inlineblock'), 'value', 'text', !empty($nchs[1]) ? $nchs[1] : 0, 'childages2'.$currModID) ;?>
							<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages3', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini inlineblock'), 'value', 'text', !empty($nchs[2]) ? $nchs[2] : 0, 'childages3'.$currModID) ;?>
							<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages4', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini inlineblock'), 'value', 'text', !empty($nchs[3]) ? $nchs[3] : 0, 'childages4'.$currModID) ;?>
							<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages5', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini inlineblock'), 'value', 'text', !empty($nchs[4]) ? $nchs[4] : 0, 'childages5'.$currModID) ;?>
						</div>
					</div>
				</div>
			</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <br />
              <textarea placeholder="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?>" name="form[note]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" ></textarea>    
            </div>
        </div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <br />
              <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PRIVACY') ?></label>
              <textarea name="form[privacy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" readonly ><?php echo $privacy ?></textarea>    
            </div>
             <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
		 	     <input name="form[accettazione]" class="checkbox" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR'); ?>">
			     <label for="agree"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM') ?></label>
			    </div>
        </div>

		<input type="hidden" id="actionform" name="actionform" value="formlabel" />
		<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
		<input type="hidden" id="orderType" name="form[orderType]" value="e" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $this->language;?>" />
		<input type="hidden" id="formCulture" name="form[Culture]" value="<?php echo $this->language;?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="" />
		<input type="hidden" id="offerId" name="form[offerId]" value="<?php echo $offer->PackageId;?>" />
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
		<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
		<input type="hidden"  id="persons<?php echo $currModID ?>" name="form[persons]" value="2">

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
		</form>
	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php endif?>
</div>
<script type="text/javascript">
jQuery(function($)
		{
			jQuery('.bfcmenu li a').click(function(e) {
				e.preventDefault();
				jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
			});
			$("#merchantdetailsoffers").validate(
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
						jQuery.blockUI({message: ''});
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

function countPersone<?php echo $currModID ?>() {
	var numAdults = new Number(jQuery('#adults<?php echo $currModID ?>').val());
	var numChildren = new Number(jQuery('#children<?php echo $currModID ?>').val());
	var numSeniores = 0;
	jQuery('#persons<?php echo $currModID ?>').val(numAdults + numChildren + numSeniores);
}

function quoteChanged<?php echo $currModID ?>() {
	countPersone<?php echo $currModID ?>();
}

function checkChildren<?php echo $currModID ?>() {
	var nch = new Number(jQuery('#children<?php echo $currModID ?>').val());
	jQuery("#childrenages<?php echo $currModID ?>").hide();
	jQuery("#childrenages<?php echo $currModID ?> select").hide();
	if (nch > 0) {
		jQuery("#childrenages<?php echo $currModID ?> select").each(function(i) {
			if (i < nch) {
				var id=jQuery(this).attr('id');
				jQuery(this).show();
			}
		});
		jQuery("#childrenages<?php echo $currModID ?>").show();
	}
}

</script>
