<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$params = $this->params;
$document = $this->document;
$language 	= $this->language;


$formatDate = 'm/d/Y';


$birthDate = new JDate('now'); 
$documentDate = new JDate('now'); 

//$checkmode = $this->params['checkmode'];
$route= JRoute::_('index.php?view=crew');

$crew = $this->item;
$user = JFactory::getUser();

$crewId = "";
$merchantId = $crew->MerchantId;
$orderId = $crew->OrderId;
$firstName = $user->name;
$lastName =  "";
$email = $user->email;
$nation =  "";
$culture = $language;
$address =   "";
$city =  "";
$postalCode =  "";
$province =  "";
$source =  "";
$phone =  "";
$newsletterSubscription =  "";
$gender =  "";
$birthLocation =  "";
$documentId =  "";
$documentNumber =  "";
$documentRelease =  "";
$plate =  ""; //targa
$exported = 0;
$crews = null;
$paxNumber = 0;

if(!empty($crew)){
	$crewId = $crew->CrewId;
	$merchantId = $crew->MerchantId;
	$orderId = $crew->OrderId;
	$firstName = $crew->FirstName;
	$lastName = $crew->LastName;
	$email = $crew->Email;
	if(!empty($crew->Nation)){	
		$nation = $crew->Nation;
	}
	$culture = $crew->Culture;
	$address = $crew->Address;
	$city = $crew->City;
	$postalCode = $crew->PostalCode;
	$province = $crew->Province;
	$source = $crew->Source;
	$phone = $crew->Phone;
	$newsletterSubscription = $crew->NewsletterSubscription;
	$gender = $crew->Gender;
	if (isset($crew->BirthDate)) $birthDate =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($crew->BirthDate,$formatDate));
	$birthLocation = $crew->BirthLocation;
	$documentId = $crew->DocumentId;
	$documentNumber = $crew->DocumentNumber;
	if (isset($crew->DocumentDate)) $documentDate = DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($crew->DocumentDate,$formatDate));
	$documentRelease = $crew->DocumentRelease;
	$plate = $crew->Plate; //targa
	$exported = $crew->Exported;
	if(!empty($crew->ChildCrews)){	
		$crews = $crew->ChildCrews;		
		$paxNumber = count($crews);
	}
}

$actionform =  $this->actionform;

//integer list
$crewslist = JHTML::_('select.integerlist',0,10,1, 'crewslist', null, $paxNumber);

// nation list 
$this->aNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
$this->aLanguageList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_LANGUAGESLIST'));
$this->aDocumentsList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_DOCUMENTSLIST'));

$nationsList = JHTML::_('select.genericlist',$this->aNationList, 'nation','class="full-width"','value', 'text', $nation);
$languagesList = JHTML::_('select.genericlist',$this->aLanguageList, 'culture','class="full-width"','value', 'text', $culture);
$documentsList = JHTML::_('select.genericlist',$this->aDocumentsList, 'documentId','class="full-width"','value', 'text', $documentId);
$privacy = BFCHelper::GetPrivacy($this->language);

?>   
<!-- {emailcloak=off} -->
<?php
$db   = JFactory::getDBO();

$uriOrder = 'index.php?option=com_bookingforconnector&view=orders&checkmode=1';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriOrder .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$uriOrder.='&Itemid='.$itemId;

$returnOrder = JRoute::_($uriOrder);
?>   

<?php if ($actionform == "insert") :?>

	<div class="alert alert-success">
    	<!-- <a href="#" class="close" data-dismiss="alert">x</a> -->
        Dati inseriti.

	</div>
<?php else: ?>

