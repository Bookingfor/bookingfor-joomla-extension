<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


//setting constants
if (!defined('COM_BOOKINGFORCONNECTOR_CONFIG_LOADED')) {
	$config = JComponentHelper::getParams('com_bookingforconnector');

	$isportal = $config->get('isportal', 1);
	$usessl = $config->get('usessl', 0);
	$ssllogo = $config->get('ssllogo', '');
	$bootstrap = $config->get('bootstrap', 2);
	$bootstrapRow = "no-gutter row-fluid";
	$bootstrapCol = "no-gutter span";
	$bootstrapColSmall = "no-gutter span";
	$bootstrapColMedium = "no-gutter span";
	$wsurl= $config->get('wsurl', '');
	$apikey= $config->get('apikey', '');
	$formlabel= $config->get('formlabel', '');
	///$imgurl= $params->get('imgurl', '');
	$XGooglePosDef = $config->get('posx', 0);
	$YGooglePosDef = $config->get('posy', 0);

		$gaenabled = $config->get('gaenabled', 0);
		$gaaccount = $config->get('gaaccount', '');
		$eecenabled = $config->get('eecenabled', 0);

	$enablecache =  $config->get('enablecache', 1);

	$nMonthinCalendar = 2;

	$useragent=$_SERVER['HTTP_USER_AGENT'];

	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
		$nMonthinCalendar = 1;
	}

	if($bootstrap == 3){
		$bootstrapRow = "no-gutter row";
		$bootstrapCol = "no-gutter col-md-";
		$bootstrapColSmall = "no-gutter col-xs-";
		$bootstrapColMedium = "no-gutter col-sm-";
	}

	if (!defined('COM_BOOKINGFORCONNECTOR_FORM_KEY')) {
			define('COM_BOOKINGFORCONNECTOR_FORM_KEY', $formlabel);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR')) {
			define('COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR', $nMonthinCalendar);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_ISPORTAL')) {
			define('COM_BOOKINGFORCONNECTOR_ISPORTAL', $isportal);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_USESSL')) {
			define('COM_BOOKINGFORCONNECTOR_USESSL', $usessl);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_SSLLOGO')) {
			define('COM_BOOKINGFORCONNECTOR_SSLLOGO', $ssllogo);
	} 

	if (!defined('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW')) {
			define('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW', $bootstrapRow);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL')) {
			define('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL', $bootstrapCol);
			define('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM', $bootstrapColMedium);
			define('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL', $bootstrapColSmall);
	} 

	$showdata = $config->get('showdata', '');
	if (!defined('COM_BOOKINGFORCONNECTOR_SHOWDATA')) {
			define('COM_BOOKINGFORCONNECTOR_SHOWDATA', $showdata);
	} 

	if (!defined('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW')) {
			define('COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW', $bootstrapRow);
	} 
	$wsurl = strtolower($wsurl);

	if(strpos($wsurl,'https://') !== false){
		$wsurl = str_replace("https://", "", $wsurl);
		$wsurl = str_replace(".bookingfor.com/modules/bookingfor/services/bookingservice.svc", "", $wsurl);
		$wsurl = str_replace("/", "", $wsurl);
	}

	if (!defined('COM_BOOKINGFORCONNECTOR_WSURL')) {

//			define('COM_BOOKINGFORCONNECTOR_WSURL', 'http://bookingforbeta.azurewebsites.net/modules/bookingfor/services/bookingservice.svc');
//			define('COM_BOOKINGFORCONNECTOR_WSURL', 'https://bookingforservices.cloudapp.net/modules/bookingfor/services/bookingservice.svc');
			define('COM_BOOKINGFORCONNECTOR_WSURL', 'https://' . $wsurl . '.bookingfor.com/modules/bookingfor/services/bookingservice.svc');
//			define('COM_BOOKINGFORCONNECTOR_WSURL', 'http://localhost:58898/modules/bookingfor/services/bookingservice.svc');
	} 
	
	if (!defined('COM_BOOKINGFORCONNECTOR_IMGURL')) {						
			define('COM_BOOKINGFORCONNECTOR_IMGURL', $wsurl . '/bookingfor/images');
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_BASEIMGURL')) {						
			define('COM_BOOKINGFORCONNECTOR_BASEIMGURL','https://cdnbookingfor.blob.core.windows.net/' . $wsurl . '/bookingfor/images');
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_GOOGLE_POSX')) {
			define('COM_BOOKINGFORCONNECTOR_GOOGLE_POSX', $XGooglePosDef);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_GOOGLE_POSY')) {
			define('COM_BOOKINGFORCONNECTOR_GOOGLE_POSY', $YGooglePosDef);
	} 

	if (!defined('COM_BOOKINGFORCONNECTOR_GAENABLED')) {
			define('COM_BOOKINGFORCONNECTOR_GAENABLED', $gaenabled);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_GAACCOUNT')) {
			define('COM_BOOKINGFORCONNECTOR_GAACCOUNT', $gaaccount);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_EECENABLED')) {
			define('COM_BOOKINGFORCONNECTOR_EECENABLED', $eecenabled);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_ENABLECACHE')) {
			define('COM_BOOKINGFORCONNECTOR_ENABLECACHE', $enablecache);
	} 

	$adultsage =  $config->get('adultsage', 18);
	$adultsqt =  $config->get('adultsqt', 2);
	$childrensage =  $config->get('childrensage', 12);
	$senioresage =  $config->get('senioresage', 65);
	if (!defined('COM_BOOKINGFORCONNECTOR_ADULTSAGE')) {
			define('COM_BOOKINGFORCONNECTOR_ADULTSAGE', $adultsage);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_ADULTSQT')) {
			define('COM_BOOKINGFORCONNECTOR_ADULTSQT', $adultsqt);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_CHILDRENSAGE')) {
			define('COM_BOOKINGFORCONNECTOR_CHILDRENSAGE', $childrensage);
	} 
	if (!defined('COM_BOOKINGFORCONNECTOR_SENIORESAGE')) {
			define('COM_BOOKINGFORCONNECTOR_SENIORESAGE', $senioresage);
	} 

	$maxqtSelectable =  $config->get('maxqtSelectable', 20);
	if (!defined('COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE')) {
			define('COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE', $maxqtSelectable);
	} 

	define('COM_BOOKINGFORCONNECTOR_CONFIG_LOADED', 1);
}

class BFCHelper {
	public static $defaultFallbackCode = 'en-gb';
	private static $sessionSeachParamKey = 'searchparams';
	private static $image_basePath = COM_BOOKINGFORCONNECTOR_BASEIMGURL;
	private static $image_basePathCDN = COM_BOOKINGFORCONNECTOR_IMGURL_CDN;
	private static $searchResults = array();
	private static $currentState = array();
	private static $defaultCheckMode = 5;
	private static $favouriteCookieName = "BFFavourites";
		
	private static $justloaded = false;

	private static $image_paths = array(
		'merchant' => '/merchants/',
		'resources' => '/products/unita/',
		'resource_gallery_xml' => '/products/unita/%d/gallery/gallery.xml',
		'offers' => '/packages/',
		'services' => '/servizi/',
		'merchantgroup' => '/merchantgroups/',
		'tag' => '/tags/',
		'onsellunits' => '/products/unitavendita/',
		'condominium' => '/products/condominio/',
		'variationplans' => '/variationplans/',
		'prices' => '/prices/'
	);

	private static $image_path_resized = array(
		'merchant_list'						=> '148x148',
		'merchant_list_default'				=> '148x148',
		'resource_list_default'				=> '148x148',
		'onsellunit_list_default'			=> '148x148',
		'resource_list_default_logo'		=> '148x148',
		'resource_list_merchant_logo'		=> '200x70',
		'merchant_logo'						=> '200x70',
		'merchant_logo_small'				=> '65x65',
		'merchant_logo_small_top'			=> '250x90',
		'merchant_logo_small_rapidview'		=> '200x70',
		'condominium_list_default'			=> '148x148',
		'offer_list_default'				=> '148x148',
		'resource_service'					=> '24x24',
		'resource_planimetry'				=> '400x250',
		'merchant_gallery_full'				=> '500x375',
		'merchant_mono_full'				=> '770x545',
		'merchant_gallery_thumb'			=> '85x85',
		'resource_gallery_full'				=> '692x450',
		'resource_mono_full'				=> '640x450',
		'resource_gallery_thumb'			=> '85x85',
		'resource_gallery_full_rapidview'	=> '416x290',
		'resource_gallery_thumb_rapidview'	=> '80x53',
		'resource_mono_full_rapidview'		=> '416x290',
		'resource_gallery_default_logo'		=> '100x100',
		'onsellunit_gallery_full'			=> '550x300',
		'onsellunit_mono_full'				=> '550x300',
		'onsellunit_default_logo'			=> '250x250',
		'onsellunit_gallery_thumb'			=> '85x85',
		'onsellunit_map_default'			=> '85x85',
		'onsellunit_showcase'				=> '180x180',
		'onsellunit_gallery'				=> '106x67',
		'condominium_map_default'			=> '85x85',
		'merchant_merchantgroup'			=> '40x40',
		'resource_search_grid'			=> '380x215',
		'merchant_resource_grid' => '380x215',
		'small' => '201x113',
		'medium' => '380x215',
		'big' => '820x460',
		'logomedium' => '148x148',
		'logobig' => '170x95',
		'tag24' => '24x24'
	);
	
	private static $image_resizes = array(
		'merchant_list' => 'width=100&bgcolor=FFFFFF',
		'merchant_logo' => 'width=200&bgcolor=FFFFFF',
		'merchant_logo_small' => 'width=65&height=65&bgcolor=FFFFFF',
		'merchant_logo_small_top' => 'width=250&height=90&bgcolor=FFFFFF',
		'merchant_logo_small_rapidview' => 'width=180&height=65&bgcolor=FFFFFF',
		'resource_list_default' => 'width=148&height=148&mode=crop&anchor=middlecente&bgcolor=FFFFFF',
		'onsellunit_list_default' => 'width=148&height=148&mode=crop&anchor=middlecenter&bgcolor=FFFFFF',
		'resource_list_default_logo' => 'width=148&height=148&bgcolor=FFFFFF',
		'resource_list_merchant_logo' =>  'width=200&height=70&bgcolor=FFFFFF',
		'merchant_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
		'condominium_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
		'offer_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
		'resource_service' => 'width=24&height=24',
		'resource_planimetry' => 'width=400&height=250&mode=pad&anchor=middlecenter',
		'merchant_gallery_full' => 'width=500&height=375&mode=pad&anchor=middlecenter',
		'merchant_mono_full' => 'width=770&height=545&mode=crop&anchor=middlecenter&scale=both',
		'merchant_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
		'resource_gallery_full' => 'width=692&height=450&mode=pad&anchor=middlecenter&ext=.jpg',
		'resource_mono_full' => 'width=640&height=450&mode=pad&anchor=middlecenter&scale=both',
		'resource_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
		'resource_gallery_full_rapidview' => 'w=416&h=290&mode=crop&anchor=middlecenter&ext=.jpg',
		'resource_gallery_thumb_rapidview' => 'width=80&height=53&mode=crop&anchor=middlecenter',
		'resource_mono_full_rapidview' => 'w=416&h=290&mode=crop&anchor=middlecenter&ext=.jpg',
		'resource_gallery_default_logo' => 'w=100&h=100&mode=pad&anchor=middlecenter&ext=.jpg',
		'onsellunit_gallery_full' => 'w=550&h=300&bgcolor=EDEDED&mode=pad&anchor=middlecenter&ext=.jpg',
		'onsellunit_mono_full' => 'width=550&height=300&mode=crop&anchor=middlecenter&scale=both',
		'onsellunit_default_logo' => 'width=250&height=250&bgcolor=FFFFFF',
		'onsellunit_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
		'onsellunit_map_default' => 'width=85&height=85&bgcolor=FFFFFF',
		'onsellunit_showcase' => 'width=180&height=180&bgcolor=FFFFFF&mode=crop&anchor=middlecenter',
		'onsellunit_gallery' => 'width=106&height=67&bgcolor=FFFFFF',
		'condominium_map_default' => 'width=85&height=85&bgcolor=FFFFFF',
		'merchant_merchantgroup' => 'width=40&height=40',
		'small' => 'width=201&height=113&mode=crop&anchor=middlecenter',
		'medium' => 'width=380&height=215&mode=crop&anchor=middlecenter',
		'big' => 'width=820&height=460&mode=crop&anchor=middlecenter',
		'logomedium' => 'width=148&height=148&anchor=middlecenter&bgcolor=FFFFFF',
		'logobig' => 'width=170&height=95&anchor=middlecenter&bgcolor=FFFFFF',
		'tag24' => 'width=24&height=24'
	);
	
