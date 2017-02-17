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

$formatDate = 'm/d/Y';

$birthDate = new JDate('now'); 
$merchantId = $this->item->MerchantId;
$orderId = $this->item->OrderId;

$currentCrew = intval($params['currentCrew']);
$firstName = "";
$lastName =  "";
$nation =  "";
$gender =  "";
$birthLocation =  "";
$parentCrewId =  $this->item->CrewId;
$crewId = "";

if(!empty($this->item->ChildCrews) && !empty($this->item->ChildCrews[$currentCrew])){
	$crew = $this->item->ChildCrews[$currentCrew];
	$countCrews= count($this->item->ChildCrews);
	$styleVisible="";
	if ($currentCrew >= $countCrews) $styleVisible="none";

	$firstName = $crew->FirstName;
	$lastName = $crew->LastName;
	$nation = $crew->Nation;
	$gender = $crew->Gender;
	if (isset($crew->BirthDate)) $birthDate =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($crew->BirthDate,$formatDate));
	$birthLocation = $crew->BirthLocation;
	if (!isset($crewId) || $crewId==null) $crewId=0;
	$parentCrewId = $crew->ParentCrewId;
	$crewId = $crew->CrewId;
}
$nationsList = JHTML::_('select.genericlist',$this->aNationList, 'nation' . $currentCrew,'class="full-width"','value', 'text', $nation);

?>
 
<div id="divOtherPerson<?php echo $currentCrew?>" style="display:<? echo $styleVisible?>">
	<input type="hidden" name="crewId<?php echo $currentCrew?>" value="<?php echo $crewId;?>" >
	<input type="hidden" name="parentCrewId<?php echo $currentCrew?>" value="<?php echo $parentCrewId;?>" >
	<input type="hidden" name="orderId<?php echo $currentCrew?>" value="<?php echo $orderId;?>" >
	<input type="hidden" name="merchantId<?php echo $currentCrew?>" value="<?php echo $merchantId;?>" >
		
	<div class="titlecrew"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_OTHERCREWS') ?> <?php echo $currentCrew +1?></div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FIRSTNAME') ?></label>
			<input name="firstName<?php echo $currentCrew?>" type="text" placeholder="" value="<?php echo $firstName;?>" maxlength="255" required class="full-width">    
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_LASTNAME') ?></label>
			<input name="lastName<?php echo $currentCrew?>" type="text" placeholder=""  value="<?php echo $lastName;?>" maxlength="255" required class="full-width">    
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_GENDER') ?></label>
			<select class="full-width" name="gender<?php echo $currentCrew?>">
				<option value="M" <?php if(strtolower($gender) == "m") {echo "selected";}?> ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_MALE') ?> </option>
				<option value="F" <?php if(strtolower($gender) == "f") {echo "selected";}?> ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_FEMALE') ?> </option>
			</select>  

		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHDATE') ?></label>
			<?php echo htmlHelper::calendar(
					$birthDate->format('m/d/Y'),
					'birthDate'.$currentCrew, 
					'birthDate'.$currentCrew, 
					'm/d/Y' /*input*/, 
					'd/m/Y' /*output*/, 
					'dd/mm/yy', 
					array('class' => 'full-width'), 
					true, 
					array(
					'numberOfMonths'=> '2',
						/*'onSelect' => 'function(dateStr) { $("#formCheckMode").validate().element(this); }'*/
					)
				) ?>                 

		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_BIRTHLOCATION') ?></label>
			<input name="birthLocation<?php echo $currentCrew?>" type="text" placeholder="" value="<?php echo $birthLocation;?>" required class="full-width">
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
			<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_CREW_VIEW_NATION') ?></label>
			<?php echo $nationsList; ?>
		</div>
	</div>
</div>
