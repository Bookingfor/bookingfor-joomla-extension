<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/simple_html_dom.php';

class FormHelper {

	
	public static function getCustomerData($formData) {
		if ($formData == null) {
			$formData = BFCHelper::getArray('form');
		}
		
				$Firstname = isset($formData['Name'])?$formData['Name']:''; //
				$Lastname = isset($formData['Surname'])?$formData['Surname']:''; // => $formData['Surname'],
				$Email = isset($formData['Email'])?$formData['Email']:''; // => $formData['Email'],
				$Address = isset($formData['Address'])?$formData['Address']:''; // => $formData['Address'],
				$Zip = isset($formData['Cap'])?$formData['Cap']:''; // => $formData['Cap'],
				$City = isset($formData['City'])?$formData['City']:''; // => $formData['City'],
				$Country = isset($formData['Provincia'])?$formData['Provincia']:''; // => $formData['Provincia'],
				$Nation = isset($formData['Nation'])?self::getOptionsFromSelect($formData, 'Nation'):''; // => self::getOptionsFromSelect($formData, 'Nation'),
				$Phone = isset($formData['Phone'])?$formData['Phone']:''; // => $formData['Phone'],
				$Fax = isset($formData['Fax'])?$formData['Fax']:''; // => $formData['Fax'],
				$VatCode = isset($formData['VatCode'])?$formData['VatCode']:''; // => $formData['VatCode'],
				$Culture = isset($formData['lingua'])?self::getOptionsFromSelect($formData, 'lingua'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
				$UserCulture = isset($formData['lingua'])?self::getOptionsFromSelect($formData, 'lingua'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
				$Culture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):$Culture; // => self::getOptionsFromSelect($formData, 'Culture'),
				$UserCulture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):$UserCulture; // => self::getOptionsFromSelect($formData, 'Culture'),
				$Culture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$Culture; // => self::getOptionsFromSelect($formData, 'Culture'),
				$UserCulture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$UserCulture; // => self::getOptionsFromSelect($formData, 'Culture'),

		$customerData = array(
				'Firstname' => $Firstname,
				'Lastname' => $Lastname,
				'Email' => $Email,
				'Address' => $Address,
				'Zip' => $Zip,
				'City' => $City,
				'Country' => $Country,
				'Nation' => $Nation,
				'Phone' => $Phone,
				'Fax' => $Fax,
				'VatCode' => $VatCode,
				'Culture' => $Culture,
				'UserCulture' => $UserCulture
		);
		
		return $customerData;
	}
	
	public static function getOptionsFromSelect($formData, $str){
		if ($formData == null) {
			$formData = BFCHelper::getArray('form');
		}
		$aStr = null;
		if(isset($formData[$str])){
			$aStr = $formData[$str];
		}
		if(isset($aStr))
		{
			if (!is_array($aStr)) return $aStr;
			$nStr = count($aStr);
			if ($nStr==1){
				return $aStr[0];
			}else
			{
				return implode($aStr, ',');
			}
		}
		return '';
	}
		
}