	public static $daySpan = '+7 day';
	public static $defaultDaysSpan = '+7 days';
	public static $defaultDuration = 7;
	public static $defaultAdultsAge = COM_BOOKINGFORCONNECTOR_ADULTSAGE;
	public static $defaultChildrensAge = COM_BOOKINGFORCONNECTOR_CHILDRENSAGE;
	public static $defaultAdultsQt = COM_BOOKINGFORCONNECTOR_ADULTSQT;
	public static $defaultSenioresAge = COM_BOOKINGFORCONNECTOR_SENIORESAGE;
	public static $onsellunitDaysToBeNew = 120;
	
	//public static $typologiesMerchantResults = array(1,6);
	public static function isUnderHTTPS() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' );
	}
	
	public static function isMerchantAnonymous($id) {
		if (defined('COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE')) {
			$aAnon = explode(",",COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE);
			return in_array($id, $aAnon);
		}	
		return false;
	}
	
	public static function showMerchantRatingByCategoryId($id) {
		if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_SHOW_RATING_MERCHANT')) {
			$aAnon = explode(",",COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_SHOW_RATING_MERCHANT);
			return in_array($id, $aAnon);
		}	
		return false;
	}
	
	public static function getCategoryMerchantResults($language='') {
//		if (self::isMerchantBehaviour()) {
//			return array();
//		}	
		$groupOnSearch = array();
		$merchantCategories = BFCHelper::getMerchantCategoriesForRequest($language);
		if(!empty($merchantCategories)){
			$groupOnSearch = array_unique(array_map(function ($i) { 
				if($i->GroupOnSearch){
					return $i->MerchantCategoryId;
				}
				return 0; 
				}, $merchantCategories));	
		}
		return $groupOnSearch;
		//return array(COM_BOOKINGFORCONNECTOR_CATEGORIES_GROUPING_RESULT_INTO_MERCHANT);
	}
		
	public static function getTypologiesMerchantResults() {
		if (self::isMerchantBehaviour()) {
			return array();
		}	
		return array(1,6);
	}
	public static function getAddressDataByMerchant($id) {
		if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_RESOURCE_ADDRESSDATA_BY_MERCHANT')) {
			$aAnon = explode(",",COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_RESOURCE_ADDRESSDATA_BY_MERCHANT);
			return in_array($id, $aAnon);
		}	
		return false;
	}

	public static function isMerchantBehaviour() {
		if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTBEHAVIOUR')) {
			if (COM_BOOKINGFORCONNECTOR_MERCHANTBEHAVIOUR) {
				return true;
			}
		}	
		return false;
	}
		
	public static function setSearchResult($searchid, $value) {
		if ($value == null) {
			if (array_key_exists($searchid, self::$searchResults)) {
				unset(self::$searchResults[$searchid]);
			}
		} else
		{
			self::$searchResults[$searchid] = $value;
		}
	}
	
	public static function getSearchResult($searchid) {
		if (array_key_exists($searchid, self::$searchResults)) {
			return self::$searchResults[$searchid];
		}
		return null;
	}
	
	public static function getItem($xml, $itemName, $itemContext = null) {
		if ($xml==null || $itemName == null) return '';
		try {
			$xdoc = new SimpleXmlElement($xml);
			if (isset($itemContext)) $xdoc= $xdoc->$itemContext;
			$item = $xdoc->$itemName;
		} catch (Exception $e) {
			// maybe it's not a well formed XML?
			return $itemName;
		}
		return $item;
	}

	public static function priceFormat($number) {
		return number_format($number, 2, ',', '.');
	}
	
/* -------------------------------- */

	public static function getCartMultimerchantEnabled() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
		return $model->getCartMultimerchantEnabled();
	}

	public static function GetPrivacy($language) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
		return $model->getPrivacy($language);
	}
	public static function GetAdditionalPurpose($language) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
		return $model->getAdditionalPurpose($language);
	}
	
	public static function GetProductCategoryForSearch($language='', $typeId = 1) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
		return $model->getProductCategoryForSearch($language, $typeId);
	}


	public static function GetPhoneByMerchantId($merchantId,$language,$number) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->getPhoneByMerchantId($merchantId,$language,$number);
	}

	public static function GetFaxByMerchantId($merchantId,$language) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->GetFaxByMerchantId($merchantId,$language);
	}

	public static function setCounterByMerchantId($merchantId = null, $what='', $language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->setCounterByMerchantId($merchantId, $what, $language);
	}
	public static function setCounterByResourceId($resourceId = null, $what='', $language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnit', 'BookingForConnectorModel');
		return $model->setCounterByResourceId($resourceId, $what, $language);
	}

/* -------------------------------- */
	public static function getMerchant() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->getItem();
	}
	public static function getMerchantFromServicebyId($merchantId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->getMerchantFromServicebyId($merchantId);
	}

	public static function getRatingByMerchantId($merchantId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->getMerchantRatingAverageFromService($merchantId);
	}
	public static function getRatingsByOrderId($orderId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
		return $model->getRatingsByOrderIdFromService($orderId);
	}
	public static function getTotalRatingsByOrderId($orderId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
		return $model->getTotalRatingsByOrderId($orderId);
	}	

	public static function GetMerchantBookingTypeList($SearchModel, $resourceId, $cultureCode) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetMerchantBookingTypeList($SearchModel, $resourceId, $cultureCode);
	}

	public static function getResourceRatingAverage($merchantId, $resourceId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getRatingAverageFromService($merchantId, $resourceId);
	}

	public static function getMerchantGroupsByMerchantId($merchantId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
		return $model->getMerchantGroupsByMerchantIdFromService($merchantId);
	}
	

	public static function getUnitCategories() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getUnitCategories();
	}
	
	public static function getLocationZones() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getLocationZones();
	}
	
	public static function getLocationZonesByLocationId($locationId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getLocationZones($locationId);
	}

	public static function getLocationZonesBySearch() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
		return $model->getLocationZonesBySearch();
	}
	public static function getLastLocationZoneOnsell() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getLastLocationZoneOnsell();
	}

	public static function getLocations() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getLocations();
	}
	public static function getLocationById($locationId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getLocationById($locationId);
	}

	public static function getMerchantTypes() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantTypes();
	}
	
	public static function getMasterTypologies($onlyEnabled = true) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
		return $model->getMasterTypologies($onlyEnabled);
	}
	