<form action="<?php echo  $route ?>" method="post"  id="formCrew" class="form-validate" >
	<input type="hidden" name="crewId" value="<?php echo $crewId;?>" >
	<input type="hidden" name="orderId" value="<?php echo $orderId;?>" >
	<input type="hidden" name="merchantId" value="<?php echo $merchantId;?>" > 
	<div class="titlecrew"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CUSTOMERDATA') ?></div>
	
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME') ?> *</label>
			<input name="firstName" type="text" placeholder="" value="<?php echo $firstName;?>" maxlength="255" required class="full-width">    
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME') ?> *</label>
			<input name="lastName" type="text" placeholder=""  value="<?php echo $lastName;?>"  maxlength="255" required class="full-width">    
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_EMAIL') ?> *</label>
			<input name="email" type="text" placeholder="email" value="<?php echo $email;?>" maxlength="255" required class="full-width">   
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_GENDER') ?></label>
			<select name="gender" class="full-width">   
				<option value="M" <?php if(strtolower($gender) == "m") {echo "selected";}?> ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_MALE') ?> </option>
				<option value="F" <?php if(strtolower($gender) == "f") {echo "selected";}?> ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FEMALE') ?> </option>
			</select>  
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHDATE') ?></label>
			<?php echo htmlHelper::calendar(
				$birthDate->format('m/d/Y'),
				'birthDate', 
				'birthDate', 
				'm/d/Y' /*input*/, 
				'd/m/Y' /*output*/, 
				'dd/mm/yy', 
				array('class' => 'full-width'), 
				true, 
				array(
					'numberOfMonths'=> '2',
//					'onSelect' => 'function(dateStr) { $("#formCheckMode").validate().element(this); }'
				)
			) ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHLOCATION') ?> *</label>
			<input name="birthLocation" type="text" placeholder="" value="<?php echo $birthLocation;?>" maxlength="255" required class="full-width">
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_ADDRESS') ?></label>
			<input name="address" type="text" placeholder="" value="<?php echo $address;?>" maxlength="255" class="full-width">

		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CITY') ?></label>
			<input name="city" type="text" placeholder="" value="<?php echo $city;?>" maxlength="255" class="full-width">
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_POSTALCODE') ?></label>
			<input name="postalCode" type="text" placeholder="" value="<?php echo $postalCode;?>" maxlength="20" class="full-width">

		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_PROVINCE') ?></label>
			<input name="province" type="text" placeholder="" value="<?php echo $province;?>" maxlength="255" class="full-width">         
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			 <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_NATION') ?></label>
			 <?php echo $nationsList; ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_PHONE') ?></label>
			<input name="phone" type="text"placeholder="" value="<?php echo $phone;?>" maxlength="255" class="full-width">     
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CULTURE') ?></label>
			<?php echo $languagesList; ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">

		</div>
	</div>
	<br /><br />
	<div class="titlecrew"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_DOCUMENT') ?></div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_DOCUMENTID') ?></label>
			<?php echo $documentsList;?> 
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_DOCUMENTNUMBER') ?></label>
			<input name="documentNumber" type="text" placeholder="" value="<?php echo $documentNumber;?>" maxlength="255" class="full-width">     
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_DOCUMENTDATE') ?></label>
			<?php echo htmlHelper::calendar(
					$documentDate->format('m/d/Y'),
					'documentDate', 
					'documentDate', 
					'm/d/Y' /*input*/, 
					'd/m/Y' /*output*/, 
					'dd/mm/yy', 
					array('class' => 'full-width'), 
					true, 
					array(
					'numberOfMonths'=> '2'
//					,'onSelect' => 'function(dateStr) { $("#formCheckMode").validate().element(this); }'
					)
				) ?>					  
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_DOCUMENTRELEASE') ?></label>
			<input name="documentRelease" type="text" placeholder="" value="<?php echo $documentRelease;?>" maxlength="255" class="full-width">  
		</div>
	</div>
	<br /><br />
	<div class="titlecrew"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_OTHERCREWS') ?></div>
<?php echo $crewslist;?>
<?php for ($i = 0; $i < 10; $i++) : ?>
	<?php
	// assign the current offer to a property so it will be available inside template 'offer'
	$this->params['currentCrew']= $i; // $Crew[$i]; 
	?>
	<?php echo  $this->loadTemplate('crews'); ?>