//	public static function getMerchantGroups() {
//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
//		return $model->getMerchantGroups();
//	}
	
	public static function getResource() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getItem();
	}
	public static function getOnSellUnit() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnit', 'BookingForConnectorModel');
		return $model->getItem();
	}
	
	public static function getResourceByMasterTypologyId($masterTypologyId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
		$mystate=$model->getState();
		$model->setState('params', array(
			'masterTypeId' => $masterTypologyId
		));
		return $model->getItems();
	}
	
	public static function getResourceModel() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model;
	}
	
	public static function getMerchantCategories() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantCategories();
	}
	public static function getMerchantCategoriesForRequest($language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantCategoriesForRequest($language);
	}

	public static function getMerchantCategory($merchanCategoryId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantCategory($merchanCategoryId);
	}
   public static function getMerchantByCategoryId($merchanCategoryId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantByCategoryId($merchanCategoryId);
	}
	
	public static function getServicesByMerchantsCategoryId($merchantCategoryId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getServicesByMerchantsCategoryId($merchantCategoryId,$language);
	}


	public static function GetPolicy($resourcesId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetPolicy($resourcesId,$language);
	}

	public static function GetResourcesByIds($listsId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
		return $model->GetResourcesByIds($listsId,$language);
	}
	public static function GetResourcesById($id,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getResourceFromServicebyId($id);
	}

	public static function GetResourcesCalculateByIds($listsId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
		return $model->GetResourcesCalculateByIds($listsId,$language);
	}

	
	public static function getDiscountDetails($ids, $language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getDiscountDetails($ids,$language);
	}
	public static function GetDiscountsByResourceId($resourcesId,$hasRateplans) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetDiscountsByResourceId($resourcesId,$hasRateplans);
	}

	public static function getRateplanDetails($rateplanId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getRateplanSimpleDetails($rateplanId);
	}
	public static function GetResourcesOnSellByIds($listsId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->GetResourcesByIds($listsId,$language);
	}
	public static function GetServicesByIds($listsId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Services', 'BookingForConnectorModel');
		return $model->getServicesByIds($listsId,$language);
	}

	public static function getServicesForSearchOnSell($language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getServicesForSearchOnSell($language);
	}
	public static function getServicesForSearch($language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
		return $model->getServicesForSearch($language);
	}

	public static function getResourcesOnSellShowcase($language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getResourcesOnSellShowcase($language);
	}

	public static function getResourcesOnSellGallery($language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getResourcesOnSellGallery($language);
	}
	
	public static function getAvailableLocationsAverages($language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getAvailableLocationsAverages($language);
	}

	public static function getCategoryPriceMqAverages($language='',$locationid) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getCategoryPriceMqAverages($language,$locationid);
	}
	
	public static function getPriceAverages($language='',$locationid,$unitcategoryid) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getPriceAverages($language,$locationid,$unitcategoryid);
	}

	public static function getPriceHistory($language='',$locationid,$unitcategoryid) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getPriceHistory($language,$locationid,$unitcategoryid);
	}

	public static function getPriceMqAverageLastYear($language='',$locationid,$unitcategoryid ,$contracttype = 0) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getPriceMqAverageLastYear($language,$locationid,$unitcategoryid, $contracttype);
	}

	public static function getMerchantsByIds($listsId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantsByIds($listsId,$language);
	}

	public static function GetCondominiumsByIds($listsId,$language='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Condominiums', 'BookingForConnectorModel');
		return $model->GetCondominiumsByIds($listsId,$language);
	}

	public static function getMerchantsSearch($text,$start,$limit,$order,$direction) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->getMerchantsForSearch($text,$start,$limit,$order,$direction);
	}

	public static function getResourcesSearch($text,$start,$limit,$order,$direction) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
		return $model->getResourcesForSearch($text,$start,$limit,$order,$direction);
	}	

	public static function getTags($language='',$categoryIds='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
		return $model->getTags($language,$categoryIds,null,null);
	}
	public static function getTagsForSearch($language='',$categoryIds='') {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
		return $model->getTagsForSearch($language,$categoryIds);
	}

	public static function getMerchantsExt($tagids, $start = null, $limit = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
		return $model->getMerchantsExt($tagids, $start, $limit);
	}

	public static function GetAlternateResources($start, $limit, $ordering = null, $direction = null, $merchantid = null,  $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid, $ignorePagination, false, $excludedResources, $requiredOffers, $overrideFilters);
	}

	public static function getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid, $ignorePagination, false, $excludedResources, $requiredOffers, $overrideFilters);
	}

	public static function prepareOrderData($formData, $customerData=null, $suggestedStay=null, $otherData=null, $creditCardData=null) {
		if ($formData == null) {
			$formData = BFCHelper::getArray('form');
		}
			
		$userNotes = $formData['note'];
		$cultureCode = $formData['cultureCode'];
		$merchantId = $formData['merchantId'];
		$orderType = $formData['orderType'];
		$label = $formData['label'];
		$customerDatas = array($customerData);
		$bt = array();
		if(!empty($formData['bookingType']) &&  strpos($formData['bookingType'].'',':') !== false ){
			$bt = explode(':',$formData['bookingType'].'');
		}
		if(!isset($suggestedStay)){
			$suggestedStay = new stdClass; 
		}
		array_push($bt, null,null);

		if(isset($bt[0])){
			$suggestedStay->MerchantBookingTypeId = $bt[0];
		}
				
		$orderData = array(
				'customerData' => $customerDatas,
				'suggestedStay' =>$suggestedStay,
				'creditCardData' => $creditCardData,
				'otherNoteData' => $otherData,
				'merchantId' => $merchantId,
				'orderType' => $orderType,
				'userNotes' => $userNotes,
				'label' => $label,
				'cultureCode' => $cultureCode
				);

		return $orderData;
	}

	public static function getSingleOrderFromService($orderId = 0) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->getSingleOrderFromService($orderId);
	}	
	
	public static function getOrderMerchantPayment($order) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
		return $model->getOrderMerchantPayment($order);
	}	
	public static function getMerchantPaymentData($bookingTypeId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
		return $model->getMerchantPaymentData($bookingTypeId);
	}	
	
	public static function setOrder($customerData = NULL, $suggestedStay = NULL, $creditCardData = NULL, $otherNoteData = NULL, $merchantId = NULL, $orderType = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processOrder = NULL, $priceType) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->setOrder($customerData, $suggestedStay, $creditCardData, $otherNoteData, $merchantId, $orderType, $userNotes, $label, $cultureCode, $processOrder, $priceType);
	}
	
	public static function setOrderStatus($orderId = NULL, $status = NULL, $sendEmails = false, $setAvailability = false, $paymentData = NULL)  {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->setOrderStatus($orderId, $status, $sendEmails, $setAvailability, $paymentData);
	}
	
	public static function updateCCdata($orderId, $creditCardData = NULL, $processOrder = NULL) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->updateCCdata($orderId, $creditCardData, $processOrder);
	}

	public static function getOrderPayments($start,$limit,$orderid) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
		return $model->getOrderPayments($start,$limit,$orderid);
	}

	public static function getTotalOrderPayments($orderId = NULL)  {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
		return $model->getTotalOrderPayments($orderId);
	}
	public static function setOrderPayment($orderId = NULL, $status = 0, $sendEmails = false,$amount = 0,$bankId, $paymentData = NULL, $cultureCode = NULL, $processOrder = NULL)  {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
		return $model->setOrderPayment($orderId, $status, $sendEmails, $amount,$bankId, $paymentData, $cultureCode, $processOrder);
	}
	public static function setInfoRequest($customerData = NULL, $suggestedStay = NULL, $otherNoteData = NULL, $merchantId = NULL, $type = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processInfoRequest = NULL) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('InfoRequests', 'BookingForConnectorModel');
		return $model->setInfoRequest($customerData, $suggestedStay, $otherNoteData, $merchantId, $type, $userNotes, $label, $cultureCode, $processInfoRequest);
	}

	public static function setAlertOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processAlert = NULL, $enabled = NULL) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
		return $model->setAlertOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processAlert, $enabled);
	}
	public static function sendRequestOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processRequest = NULL) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
		return $model->sendRequestOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processRequest);
	}
	
	public static function unsubscribeAlertOnSell($hash = NULL, $id = NULL)  {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
		return $model->unsubscribeAlertOnSell($hash, $id);
	}

	public static function setMerchantAndUser($customerData = NULL, $password = NULL, $merchantType = 0, $merchantCategory = 0, $company = NULL, $userPhone = NULL, $webSite = NULL) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		return $model->setMerchantAndUser($customerData, $password, $merchantType, $merchantCategory, $company, $userPhone, $webSite);
	}

	public static function sendNLPRequest($email = NULL, $cultureCode = NULL, $firstname = NULL, $lastname = NULL, $IDcategoria = NULL, $phone = NULL, $address = NULL, $nation = NULL, $reqUrlReg = NULL,$denominazione= NULL, $referer = NULL) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('NewsLetterPlus', 'BookingForConnectorModel');
		return $model->sendRequest($email, $cultureCode, $firstname, $lastname, $IDcategoria, $phone, $address, $nation, $reqUrlReg,$denominazione= NULL, $referer);
	}

	public static function getCountAllResourcesOnSell() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
		return $model->getAllResources();
	}

	public static function getStartDate() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getStartDateFromService();
	}
	
	public static function getEndDate() {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getEndDate();
	}

	public static function getStartDateByMerchantId($merchantId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getStartDateByMerchantId($merchantId);
	}
	
	public static function getEndDateByMerchantId($merchantId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getEndDateByMerchantId($merchantId);
	}

	public static function getCheckInDates($resourceId = null,$ci = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getCheckInDatesFromService($resourceId ,$ci);
	}
	public static function GetCheckInDatesPerTimes($resourceId = null,$ci = null, $limitTotDays = 0) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetCheckInDatesPerTimes($resourceId ,$ci, $limitTotDays);
	}
	public static function GetListCheckInDayPerTimes($resourceId = null,$ci = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetListCheckInDayPerTimes($resourceId ,$ci);
	}
	
	public static function GetCheckInDatesTimeSlot($resourceId = null,$ci = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetCheckInDatesTimeSlot($resourceId ,$ci);
	}

	public static function getCheckOutDates($resourceId = null,$checkIn = null,$checkOut = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getCheckOutDatesFromService($resourceId ,$checkIn,$checkOut);
	}
	
	public static function getCheckAvailabilityCalendar($resourceId = null,$checkIn= null,$checkOut= null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getCheckAvailabilityCalendarFromService($resourceId,$checkIn,$checkOut);
	}
	
	//Please attention it's another method into another model, this is for list, not for single
	public static function getCheckAvailabilityCalendarFromlList($resourcesId = null,$checkIn= null,$checkOut= null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
		return $model->getCheckAvailabilityCalendarFromService($resourcesId,$checkIn,$checkOut);
	}

	//Please attention it's another method into another model, this is for list, not for single
	public static function getStayFromParameter($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$extras='',$packages,$pricetype='',$rateplanId=null,$variationPlanId=null,$hasRateplans=null ) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getStayFromServiceFromParameter($resourceId,$checkIn,$duration,$paxages,$extras,$packages,$pricetype,$rateplanId,$variationPlanId,$hasRateplans);
	}
	public static function getCompleteRateplansStayFromParameter($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$selectablePrices='',$packages,$pricetype='',$rateplanId=null,$variationPlanId=null ) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->getCompleteRateplansStayFromParameter($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$rateplanId,$variationPlanId);
	}

	public static function GetCompleteRatePlansStayWP($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$selectablePrices='',$packages='',$pricetype='',$rateplanId=null,$variationPlanId=null,$language="",$merchantBookingTypeId = "", $getAllResults=false ) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetCompleteRatePlansStayWP($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$rateplanId,$variationPlanId,$language,$merchantBookingTypeId, $getAllResults);
	}
	public static function GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language="" ){
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language );
	}

	public static function setRating(
			$name = NULL, 
			$city = NULL, 
			$typologyid = NULL, 
			$email = NULL, 
			$nation = NULL, 
			$merchantId = NULL,
			$value1= NULL, 
			$value2= NULL, 
			$value3= NULL, 
			$value4= NULL, 
			$value5= NULL, 
			$totale = NULL, 
			$pregi =NULL, 
			$difetti =NULL, 
			$userId = NULL,
			$cultureCode = NULL,
			$checkin= NULL, 
			$resourceId= NULL, 
			$orderId= NULL, 
			$label = NULL
		) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
		return $model->setRating($name, $city, $typologyid, $email, $nation, $merchantId,$value1, $value2, $value3, $value4, $value5, $totale, $pregi, $difetti, $userId, $cultureCode,$checkin, $resourceId, $orderId, $label);
	}

	public static function getSlug($string) {
		$s = array();
		$r = array();
		$s[0] = "/\&/";
		$r[0] = "and";
		$s[1] = '/[^a-z0-9-]/';
		$r[1] = '-';
		$s[2] = '/-+/';
		$r[2] = '-';
		$string = preg_replace( $s, $r, strtolower( trim( $string ) ) );
		return $string;
    }

	
	public static function getLanguage($xml, $langCode, $fallbackCode = 'en-gb', $opts = array() ) {
		if (!isset($xml)) {
			return '';
		}
		$retVal = $xml;
		if (strpos($xml,'<languages>') !== false) {
			if ($fallbackCode == null || !isset($fallbackCode)) {
				$fallbackCode = self::$defaultFallbackCode;
			}
			$langCode = strtolower($langCode);
			$fallbackCode = strtolower($fallbackCode);
			if (strlen ($langCode) > 2) {
				$langCode = substr($langCode,0,2);
			}
			if (strlen ($fallbackCode) > 2) {
				$fallbackCode = substr($fallbackCode,0,2);
			}
			$xml = self::stripInvalidXml($xml);
			$xdoc = new SimpleXmlElement($xml);
			$item = $xdoc->xpath("language [@code='" . $langCode . "']");
			$result = '';
			$retVal = '';
			if(!empty($item)){
				$result = (string)$item[0];
			}
			if (($result == '') && $fallbackCode != '') {
				$item = $xdoc->xpath("language [@code='" . $fallbackCode . "']");
			}
			if(!empty($item)){
				$retVal = (string)$item[0];
			}
			//$retVal = (string)$item[0];
		}

		if (isset($opts) && count($opts) > 0) {
			foreach ($opts as $key => $opt) {
				switch (strtolower($key)) {
					case 'ln2br':
						$retVal = nl2br($retVal, true);
						break;
					case 'htmlencode':
						$retVal = htmlentities($retVal, ENT_COMPAT);
						break;
					case 'striptags':
						$retVal = strip_tags($retVal, "<br><br/>");
						break;
					case 'nomore1br':
						$retVal = preg_replace("/\n+/", "\n", $retVal);
						break;
						
					default:
						break;
				}
			}
		}

		return $retVal;
	}
/**
 * Removes invalid XML
 *
 * @access public
 * @param string $value
 * @return string
 */
	public static function stripInvalidXml($value)
{
    $ret = "";
    $current;
    if (empty($value)) 
    {
        return $ret;
    }

    $length = strlen($value);
    for ($i=0; $i < $length; $i++)
    {
        $current = ord($value{$i});
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x20) && ($current <= 0xD7FF)) ||
            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) && ($current <= 0x10FFFF)))
        {
            $ret .= chr($current);
        }
        else
        {
            $ret .= " ";
        }
    }
    return $ret;
}	
	public static function getQuotedString($str){
		if (isset($str) && $str!=null){
			return '\'' . $str . '\'';	
			//return '\'' . str_replace('%27', '\'', $str) . '\'';	
		}	
		return null;
	}
	
	public static function getJsonEncodeString($str){
		if (isset($str) && $str!=null){
			return json_encode($str);
		}
		return null;
		
	}
		
	public static function parseJsonDate($date, $format = 'd/m/Y') { 
		date_default_timezone_set('UTC');
		//preg_match( '/([\d]{13})/', $date, $matches); 
		preg_match( '/(\-?)([\d]{9,})/', $date, $matches);
		// Match the time stamp (microtime) and the timezone offset (may be + or -)     
		$formatDate = 'd/m/Y';
		if (isset($format) && $format!=""){
			$formatDate = $format;
		}
		$date = date($formatDate, $matches[1].$matches[2]/1000 ); // convert to seconds from microseconds      
		return $date;
	}

	public static function parseArrayList($stringList, $fistDelimiter = ';', $secondDelimiter = '|'){
		$a = array();
		if(!empty($stringList)){
			foreach (explode($fistDelimiter, $stringList) as $aa) {
				list ($cKey, $cValue) = explode($secondDelimiter, $aa, 2);
				$a[$cKey] = $cValue;
			}
		}
		return $a;
	}
	
	public static function getDefaultCheckMode() {
		return self::$defaultCheckMode;
	}
	
	public static function getImagePath($type) {		
		return self::$image_paths[$type];
	}
	
	public static function getImageUrlResized($type, $path = null, $resizedpath = null ) {
		if ($path == '' || $path===null)
			return '';
		$finalPath = self::$image_basePathCDN . COM_BOOKINGFORCONNECTOR_IMGURL;
		if (isset($type) && isset(self::$image_paths[$type])) {
			$finalPath .= self::$image_paths[$type] ;
			if (!empty($resizedpath)) {
					$pathfilename = basename($path);
					if (isset(self::$image_path_resized[$resizedpath])) {
						$path = str_replace($pathfilename, self::$image_path_resized[$resizedpath] . "/".$pathfilename ,$path);
						
						//$finalPath .= self::$image_path_resized[$resizedpath] . "/";
					} else {
						$path = str_replace($pathfilename, $resizedpath . "/".$pathfilename ,$path);
						//$finalPath .= $resizedpath . "/";
					}

			}

			$finalPath .= $path;
				
		}
				
		return $finalPath;
	}

	public static function getImageUrl($type, $path = null, $resizepars = null ) {
		if ($path == '' || $path===null)
			return '';
		$finalPath = self::$image_basePath;
		if (isset($type) && isset(self::$image_paths[$type])) {
			$finalPath .= self::$image_paths[$type] . $path;
			if (isset($resizepars)) {
				// resize params manually added
				if (is_array($resizepars)) {
					$params = '';
					foreach ($resizepars as $param) {
						if ($params=='') 
							$params .= '?';
						else
							$params .= '&';
						$params .= $param;
					}
					if ($params!='') {
						$finalPath .= $params;
					}
				} else { // resize params as predefined configuration
					if (isset(self::$image_resizes[$resizepars])) {
						$finalPath .= '?' . self::$image_resizes[$resizepars];
					}
				}
			}
			/*if (isset($resizepars) && isset(self::$image_resizes[$resizepars])){
				$finalPath .= '?' . self::$image_resizes[$resizepars];
			}*/
		}
		
		return $finalPath;
	}
	
	public static function getDefaultParam($param) {
		switch (strtolower($param)) {
			case 'checkin':
				return DateTime::createFromFormat('d/m/Y',self::getStartDate());
				//return new DateTime();
				break;
			case 'checkout':
				$co = DateTime::createFromFormat('d/m/Y',self::getStartDate());
				//$co = new DateTime();
				return $co->modify(self::$defaultDaysSpan);
				break;
			case 'duration':
				return self::$defaultDuration;
				break;
			case 'extras':
				return '';
				break;
			case 'paxages':
				return '';
				break;
			case 'pricetype':
				return '';
				break;
			default:
				break;
		}
	}
	
	/* http://blog.amnuts.com/2011/04/08/sorting-an-array-of-objects-by-one-or-more-object-property/
	 * 
	 * Sort an array of objects.
	 * 
	 * You can pass in one or more properties on which to sort.  If a
	 * string is supplied as the sole property, or if you specify a
	 * property without a sort order then the sorting will be ascending.
	 * 
	 * If the key of an array is an array, then it will sorted down to that
	 * level of node.
	 * 
	 * Example usages:
	 * 
	 * osort($items, 'size');
	 * osort($items, array('size', array('time' => SORT_DESC, 'user' => SORT_ASC));
	 * osort($items, array('size', array('user', 'forname'))
	 * 
	 * @param array $array
	 * @param string|array $properties
	 * 
	 */
	public static function osort(&$array, $properties) {
		if (is_string($properties)) {
			$properties = array($properties => SORT_ASC);
		}
		uasort($array, function($a, $b) use ($properties) {
			foreach($properties as $k => $v) {
				if (is_int($k)) {
					$k = $v;
					$v = SORT_ASC;
				}
				$collapse = function($node, $props) {
					if (is_array($props)) {
						foreach ($props as $prop) {
							$node = (!isset($node->$prop)) ? null : $node->$prop;
						}
						return $node;
					}else {
						return (!isset($node->$props)) ? null : $node->$props;
					}
				};
				$aProp = $collapse($a, $k);
				$bProp = $collapse($b, $k);
				if ($aProp != $bProp) {
					return ($v == SORT_ASC)
						? strnatcasecmp($aProp, $bProp)
						: strnatcasecmp($bProp, $aProp);
				}
			}
			return 0;
		});
	}
	
	public static function AddToFavourites($id) {
		$expire=time()+60*60*24*30;
		$counter = 1;
		$listFav = (string) $id;
		$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
		if (isset($varCook))
		{
			$arr= explode(",", $varCook);
			if ( !self::IsInFavourites($id)){
				array_push($arr, $id);
			}
			$arr = array_filter( $arr );
			$counter = count($arr);
			$listFav = implode(",", $arr);
		}
		$config = JFactory::getConfig();
		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');
		$ok = setcookie(self::$favouriteCookieName, $listFav, $expire, $cookie_path, '');
		//setcookie(self::$favouriteCookieName, $listFav, $expire, $cookie_path, $cookie_domain);
		return $counter;
	}
	
	public static function RemoveFromFavourites($id) {
		$expire=time()+60*60*24*30;
		$listFav = (string) $id;
		$counter = 0;
		$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
		if (isset($varCook))
		{
			$arr= explode(",", $varCook);
			if(($key = array_search($id, $arr)) !== false) {
				unset($arr[$key]);
			}
			$arr = array_filter( $arr );
			$counter = count($arr);
			$listFav = implode(",", $arr);
		}
		$config = JFactory::getConfig();
		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');
		setcookie(self::$favouriteCookieName, $listFav, $expire, $cookie_path, '');
		//setcookie(self::$favouriteCookieName, $listFav, $expire);
		return $counter;
	}
	
	public static function IsInFavourites($id) {
		$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
		if (isset($varCook))
		{
			$arr= explode(",", $varCook);
			return in_array($id, $arr);
		}
		return false;
	}
	
	public static function CountFavourites() {
		$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
		if (isset($varCook))
		{
			$arr= explode(",", $varCook);
			return count(array_filter($arr));
		}
		return 0;
	}
	
	public static function GetFavourites() {
		$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
		if (isset($varCook))
		{
			$arr= explode(",", $varCook);
			return $arr;
		}
		return null;
	}

	public static function setSearchOnSellParamsSession($params) {
		$sessionkey = 'searchonsell.params';
		$pars = array();
		$pars['contractTypeId'] = $params['contractTypeId'];
		$pars['unitCategoryId'] = $params['unitCategoryId'];
		$pars['roomsmin'] = $params['roomsmin'];
		$pars['roomsmax'] = $params['roomsmax'];
		$pars['bedroomsmin'] = $params['bedroomsmin'];
		$pars['bedroomsmax'] = $params['bedroomsmax'];
		$pars['bathsmin'] = $params['bathsmin'];
		$pars['bathsmax'] = $params['bathsmax'];
		$pars['areamin'] = $params['areamin'];
		$pars['areamax'] = $params['areamax'];
		$pars['pricemax'] = $params['pricemax'];
		$pars['pricemin'] = $params['pricemin'];
		$pars['zoneId'] = $params['zoneId'];
		$pars['searchType'] = $params['searchType'];

		if(!empty($params['locationzones'])){
			$pars['locationzones'] = $params['locationzones'];
		}
		if(!empty($params['locationzone'])){
			$pars['locationzone'] = $params['locationzone'];
		}

		$pars['contractTypeId'] = $params['contractTypeId'];
		$pars['cultureCode'] = $params['cultureCode'];
		$pars['merchantId'] = $params['merchantId'];
		$pars['points'] = $params['points'];

		if(!empty($params['filters'])){
			$pars['filters'] = $params['filters'];
		}
		
		$pars['services'] = $params['services'];
		$pars['isnewbuilding'] = $params['isnewbuilding'];
		$pars['randomresult'] = $params['randomresult'];
		$pars['showReserved'] = $params['showReserved'];
		$pars['searchseed'] = $params['searchseed'];
		$pars['searchtypetab'] = BFCHelper::getVar('searchtypetab',-1);
//		$pars['searchtypetab'] = !empty($params['searchtypetab']) ? $params['searchtypetab']: "0";


		$session = JFactory::getSession();
		$session->set($sessionkey, $pars, 'com_bookingforconnector'); 
	}
	
	public static function getSearchOnSellParamsSession() {
		$sessionkey = 'searchonsell.params';
		$session = JFactory::getSession();
		$pars = $session->get($sessionkey, '', 'com_bookingforconnector'); 
		return $pars;
	}
	
	public static function setSearchParamsSession($params) {
		$sessionkey = 'search.params';
		$pars = array();
		
		$pars['checkin'] = $params['checkin'];
		$pars['checkout'] = $params['checkout'];
		$pars['duration'] = $params['duration'];
		$pars['masterTypeId']=0;
		if(!empty($params['masterTypeId'])){
			$pars['masterTypeId'] = $params['masterTypeId'];
		}
		$pars['merchantResults']=0;
		if(!empty($params['merchantResults'])){
			$pars['merchantResults'] = $params['merchantResults'];
		}
		$pars['merchantCategoryId']=0;
		if(!empty($params['merchantCategoryId'])){
			$pars['merchantCategoryId'] = $params['merchantCategoryId'];
		}
		$pars['paxes'] = $params['paxes'];
		$pars['paxages'] = $params['paxages'];
		$pars['zoneId'] = 0;
		if(!empty($params['zoneId'])){
			$pars['zoneId'] = $params['zoneId'];
		}
		$pars['cityId'] = 0;
		if(!empty($params['cityId'])){
			$pars['cityId'] = $params['cityId'];
		}
		$pars['locationzones'] = "";
		if(!empty($params['locationzones'])){
			$pars['locationzones'] = $params['locationzones'];
		}
		$pars['zoneIds'] = "";
		if(!empty($params['zoneIds'])){
			$pars['zoneIds'] = $params['zoneIds'];
		}
		$pars['cultureCode'] = "";
		if(!empty($params['cultureCode'])){
			$pars['cultureCode'] = $params['cultureCode'];
		}
		//$pars['paxages'] = array_fill(0,$pars['paxes'],BFCHelper::$defaultAdultsAge);
		$pars['merchantId'] = $params['merchantId'];
		$pars['filters'] = "";
		if(!empty($params['filters'])){
			$pars['filters'] = $params['filters'];
		}

		$pars['resourceName'] = "";
		if(!empty($params['resourceName'])){
			$pars['resourceName'] = $params['resourceName'];
		}
		$pars['refid'] = "";
		if(!empty($params['refid'])){
			$pars['refid'] = $params['refid'];
		}
		$pars['onlystay'] = 0;
		if(!empty($params['onlystay'])){
			$pars['onlystay'] = $params['onlystay'];
		}

		$pars['pricerange'] = 0;
		if(!empty($params['pricerange'])){
			$pars['pricerange'] = $params['pricerange'];
		}
		$pars['bookableonly'] = 0;
		if(!empty($params['bookableonly'])){
			$pars['bookableonly'] = $params['bookableonly'];
		}
		$pars['condominiumsResults'] = 0;
		if(!empty($params['condominiumsResults'])){
			$pars['condominiumsResults'] = $params['condominiumsResults'];
		}
		$pars['searchtypetab'] = BFCHelper::getVar('searchtypetab',-1);
//		$pars['searchtypetab'] = !empty($params['searchtypetab']) ? $params['searchtypetab']: "0";
		if(!empty($params['availabilitytype'])){
			$pars['availabilitytype'] = $params['availabilitytype'];
		}

		$session = JFactory::getSession();
		$session->set($sessionkey, $pars, 'com_bookingforconnector');
	}
	
	public static function getSearchParamsSession() {
		$sessionkey = 'search.params';
		$session = JFactory::getSession();
		$pars = $session->get($sessionkey, '', 'com_bookingforconnector'); 
		return $pars;
	}
	
	public static function setFilterSearchParamsSession($paramsfilters) {
		$sessionkey = 'search.filterparams';
		$session = JFactory::getSession();
		$session->set($sessionkey, $paramsfilters, 'com_bookingforconnector');
	}
	
	public static function getFilterSearchParamsSession() {
		$sessionkey = 'search.filterparams';
		$session = JFactory::getSession();
		$paramsfilters = $session->get($sessionkey, '', 'com_bookingforconnector');
		return $paramsfilters;
	}

	public static function setEnabledFilterSearchParamsSession($paramsfilters) {
		$sessionkey = 'search.enabledfilterparams';
		$session = JFactory::getSession();
		$session->set($sessionkey, $paramsfilters, 'com_bookingforconnector');
	}
	
	public static function getEnabledFilterSearchParamsSession() {
		$sessionkey = 'search.enabledfilterparams';
		$session = JFactory::getSession();
		$paramsfilters = $session->get($sessionkey, '', 'com_bookingforconnector'); 
		return $paramsfilters;
	}

	public static function setState($stateObj, $key, $namespace = null) {
		if (isset($namespace)) {
			$key = $namespace . '.' . $key;
		}
		self::$currentState[$key] = $stateObj;
	}

	public static function getState($key, $namespace = null) {
		if (isset($namespace)) {
			$key = $namespace . '.' . $key;
		}
		if (isset(self::$currentState[$key])) {
			return self::$currentState[$key];
		}
		return null;
	}
	
	public static function orderBy($a, $b, $ordering, $direction) {
		return ($a->$ordering < $b->$ordering) ? 
		(
			($direction == 'desc') 
				? 1 
				: -1
		) : 
		(
			($a->$ordering > $b->$ordering) 
				?	(
						($direction == 'desc') 
							? -1 
							: 1
				  	) 
				: 0
		);
	}
   public static function orderByStay($a, $b, $direction) {
		return ($a->Resources[0]->TotalPrice < $b->Resources[0]->TotalPrice) ? 
		(
			($direction == 'desc') 
				? 1 
				: -1
		) : 
		(
			($a->Resources[0]->TotalPrice > $b->Resources[0]->TotalPrice) 
				?	(
						($direction == 'desc') 
							? -1 
							: 1
				  	) 
				: 0
		);
	}
	public static function orderByDiscount($a, $b, $direction) {
		return ($a->Resources[0]->TotalPrice - $a->Resources[0]->Price < $b->Resources[0]->TotalPrice - $b->Resources[0]->Price) ? 
		(
			($direction == 'desc') 
				? 1 
				: -1
		) : 
		(
			($a->Resources[0]->TotalPrice - $a->Resources[0]->Price > $b->Resources[0]->TotalPrice - $b->Resources[0]->Price) 
				?	(
						($direction == 'desc') 
							? -1 
							: 1
				  	) 
				: 0
		);	
	}
	public static function orderBySingleDiscount($a, $b, $direction) {
		return ($a->TotalPrice - $a->Price < $b->TotalPrice - $b->Price) ? 
		(
			($direction == 'desc') 
				? 1 
				: -1
		) : 
		(
			($a->TotalPrice - $a->Price > $b->TotalPrice - $b->Price) 
				?	(
						($direction == 'desc') 
							? -1 
							: 1
				  	) 
				: 0
		);	
	}
	
	public static function getStayParam($param, $default= null) {
		$pars = self::getSearchParamsSession();

		switch (strtolower($param)) {
			case 'checkin':
				$strCheckin = BFCHelper::getString('checkin');
				if (($strCheckin == null || $strCheckin == '') && (isset($pars['checkin']) && $pars['checkin'] != null && $pars['checkin'] != '')) {
					return clone $pars['checkin'];
				}
				$checkin = DateTime::createFromFormat('d/m/Y',$strCheckin);
				if ($checkin===false && isset($default)) {
					$checkin = $default;
				}
				return $checkin;
				break;
			case 'checkout':
				$strCheckout = BFCHelper::getString('checkout');
				if (($strCheckout == null || $strCheckout == '') && (isset($pars['checkout']) && $pars['checkout'] != null && $pars['checkout'] != '')) {
					return clone $pars['checkout'];
				}
				$checkout = DateTime::createFromFormat('d/m/Y',$strCheckout);
				if ($checkout===false && isset($default)) {
					$checkout = $default;
				}
				return $checkout;
				break;
			case 'duration':
				$ci = self::getStayParam('checkin', new DateTime());
				$dco = new DateTime();
				$co = self::getStayParam('checkout', $dco->modify('+7 days'));
				$interval = $co->diff($ci);
				return $interval->d;
				break;
			case 'extras':
				$extraVar = BFCHelper::getArray('extras');
				$extras = "";
				if(is_array($extraVar)){
					$extraVar = array_filter($extraVar);
				}
				if (!empty($extraVar) && is_array($extraVar) && count($extraVar)>0 ){
				$extras = implode('|',
					array_filter($extraVar, function($var) {
							$vals = explode(':', $var);
							if (count($vals) < 2 || $vals[1] == '') return false;
							return true;
						})
					);
				}
				return $extras;
				break;
			case 'packages':
				$packagesVar = BFCHelper::getVar('packages');
				$packages = "";
				if (!empty($packagesVar)){
				$packages = implode('|',
					array_filter($packagesVar, function($var) {
							$vals = explode(':', $var);
							if (count($vals) < 3 || $vals[2] == 0 || $vals[1] == 0) return false;
							return true;
						})
					);
				}
				return $packages;
				break;				
			case 'selectedprices':
				$extras = implode('|',
					array_filter(BFCHelper::getVar('extras'), function($var) {
						$vals = explode(':', $var);
						if (count($vals) < 2 || $vals[1] == '') return false;
						return true;
					})
				);
				return $extras;
				break;
			case 'paxages':
				$adults = BFCHelper::getVar('adults');
				$children = BFCHelper::getVar('children');
				$seniores = BFCHelper::getVar('seniores');
				if (($adults == null || $adults == '') && ($children == null || $children == '') && (isset($pars['paxages']) && $pars['paxages'] != null && $pars['paxages'] != '')) {
					return array_slice($pars['paxages'],0);
				}
				$adults = BFCHelper::getInt('adults', self::$defaultAdultsQt);
				$children = BFCHelper::getInt('children',0);
				$seniores = BFCHelper::getInt('seniores',0);
				$strAges = array();
				for ($i = 0; $i < $adults; $i++) {
					$strAges[] = self::$defaultAdultsAge;
				}
				for ($i = 0; $i < $seniores; $i++) {
					$strAges[] = self::$defaultSenioresAge;
				}
				if ($children > 0) {
					for ($i = 0;$i < $children; $i++) {
						$age = BFCHelper::getInt('childages'.($i+1));
						if ($age < self::$defaultAdultsAge) {
							$strAges[] = $age;
						}
					}
				}
				return $strAges;
				break;
			case 'pricetype':
				return BFCHelper::getInt('pricetype',0);
				break;
			case 'pricerange':
				return BFCHelper::getVar('pricerange','0');
				break;
			case 'bookableonly':
				return BFCHelper::getVar('bookableonly','0');
				break;
			case 'rateplanid':
				return BFCHelper::getVar('pricetype');
				break;
			case 'variationplanid':
				return BFCHelper::getVar('variationPlanId','');
				break;				
			case 'state':
				return BFCHelper::getVar('state');
			default:
				break;
		}
	}
	
	public static function convertTotal($x){
		switch($x){
			case $x < 3:
				$y = 0;
				break;
			case $x < 4:
				$y = 1;
				break;
			case $x < 5:
				$y = 2;
				break;
			case $x <= 5.5:
				$y = 3;
				break;
			case $x < 6:
				$y = 4;
				break;
			case $x < 7:
				$y = 5;
				break;
			case $x < 8:
				$y = 6;
				break;
			case $x <= 8.5:
				$y = 7;
				break;
			case $x < 9:
				$y = 8;
				break;
			case $x < 10:
				$y = 9;
				break;
			case $x == 10:
				$y = 10;
				break;
			default:
				$y = 4;
				break;
		}
		return $y;
	}