<?php endfor?>			
	<br /><br />
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CONFIRM') ?></label>
			<textarea name="privacy" style="height:200px;" readonly  class="full-width" ><?php echo $privacy ?></textarea>    
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
			<input name="form[accettazione]" class="checkbox" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM_ERROR'); ?>">
			<label for="agree"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM') ?></label>
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
			<input type="hidden" id="actionform" name="actionform" value="insert" />
			<button type="submit" class="button"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SEND') ?></button>
		</div>
	</div>
</form>

<script type="text/javascript">
jQuery(function($)
		{
		    $("#formCrew").validate(
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
		        	firstName: "required",
		        	lastName: "required",
						email:
						{
							  required: true,
							  email: true
						},
						birthDate: {
		        		required: true,
		        		dateITA: true
		        	},
		        	birthLocation: "required",
		        	confirmprivacy : "required"
		        },
		        messages:
		        {
		        	firstName: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME_ERROR') ?>",
		        	lastName: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME_ERROR') ?>",
		        	birthDate: {
		        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHDATE_ERROR') ?>",
		        		dateITA:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHDATE_FORMAT_ERROR') ?>"
		        		},
		        	birthLocation: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHLOCATION_ERROR') ?>",
		        	confirmprivacy: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_CONFIRM_ERROR') ?>",
						email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL_ERROR') ?>"
		        },
		        highlight: function(label) {
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.text('ok!').addClass('valid')
			    		.closest('.control-group').removeClass('error').addClass('success');
			    },
				submitHandler: function(form) {
					var $form = $(form);
					if($form.valid()){
						 jQuery.blockUI();
						 form.submit();
					}

				}

		    });


		    checkSelect();
		    
		    $("select[name=crewslist]").change(function() {
		    	checkSelect();
		    });

		    function checkSelect(){
				var limit = parseInt( $("select[name=crewslist]").val());
				
				for (var i=0;i<10;i++){
					var firstName = $("input[name=firstName" + i + "]");
					var lastName = $("input[name=lastName" + i + "]");
					var birthDate = $("input[name=birthDate" + i + "]");
					var birthLocation = $("input[name=birthLocation" + i + "]");
										
					if (i<limit){
						$('#divOtherPerson' + i).slideDown(500);
						if (firstName !== null && typeof firstName != 'undefined'){
							firstName.rules("add", {
							 required: true,
							 minlength: 2,
							 messages: {
							   required: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME_ERROR') ?>"
							 }
							});
						}
						if (lastName !== null && typeof lastName != 'undefined'){
							lastName.rules("add", {
							 required: true,
							 minlength: 2,
							 messages: {
							   required: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME_ERROR') ?>"
							 }
							});
						}						
						if (birthLocation !== null && typeof birthLocation != 'undefined'){
							birthLocation.rules("add", {
							 required: true,
							 minlength: 2,
							 messages: {
							   required: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHLOCATION_ERROR') ?>"
							 }
							});
						}
						if (birthDate !== null && typeof birthDate != 'undefined'){
							birthDate.rules("add", {
							 required: true,
							 dateITA: true,
							 messages: {
					        		required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHDATE_ERROR') ?>",
					        		dateITA:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHDATE_FORMAT_ERROR') ?>"
							 }
							});
						}
					}else{
						$('#divOtherPerson' + i).slideUp(500);
						if (firstName !== null && typeof firstName != 'undefined'){
							firstName.rules("remove", "required");
						}
						if (lastName !== null && typeof lastName != 'undefined'){
							lastName.rules("remove", "required");
						}
						if (birthDate !== null && typeof birthDate != 'undefined'){
							birthDate.rules("remove", "required");
						}
						if (birthLocation !== null && typeof birthLocation != 'undefined'){
							birthLocation.rules("remove", "required");
						}		
					}
				}
			}		    
		});
		


</script>
<?php endif; ?>
			<form method="post" action="<?php echo $returnOrder; ?>" class="form-inline">
						<?php echo JHtml::_('form.token'); ?>
						<input type="hidden" id="orderId" name="orderId" value="<?php echo $orderId?>" />
						<input type="hidden" id="actionform" name="actionform" value="login"/>
					</form>