// - funzione di criptazione/decriptazione basato su una chiave
	public static function encrypt($string,$key=null) {
        //Key 
        if(empty($key)){
			$key = COM_BOOKINGFORCONNECTOR_KEY;
		}
 		$key = str_pad($key, 24, "\0");  
                 
        //Encryption
        $cipher_alg = MCRYPT_TRIPLEDES;
    
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
         
 
        $encrypted_string = mcrypt_encrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
        return base64_encode($encrypted_string).$key;
//        return $encrypted_string;
    }
    
   public static function decrypt($string,$urldecode = false,$key=null) {
		    if ($urldecode) {
				$string = urldecode($string);
			}
			
            $string = base64_decode($string);
 
            //key 
			if(empty($key)){
				$key = COM_BOOKINGFORCONNECTOR_KEY;
			}
			$key = str_pad($key, 24, "\0");               
 
            $cipher_alg = MCRYPT_TRIPLEDES;
 
            $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
             
 
            $decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
            return trim($decrypted_string);
    }

//		public static function getImgPathSession() {
//		$sessionkey = 'imgpath.params';
//		$session = JFactory::getSession();
//		$path = $session->get($sessionkey, '', 'com_bookingforconnector'); 
//		if(empty($path)){
//			JLoader::import('joomla.application.component.helper');
//			$params = JComponentHelper::getParams('com_bookingforconnector');
//			$path= $params->get('imgurl', '');
//			$session->set($sessionkey, $path, 'com_bookingforconnector'); 
//		}
//		return $path;
//	}

	public static function getOrderMerchantPaymentId($order) {
		if(!empty($order)){
			$bookingTypeId = self::getItem($order->NotesData, 'bookingTypeId');
			if ($bookingTypeId!=''){
				return $bookingTypeId;
			}
			$bookingTypeId = self::getItem($order->NotesData, 'merchantBookingTypeId');
			if ($bookingTypeId!=''){
				return $bookingTypeId;
			}
		}
		return null;
	}
	
	public static function getCriteoConfiguration($pagetype = 0, $merchantsList = array(), $orderId = null) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Criteo', 'BookingForConnectorModel');
		return $model->getCriteoConfiguration($pagetype, $merchantsList,$orderId);
	}
	
	public static function calculate_paxages($post, $adults = NULL, $children = NULL, $seniores = NULL) {
		$seniores = isset($seniores) ? $seniores : 0;
		$adults = isset($adults) ? $adults : BFCHelper::$defaultAdultsQt;
		$children = isset($children) ? $children : 0;
		$strAges = array();
		for ($i = 0; $i < $adults; $i++) {
		  $strAges[] = BFCHelper::$defaultAdultsAge;
		}
		for ($i = 0; $i < $seniores; $i++) {
		  $strAges[] = BFCHelper::$defaultSenioresAge;
		}
		if ($children > 0) {
		  for ($i = 0;$i < $children; $i++) {
			$age = $post['childages'.($i+1)];
			if($age == NULL) {
			  $age = 0;
			}
			 if ($age < BFCHelper::$defaultAdultsAge) {
			   $strAges[] = $age;
			 }
		  }
		}
		return $strAges;
	}

	public static function ConvertIntTimeToDate($timeMinEnd)
	{
            $returnDateTime = new DateTime(1, 1, 1);
            if ($timeMinEnd > 0)
            {
                $hour = $timeMinEnd / 10000;
                $minute = ($timeMinEnd - hour * 10000) / 100;
                $returnDateTime->modify('+{$hour} hours');
                $returnDateTime->modify('+{$minute} minutes');
            }
            return $returnDateTime;
	}
	public static function ConvertIntTimeToMinutes($timeMin)
	{
            $returnMinute =0;
            if ($timeMin > 0)
            {
                $hour = $timeMin / 10000;
                $minute = ($timeMin - $hour * 10000) / 100;
				$returnMinute = $hour* 60 + $minute;
            }
            return $returnMinute;
	}

	public static function shorten_string($string, $amount)
	{
		 if(strlen($string) > $amount)
		{
			$string = trim(substr($string, 0, $amount))."...";
		}
		return $string;
	}



	
	public static function checkAnalytics($listName) {
		if(!self::$justloaded){
			self::$justloaded = true;
			$writeJs = true;
			if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
			   $writeJs = false;
			}
			$document 	= JFactory::getDocument();
			$config = JComponentHelper::getParams('com_bookingforconnector');
			$gaaccount = $config->get('gaaccount', '');
			if($config->get('gaenabled', 0) == 1 && !empty($gaaccount)) {
				if($writeJs) {
					$document->addScriptDeclaration('
					var bookingfor_gacreated = true;
					var bookingfor_eeccreated = false;
					var bookingfor_gapageviewsent = 0;
					if(!window.ga) {
						(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})(window,document,"script","https://www.google-analytics.com/analytics.js","ga");
						ga("create","' . $config->get('gaaccount') . '"' . (strpos(JURI::current(), 'localhost') !== false ? ', {
						  "cookieDomain": "none"
						}' : ', "auto"') . ');
					}');
				}
				if($config->get('eecenabled', 0) == 1) {
					if($writeJs) {
						$document->addScriptDeclaration('
						ga("require", "ec");
						bookingfor_eeccreated = true;
						
						function initAnalyticsBFEvents() {
							jQuery("body").on("click", "#grid-view, #list-view", function(e) {
								if(e.originalEvent) {
									//var listname = "' . $listName . ' - " + (jQuery(this).attr("id") == "grid-view" ? "Grid View" : "List View");
									callAnalyticsEEc("", "", (jQuery(this).attr("id") == "grid-view" ? "GridView" : "ListView"), null, "changeView", "View&Sort");
								}
							});
							jQuery("body").on("click", ".com_bookingforconnector-sort-item", function(e){
								if(e.originalEvent) {
									var listname = "OrderBy";
									var sortType = "";
									switch(jQuery(this).attr("rel").split("|")[0].toLowerCase()) {
										case "reviewvalue":
											sortType = "GuestRating";
											break;
										case "stay":
										case "price":
											sortType = "Price";
											break;
										case "offer":
											sortType = "Offer";
											break;
										case "addedon":
											sortType = "AddedDate";
											break;
										case "name":
											sortType = "Name";
											break;
									}
									if(!jQuery.trim(sortType).length) { return; }
									listname += sortType;
									callAnalyticsEEc("", "", listname, null, "changeSort", "View&Sort");
								}
							});
							jQuery("body").on("mouseup", ".eectrack", function(e) {
								if( e.which <= 2 ) {
									callAnalyticsEEc("addProduct", [{
										id: jQuery(this).attr("data-id") + " - " + jQuery(this).attr("data-type"),
										name: jQuery(this).attr("data-itemname"), 
										category: jQuery(this).attr("data-category"),
										brand: jQuery(this).attr("data-brand"), 
										//variant: jQuery(this).attr("data-type"),
										position: parseInt(jQuery(this).attr("data-index")), 
									}], "viewDetail", null, jQuery(this).attr("data-id"), jQuery(this).attr("data-type"));
								}
							});
						}
						
						function callAnalyticsEEc(type, items, actiontype, list, actiondetail, itemtype) {
							list = list && jQuery.trim(list).length ? list : "' . $listName . '";
							switch(type) {
								case "addProduct":
									if(!items.length) { return; }
									jQuery.each(items, function(i, itm) {
										ga("ec:addProduct", itm);
									});
									break;
								case "addImpression":
									if(!items.length) { return; }
									jQuery.each(items, function(i, itm) {
										itm.list = list;
										ga("ec:addImpression", itm);
									});
									break;
							}
							
							switch(actiontype.toLowerCase()) {
								case "click":
									ga("ec:setAction", "click", {"list": list});
									ga("send", "event", "Bookingfor", "click", list);
									break;
								case "item":
									ga("ec:setAction", "detail");
									ga("send","pageview");
									bookingfor_gapageviewsent++;
									break;
								case "checkout":
								case "checkout_option":
									ga("ec:setAction", actiontype, actiondetail);
									ga("send","pageview");
									bookingfor_gapageviewsent++;
									break;
								case "addtocart":
									ga("set", "&cu", "EUR");
									ga("ec:setAction", "add", actiondetail);
									ga("send", "event", "Bookingfor - " + itemtype, "click", "addToCart");
									bookingfor_gapageviewsent++;
									break;
								case "purchase":
									ga("set", "&cu", "EUR");
									ga("ec:setAction", "purchase", actiondetail);
									ga("send","pageview");
									bookingfor_gapageviewsent++;
								case "list":
									ga("send","pageview");
									bookingfor_gapageviewsent++;
									break;
								default:
									ga("ec:setAction", "click", {"list": list});
									ga("send", "event", "Bookingfor - " + itemtype, actiontype, actiondetail);
									break;
							}
						}
						
						jQuery(function(){
							initAnalyticsBFEvents();
						});
						
						');
					}
				}
				return true;
			}
			return false;
		}

		$config = JComponentHelper::getParams('com_bookingforconnector');
		$gaaccount = $config->get('gaaccount', '');
		if($config->get('gaenabled', 0) == 1 && !empty($gaaccount)) {
			return true;
		}
		return false;
	}

	public static function getVar($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
//		return $jinput->get($string, $defaultValue);
		return $jinput->getString($string, $defaultValue);
	}
	public static function getInt($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
//		return $jinput->get($string, $defaultValue,'INT');
		return $jinput->getInt($string, $defaultValue);
	}
	public static function getCmd($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
		return $jinput->getCmd($string, $defaultValue);
	}
	public static function getFloat($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
		return $jinput->getFloat($string, $defaultValue);
	}
	public static function getString($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
		return $jinput->getString($string, $defaultValue);
	}
	public static function getBool($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
		return $jinput->getBool($string, $defaultValue);
	}
	public static function getArray($string, $defaultValue=null) {
		$jinput = JFactory::getApplication()->input;
		$result =$jinput->getArray(array($string => ''));
		return $result[$string];
	}

	public static function raiseWarning($code, $string) {
		JFactory::getApplication()->enqueueMessage("Error code: " .$code .'\n'. $string, 'warning');
	}

	public static function setVar($string, $value) {
		$jinput = JFactory::getApplication()->input;
		$jinput->post->set($string, $value);
	}

//	public static function setCookie($cookieName, $cookieValue, $lifetime) {
//		$app = JFactory::getApplication();
//		$app->input->cookie->set($cookieName, $cookieValue, time() + $lifetime, $app->get('cookie_path', '/'), $app->get('cookie_domain'), $app->isSSLConnection());
//		$jinput->cookie->set($string, $value);
//	}

	public static function getCookie($cookieName, $defaultValue=null) {
		$app = JFactory::getApplication();
		$cookieValue = $app->input->cookie->get($cookieName, $defaultValue);
		return $cookieValue;
	}

	public static function getSession($string, $defaultValue=null, $prefix ='') {
		if(!defined(COM_BOOKINGFORCONNECTOR_ENABLECACHE)) return null;
//		return isset($_SESSION[$prefix.$string]) ? $_SESSION[$prefix.$string] : $defaultValue;
		$session = JFactory::getSession();
		return $session->get($string, $defaultValue , $prefix);

	}
	public static function setSession($string, $value=null, $prefix ='') {
		$session = JFactory::getSession();
		$session->set($string, $value, $prefix);
//		$_SESSION[$prefix.$string] = $value;
	}

	public static function getAppication($string, $defaultValue=null) {
		$app  = JFactory::getApplication();
		return $app ->get($string, $defaultValue);

	}
	public static function setAppication($string, $value=null) {
		$app  = JFactory::getApplication();
		$app ->set($string, $value);
	}

	public static function pushStay($arr, $resourceid, $resStay, $defaultResource = null) {
		$selected = array_values(array_filter($arr, function($itm) use ($resourceid) {
			return $itm->ResourceId == $resourceid;
		}));
		$index = 0;
		if(count($selected) == 0) {
			$obj = new stdClass();
			$obj->ResourceId = $resourceid;
			
			if(isset($defaultResource) && $defaultResource->ResourceId == $resourceid) {
				$obj->MinCapacityPaxes = $defaultResource->MinCapacityPaxes;
				$obj->MaxCapacityPaxes = $defaultResource->MaxCapacityPaxes;
				$obj->Name = $defaultResource->Name;
				$obj->ImageUrl = $defaultResource->ImageUrl;
				$obj->Availability = $defaultResource->Availability;
				$obj->AvailabilityType = $defaultResource->AvailabilityType;
				$obj->Policy = $resStay->Policy;
			} else {
				$obj->MinCapacityPaxes = $resStay->MinCapacityPaxes;
				$obj->MaxCapacityPaxes = $resStay->MaxCapacityPaxes;
				$obj->Availability = $resStay->Availability;
				$obj->AvailabilityType = $resStay->AvailabilityType;
				$obj->Name = $resStay->ResName;
				$obj->ImageUrl = $resStay->ImageUrl;
				$obj->Policy = $resStay->Policy;
				$obj->TimeLength = $resStay->TimeLength;
			}
			$obj->RatePlans = array();
			//$obj->Policy = $completestay->Policy;
			//$obj->Description = $singleRateplan->Description;
			$arr[] = $obj;
			$index = count($arr) - 1;
		} else {
			$index = array_search($selected[0], $arr);
			//$obj = $selected[0];
		}

		$rt = new stdClass();
		$rt->RatePlanId = $resStay->RatePlanId;
		$rt->Name = $resStay->Name;	
		$rt->RatePlanRefId = isset($resStay->RefId) ? $resStay->RefId : "";	
		$rt->PercentVariation = $resStay->PercentVariation;	
		
		$rt->TotalPrice=0;
		$rt->TotalPriceString ="";
		$rt->Days=0;
		$rt->BookingType=$resStay->BookingType;
		$rt->IsBookable=$resStay->IsBookable;
		$rt->CheckIn = BFCHelper::parseJsonDate($resStay->CheckIn); 
		$rt->CheckOut= BFCHelper::parseJsonDate($resStay->CheckOut);

		$rt->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
		$rt->SelectablePrices = $resStay->SelectablePrices;
		$rt->Variations = $resStay->Variations;
		$rt->SimpleDiscountIds = implode(',', $resStay->SimpleDiscountIds);	
		if(!empty($resStay->SuggestedStay->DiscountedPrice)){
			$rt->TotalPrice = (float)$resStay->SuggestedStay->TotalPrice;
			$rt->TotalPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->TotalPrice);
			$rt->Days = $resStay->SuggestedStay->Days;
			$rt->DiscountedPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->DiscountedPrice);
			$rt->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
		}
		
		$arr[$index]->RatePlans[] = $rt;
		
		return $arr;
	}

	public static function ParsePriceParameter($str)
        {
			$array = explode(':',$str);
			$newarray = array(
                "PriceId" => intval($array[0]),
                "ProductId" => intval($array[0]),
                "Quantity" =>intval($array[1]),
                "CheckInDateTime" => count($array) > 2 && !empty($array[2]) ? DateTime::createFromFormat("YmdHis", $array[2]) : null,
                "PeriodDuration" => count($array) > 3 && !empty($array[3]) ? intval($array[3]) : 0,
                "TimeSlotId" => count($array) > 4 && !empty($array[4]) ? intval($array[4]) : 0,
                "TimeSlotStart" => count($array) > 5 && !empty($array[5]) ? intval($array[5]) : 0,
                "TimeSlotEnd" => count($array) > 6 && !empty($array[6]) ? intval($array[6]) : 0,
                "TimeSlotDate" => count($array) > 7 && !empty($array[7]) ? DateTime::createFromFormat("Ymd", $array[7]) : null,
                "CheckInDate" => count($array) > 8 && !empty($array[8]) ? DateTime::createFromFormat("Ymd", $array[8]) : null,
                "CheckOutDate" => count($array) > 9 && !empty($array[9]) ? DateTime::createFromFormat("Ymd", $array[9]) : null,
                "Configuration" => $str
            );
            return $newarray;
        }
	
	public static function GetPriceParameters($selectablePrices)
        {
			$priceParameters = array();
			if (empty($selectablePrices)) {
				return $priceParameters;
			}
            $priceParametersArray = explode('|', $selectablePrices);
			if(!empty($priceParametersArray)){
                foreach ($priceParametersArray as $s)
                {
					array_push($priceParameters, BFCHelper::ParsePriceParameter($s));
				}
			}
			return $priceParameters;
        }
		
		public static function calculateOrder($OrderJson,$language,$bookingType = "") {
			$orderModel = json_decode($OrderJson);
			$order = new stdClass;
			$DateTimeMinValue = new DateTime();
			$DateTimeMinValue->setDate(1, 1, 1);

			$orderModel->SearchModel->FromDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkin);
			$orderModel->SearchModel->ToDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkout);
			$orderModel->SearchModel->FromDate->setTime(0,0,0);
			$orderModel->SearchModel->ToDate->setTime(0,0,0);


            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 && $orderModel->SearchModel->FromDate != $DateTimeMinValue)
            {
                $order->Resources = array();
                $resourceDetail = null;
                
				foreach ($orderModel->Resources as $resource)
                {
					$resourceDetail = BFCHelper::GetResourcesById($resource->ResourceId);
                    $order->MerchantId = $resourceDetail->MerchantId;
                    $services = "";
					
					$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
					if(!empty($servicesArray)){
						$services = implode("|",$servicesArray);
					}
					$currservices = BFCHelper::GetPriceParameters($services);
                    $selectablePrices = array_filter($currservices, function($t) {return $t["Quantity"] > 0;});
									
					$currModel = clone $orderModel;
                    $currModel->SearchModel->MerchantId = $resourceDetail->MerchantId;
                    $currModel->SearchModel->ProductAvailabilityType = $resourceDetail->AvailabilityType;
                    $duration = 1;

					if ($resourceDetail->AvailabilityType== 2)
                    {
                        $duration = $resource->TimeDuration;
                        $currModel->SearchModel->FromDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
                        $currModel->SearchModel->ToDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
                                              
						$currModel->SearchModel->ToDate->modify('+1 day');
                    }
                    if ($resourceDetail->AvailabilityType== 3)
                    {
                        $currModel->SearchModel->ToDate = clone $currModel->SearchModel->FromDate;
                        $currModel->SearchModel->ToDate->modify('+1 day');
                    }
					if($resourceDetail->AvailabilityType != 3 && $resourceDetail->AvailabilityType != 2){
						$duration = $currModel->SearchModel->ToDate->diff($currModel->SearchModel->FromDate)->format('%a');
					}

					if ($resourceDetail->AvailabilityType == 0)
					{
						$duration +=1; 
					}

					$paxages = array();
					for ($i=0;$i<$currModel->SearchModel->AdultCount ; $i++)	
					{
						array_push($paxages, "18");
					}
					for ($i=0;$i<$currModel->SearchModel->SeniorCount ; $i++)	
					{
						array_push($paxages, "65");
					}
					$nchsarray = array($currModel->SearchModel->childages1,$currModel->SearchModel->childages2,$currModel->SearchModel->childages3,$currModel->SearchModel->childages4,$currModel->SearchModel->childages5,null);
					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
					{
						array_push($paxages, $nchsarray[$i]);
					}
//					$paxages = implode("|",$paxages);
										
					$packages =null;
					$pricetype = !empty($resource->RatePlanId)?$resource->RatePlanId:"";
					$ratePlanId = $pricetype;
					$variationPlanId = "";

					$listRatePlans = BFCHelper::GetCompleteRatePlansStayWP($resource->ResourceId,$currModel->SearchModel->FromDate,$duration,$paxages,$services,$packages,$pricetype,$ratePlanId,$variationPlanId,$language, $bookingType, true);
					if (!empty($listRatePlans) && is_array($listRatePlans)){
						$listRatePlans = array_filter($listRatePlans, function($l)  {return ($l->TotalAmount>0 && !empty($l->SuggestedStay)  && $l->SuggestedStay->Available ) ;});
						
						if (!empty($resource->RatePlanId))
						{
												
							$listRatePlans =  array_filter($listRatePlans, function($l) use ($resource) {return $l->RatePlanId == $resource->RatePlanId ;}); // c#: allRatePlans.Where(p => p.ResourceId == resId);
						}
						else
						{							
							$listRatePlansGrouped = array();
							$tmpLlistRatePlansGrouped = array();
							foreach ($listRatePlans as $data) {
								$id = $data->SuggestedStay->BookingType;
								if (isset($listRatePlansGrouped[$id])) {
									$listRatePlansGrouped[$id][] = $data;
								} else {
									$listRatePlansGrouped[$id] = array($data);
								}
							}
							foreach ($listRatePlansGrouped as $ratePlansGrouped) {
								usort($ratePlansGrouped, "BFCHelper::bfi_sortRatePlans");
								$tmpLlistRatePlansGrouped[] = reset($ratePlansGrouped);
							}

							$listRatePlans = $tmpLlistRatePlansGrouped;


							$selRatePlan = reset($listRatePlans);

						}
						foreach ($listRatePlans as $selRatePlan)
						{

							if (!empty($selRatePlan))
							{
								
								//$order->BookingType = $selRatePlan->SuggestedStay->BookingType;
								for ($i = 0; $i < $resource->SelectedQt; $i++)
								{
									$lstExtraServices = array();
									$lstPriceSimpleResult = json_decode($selRatePlan->CalculatedPricesString); 
									$lstPriceSimpleResult = array_filter($lstPriceSimpleResult, function($c) use ($resource) {return $c->RelatedProductId != $resource->ResourceId ;});
									$lstPriceSimpleResultGrouped = array();
									foreach ($lstPriceSimpleResult as $data) {
									  $id = $data->RelatedProductId;
									  if (isset($lstPriceSimpleResultGrouped[$id])) {
										 $lstPriceSimpleResultGrouped[$id][] = $data;
									  } else {
										 $lstPriceSimpleResultGrouped[$id] = array($data);
									  }
									}
									foreach ($lstPriceSimpleResultGrouped as $pricesKey => $prices ){
										$resInfo = reset($prices);

										$resInfoRequest = current(array_filter($selectablePrices, function($c) use ($pricesKey) {return $c["ProductId"] == $pricesKey;}));

										$CalculatedQt = 0;
										$TotalAmount = 0;
										$TotalDiscounted = 0;
										foreach ($prices as $item) {
											$CalculatedQt += $item->CalculatedQt;
											$TotalAmount += $item->TotalAmount;
											$TotalDiscounted += $item->TotalDiscounted;
										}
										$currSelectedService = new stdClass;                        
										$currSelectedService->PriceId = $pricesKey;
										$currSelectedService->CalculatedQt = $CalculatedQt;
										$currSelectedService->ResourceId = $pricesKey;
										$currSelectedService->Name = $resInfo->Name;
										$currSelectedService->TotalAmount = $TotalAmount;
										$currSelectedService->TotalDiscounted = $TotalDiscounted;
										$currSelectedService->TimeSlotDate = !empty($resInfoRequest["TimeSlotDate"]) ? $resInfoRequest["TimeSlotDate"]->format('d/m/Y') : ""; //$resInfoRequest["TimeSlotDate"];
										$currSelectedService->TimeSlotStart = $resInfoRequest["TimeSlotStart"];
										$currSelectedService->TimeSlotEnd = $resInfoRequest["TimeSlotEnd"];
										$currSelectedService->TimeSlotId = $resInfoRequest["TimeSlotId"];
										$currSelectedService->CheckInTime = !empty($resInfoRequest["CheckInDateTime"]) ? $resInfoRequest["CheckInDateTime"]->format('YmdHis') : "";
										$currSelectedService->TimeDuration = !empty($resInfoRequest["PeriodDuration"]) ? $resInfoRequest["PeriodDuration"] : "";

										array_push($lstExtraServices, $currSelectedService);
									}
									
									$calPricesResources = json_decode($selRatePlan->CalculatedPricesString);
									$calPricesResources = array_filter($calPricesResources, function($c) use ($resource) {return $c->RelatedProductId == $resource->ResourceId ;});

//									$calPricesResources = array_filter($calPricesResources, function($c) {
//										return $c->Tag == "person" || $c->Tag == "default" || $c->Tag == "" || $c->Tag== "timeslot" || $c->Tag == "timeperiod" ;
//									});
																
									$calPricesResourcesTotalAmount = 0;
									$calPricesResourcesTotalDiscounted = 0;
									foreach ($calPricesResources as $item) {
										$calPricesResourcesTotalAmount += $item->TotalAmount;
										$calPricesResourcesTotalDiscounted += $item->TotalDiscounted;
									}
									$AllVariations = "";

									if(!empty($selRatePlan->AllVariationsString)){
										$allVariationPlanId = array_unique(array_map(function ($i) { return $i->VariationPlanId; }, json_decode($selRatePlan->AllVariationsString)));
										$AllVariations = implode(",",$allVariationPlanId);

									}


									$SelectedResource = new stdClass;
									$SelectedResource->ResourceId = $resource->ResourceId;
									$SelectedResource->MerchantId = $resource->MerchantId;
									$SelectedResource->RatePlanId = $resource->RatePlanId;
									$SelectedResource->SelectedQt = $resource->SelectedQt;
									$SelectedResource->TimeSlotId = isset($resource->TimeSlotId)?$resource->TimeSlotId:null;
									$SelectedResource->TimeSlotStart = isset($resource->TimeSlotStart)?$resource->TimeSlotStart:null;
									$SelectedResource->TimeSlotEnd = isset($resource->TimeSlotEnd)?$resource->TimeSlotEnd:null;
									$SelectedResource->CheckInTime = isset($resource->CheckInTime)?$resource->CheckInTime:null;
									$SelectedResource->TimeDuration = isset($resource->TimeDuration)?$resource->TimeDuration:null;
									$SelectedResource->Name = $resourceDetail->Name;
									$SelectedResource->BookingType = $selRatePlan->SuggestedStay->BookingType;
									$SelectedResource->AvailabilityType = $resourceDetail->AvailabilityType;
									$SelectedResource->TotalAmount = $calPricesResourcesTotalAmount;
									$SelectedResource->TotalDiscounted = $calPricesResourcesTotalDiscounted;
									$SelectedResource->ExtraServices = $lstExtraServices;
									$SelectedResource->ExtraServicesValue = $services;
									$SelectedResource->RatePlanName = $selRatePlan->Name;
									$SelectedResource->PercentVariation = $selRatePlan->PercentVariation;
									$SelectedResource->AllVariations = $AllVariations;

									array_push($order->Resources, $SelectedResource);

								}

							}
						}
					}
					$order->TotalAmount = 0;
					$order->TotalDiscountedAmount = 0;
					foreach ($order->Resources as $resource)
					{
						$order->TotalAmount += $resource->TotalAmount;
						$order->TotalDiscountedAmount += $resource->TotalDiscounted ;
						foreach ($resource->ExtraServices as $item) {
							$order->TotalAmount  += $item->TotalAmount;
							$order->TotalDiscountedAmount  += $item->TotalDiscounted;
						}

					}
					$order->SearchModel = $orderModel->SearchModel;

			}
			}

            return $order;
	}
		public static function CreateOrder($OrderJson,$language,$bookingType = "") {
			$totalModel = json_decode(stripslashes($OrderJson));
			$lstOrderStay = array();
			$DateTimeMinValue = new DateTime();
			$DateTimeMinValue->setDate(1, 1, 1);
            foreach ($totalModel as $orderModel)
            {
				
			if(isset($orderModel->SearchModel->checkin)){
				$orderModel->SearchModel->FromDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkin);
			}else{
				$orderModel->SearchModel->FromDate = new DateTime($orderModel->SearchModel->FromDate );
			}
			if(isset($orderModel->SearchModel->checkout)){
				$orderModel->SearchModel->ToDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkout);
			}else{
				$orderModel->SearchModel->ToDate = new DateTime($orderModel->SearchModel->ToDate );
			}

			$orderModel->SearchModel->FromDate->setTime(0,0,0);
			$orderModel->SearchModel->ToDate->setTime(0,0,0);

//				echo "<pre>";
//				echo print_r($orderModel->SearchModel);
//				echo "</pre>";
//				die();

            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 && $orderModel->SearchModel->FromDate != $DateTimeMinValue)
            {
//                $resourceDetail = null;
                foreach ($orderModel->Resources as $resource)
                {
//                    $resourceDetail = BFCHelper::GetResourcesById($resource->ResourceId);
//                    $order->MerchantId = $resourceDetail->MerchantId;
                    $services = $resource->ExtraServicesValue;
					
//					$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
//					if(!empty($servicesArray)){
//						$services = implode("|",$servicesArray);
//					}
//					$currservices = BFCHelper::GetPriceParameters($services);
//                    $selectablePrices = array_filter($currservices, function($t) {return $t["Quantity"] > 0;});
									
					$currModel = clone $orderModel;
                    $currModel->SearchModel->MerchantId = $resource->MerchantId;
                    $currModel->SearchModel->ProductAvailabilityType = $resource->AvailabilityType;
                    $duration = 1;

					if ($resource->AvailabilityType== 2)
                    {
                        $duration = $resource->TimeDuration;
                        $currModel->SearchModel->FromDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
                        $currModel->SearchModel->ToDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
                        $currModel->SearchModel->ToDate->modify('+1 day');
                    }
                    if ($resource->AvailabilityType== 3)
                    {
                        $currModel->SearchModel->ToDate = clone $currModel->SearchModel->FromDate;
                        $currModel->SearchModel->ToDate->modify('+1 day');
                    }
					if($resource->AvailabilityType != 3 && $resource->AvailabilityType != 2){
						$duration = $currModel->SearchModel->ToDate->diff($currModel->SearchModel->FromDate)->format('%a');
					}

					$paxages = array();
					for ($i=0;$i<$currModel->SearchModel->AdultCount ; $i++)	
					{
						array_push($paxages, "18");
					}
					for ($i=0;$i<$currModel->SearchModel->SeniorCount ; $i++)	
					{
						array_push($paxages, "65");
					}
//					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
////					$paxages = implode("|",$paxages);
					$nchsarray = array($currModel->SearchModel->childages1,$currModel->SearchModel->childages2,$currModel->SearchModel->childages3,$currModel->SearchModel->childages4,$currModel->SearchModel->childages5,null);
					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
					{
						array_push($paxages, $nchsarray[$i]);
					}  										
					$packages =null;
					$pricetype = !empty($resource->RatePlanId)?$resource->RatePlanId:"";
					$ratePlanId = $pricetype;
					$variationPlanId = "";
					

					$stay = BFCHelper::GetCompleteRatePlansStayWP($resource->ResourceId,$currModel->SearchModel->FromDate,$duration,$paxages,$services,$packages,$pricetype,$ratePlanId,$variationPlanId,$language, $bookingType , false);
					if (!empty($stay) && is_array($stay)){
						$stay = reset($stay);
					}
					if (!empty($stay) && !empty($stay->SuggestedStay))
					{
						for ($i = 0; $i < $resource->SelectedQt; $i++)
						{						
							$order = new stdClass;

							$order->Availability = $stay->SuggestedStay->Availability;
							$order->Available = $stay->SuggestedStay->Available;
							$order->MerchantBookingTypeId = intVal($bookingType);
							$order->CheckIn = $stay->SuggestedStay->CheckIn;
							$order->CheckOut = $stay->SuggestedStay->CheckOut;
							$order->Days = $stay->SuggestedStay->Days;
							$order->DiscountDescription = $stay->SuggestedStay->DiscountDescription;
							$order->DiscountId = $stay->SuggestedStay->DiscountId;
							$order->MerchantId = $resource->MerchantId;
							$order->Extras = $stay->SuggestedStay->Extras;
							$order->ExtrasDiscount = $stay->SuggestedStay->ExtrasDiscount;
							$order->HolidayDiscount = $stay->SuggestedStay->HolidayDiscount;
							$order->HolidayPrice = $stay->SuggestedStay->HolidayPrice;
							$order->IsOffer = $stay->SuggestedStay->IsOffer;
							$order->Paxes = $stay->SuggestedStay->Paxes;
							$order->PaxesDiscount = $stay->SuggestedStay->PaxesDiscount;
							$order->PaxesPrice = $stay->SuggestedStay->PaxesPrice;
							$order->TotalDiscount = $stay->SuggestedStay->TotalDiscount;
							$order->TotalPrice = $stay->SuggestedStay->TotalPrice;
							$order->UnitId = $stay->SuggestedStay->UnitId;
							$order->DiscountedPrice = $stay->SuggestedStay->DiscountedPrice;
							$order->RatePlanStay = $stay;
							$order->CalculatedPricesDetails = json_decode($stay->CalculatedPricesString);
							$order->SelectablePrices = json_decode($stay->CalculablePricesString);
							$order->CalculatedPackages = json_decode($stay->PackagesString);
							$order->Variations = json_decode($stay->AllVariationsString);
							$order->DiscountVariation = !empty($stay->Discount) ? $stay->Discount : null;
							$order->SupplementVariation = !empty($stay->Supplement) ? $stay->Supplement : null;
							$order->TimeSlotId = $resource->TimeSlotId;
							$order->TimeSlotStart = $resource->TimeSlotStart;
							$order->TimeSlotEnd = $resource->TimeSlotEnd;
							$order->CheckInTime = $resource->CheckInTime;
							$order->TimeDuration = $resource->TimeDuration;
							$order->ServiceConfiguration = $services;

														
							array_push($lstOrderStay, $order);
						}						
					}
                }
						
            }
		}
						

            return $lstOrderStay;
	}

	public static function AddToCartByExternalUser($tmpUserId, $language, $OrderJson) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//WP->			$model = new BookingForConnectorModelOrders;
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->AddToCartByExternalUser($tmpUserId, $language, $OrderJson);
	}	
	public static function DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//WP->			$model = new BookingForConnectorModelOrders;
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId);
	}	
	public static function GetCartByExternalUser($userId, $language, $includeDetails = true) {
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//WP->			$model = new BookingForConnectorModelOrders;
		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
		return $model->GetCartByExternalUser($userId, $language, $includeDetails);
	}	
	public static function bfi_get_userId() {
		$tmpUserId = BFCHelper::getSession('tmpUserId', null , 'com_bookingforconnector');		
		if(empty($tmpUserId)){
//WP->
//			$uid = get_current_user_id();
//			$user = get_user_by('id', $uid);
//			if (!empty($user->ID)) {
//				$tmpUserId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//			}
//->WP
			$user = JFactory::getUser();
			if ($user->id != 0) {
				$tmpUserId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
			}
			if(empty($tmpUserId)){
				$tmpUserId = uniqid($_SERVER["SERVER_NAME"]);
			}
			BFCHelper::setSession('tmpUserId', $tmpUserId , 'com_bookingforconnector');
		}
		return $tmpUserId;
	}	

	public static function bfi_sortRatePlans($a, $b)
	{
		return $a->SortOrder - $b->SortOrder;
	}
	public static function bfi_sortResourcesRatePlans($a, $b)
	{
		return $a->RatePlan->SortOrder - $b->RatePlan->SortOrder;
	}


}
