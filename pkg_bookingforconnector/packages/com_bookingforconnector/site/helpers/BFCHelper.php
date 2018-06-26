<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if ( ! function_exists( 'bfiDefine' ) ) {
	function bfiDefine( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}
if ( ! function_exists( 'bfi_setSessionFromSubmittedData' ) ) {

function bfi_setSessionFromSubmittedData() {
	$ci = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
	$merchantCategoryId = isset($_REQUEST['merchantCategoryId']) ? $_REQUEST['merchantCategoryId'] : array();
	$cultureCode = isset($_REQUEST['cultureCode']) ? $_REQUEST['cultureCode'] : '';
	$availabilitytype =  isset($_REQUEST['availabilitytype']) ? $_REQUEST['availabilitytype'] : 1;
	$duration = BFCHelper::getStayParam('duration');
	if($availabilitytype == 2 && isset($_REQUEST['duration'])){
		$duration = $_REQUEST['duration'];
	}
	if($availabilitytype == 3 ){
		$duration = 1;
	}

	$itemtypes =  isset($_REQUEST['itemtypes']) ? $_REQUEST['itemtypes'] : '';
	$groupresulttype =  isset($_REQUEST['groupresulttype']) ? $_REQUEST['groupresulttype'] : 0;
	
	$merchantResults = false;
	if(COM_BOOKINGFORCONNECTOR_ISPORTAL){
		//$merchantResults = !empty($merchantCategoryId) && in_array($merchantCategoryId, BFCHelper::getCategoryMerchantResults($cultureCode));
		$merchantResults = ($groupresulttype==1);
	}
	$currParamInSession = BFCHelper::getSearchParamsSession();
	$currParam = array(
		'searchid' => isset($_REQUEST['searchid']) ? $_REQUEST['searchid'] : '',
		'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : '',
		'newsearch' => isset($_REQUEST['newsearch']) ? $_REQUEST['newsearch'] : '0',
		'checkin' => BFCHelper::getStayParam('checkin', new DateTime('UTC')),
		'checkout' => BFCHelper::getStayParam('checkout', $ci->modify(BFCHelper::$defaultDaysSpan)),
		'duration' => $duration,
		'searchTerm' => isset($_REQUEST['searchTerm']) ? $_REQUEST['searchTerm'] : '',
		'searchTermValue' => isset($_REQUEST['searchTermValue']) ? $_REQUEST['searchTermValue'] : '',
		'stateIds' => isset($_REQUEST['stateIds']) ? $_REQUEST['stateIds'] : '',
		'regionIds' => isset($_REQUEST['regionIds']) ? $_REQUEST['regionIds'] : '',
		'cityIds' => isset($_REQUEST['cityIds']) ? $_REQUEST['cityIds'] : '',
		'merchantIds' => isset($_REQUEST['merchantIds']) ? $_REQUEST['merchantIds'] : '',
		'merchantTagIds' => isset($_REQUEST['merchantTagIds']) ? $_REQUEST['merchantTagIds'] : '',
		'productTagIds' => isset($_REQUEST['productTagIds']) ? $_REQUEST['productTagIds'] : '',
		'paxages' => BFCHelper::getStayParam('paxages'),
		'masterTypeId' => isset($_REQUEST['masterTypeId']) ? $_REQUEST['masterTypeId'] : '',
		'merchantResults' => $merchantResults,
		'merchantCategoryId' => $merchantCategoryId,
		'merchantId' => isset($_REQUEST['merchantId']) ? $_REQUEST['merchantId'] : 0,
		'zoneId' => isset($_REQUEST['locationzone']) ? $_REQUEST['locationzone'] : 0,
		'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : -1,
		'availabilitytype' => $availabilitytype,
		'itemtypes' => $itemtypes,
		'groupresulttype' => $groupresulttype,
		'locationzone' => isset($_REQUEST['locationzone']) ? $_REQUEST['locationzone'] : 0,
		'cultureCode' => $cultureCode,
		'paxes' => isset($_REQUEST['persons']) ? $_REQUEST['persons'] : count(BFCHelper::getStayParam('paxages')),
		'tags' => isset($_REQUEST['tags']) ? $_REQUEST['tags'] : '',
		'resourceName' =>  isset($_REQUEST['resourceName']) ? $_REQUEST['resourceName'] : 0,
		'refid' => isset($_REQUEST['refid']) ? $_REQUEST['refid'] : 0,
		'condominiumsResults' => isset($_REQUEST['condominiumsResults']) ? $_REQUEST['condominiumsResults'] : '',
		'pricerange' => isset($_REQUEST['pricerange']) ? $_REQUEST['pricerange'] : '',
		'onlystay' => isset($_REQUEST['onlystay']) ? $_REQUEST['onlystay'] : 0,
		'resourceId' => isset($_REQUEST['resourceId']) ? $_REQUEST['resourceId'] : '',
		'extras' => isset($_REQUEST['extras']) ? $_REQUEST['extras'] : '',
		'packages' => isset($_REQUEST['packages']) ? $_REQUEST['packages'] : '',
		'pricetype' => isset($_REQUEST['pricetype']) ? $_REQUEST['pricetype'] : '',
		'filters' => isset($_REQUEST['filters']) ? $_REQUEST['filters'] : '',
		'rateplanId' => isset($_REQUEST['pricetype']) ? $_REQUEST['pricetype'] : '',
		'variationPlanId' => isset($_REQUEST['variationPlanId']) ? $_REQUEST['variationPlanId'] : '',
		'gotCalculator' => isset($_REQUEST['gotCalculator']) ? $_REQUEST['gotCalculator'] : '',
		'totalDiscounted' => isset($currParamInSession['totalDiscounted']) ? $currParamInSession['totalDiscounted'] : '',
		'suggestedstay' => isset($currParamInSession['suggestedstay']) ?$currParamInSession['suggestedstay'] : '',
		'points' => BFCHelper::getVar('searchType')=="1" ? BFCHelper::getVar('points') : "",
	);
	
	BFCHelper::setSearchParamsSession($currParam);
	
			
}
}

if ( ! class_exists( 'bfi_load_scripts' ) ) {
	function bfi_load_scripts(){
		if (!defined('COM_BOOKINGFORCONNECTOR_SCRIPTS_LOADED')) {

			// load scripts
			JHtml::_('jquery.framework');
			JHtml::_('bootstrap.framework');
			$document 	= JFactory::getDocument();
			$language 	= JFactory::getLanguage()->getTag();
			$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js',false, false);

			$document->addStyleSheet('components/com_bookingforconnector/assets/jquery-ui/themes/smoothness/jquery-ui.min.css');
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/font-awesome.min.css');
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/magnific-popup.css');
			$document->addStyleSheet('components/com_bookingforconnector/assets/js/webui-popover/jquery.webui-popover.min.css');
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');

			$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/jquery-validation/jquery.validate.min.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/jquery-validation/additional-methods.min.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.additional-custom-methods.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.magnific-popup.min.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/webui-popover/jquery.webui-popover.min.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/bfi.js?',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/bfisearchonmap.js',false, false);
			$document->addScript('components/com_bookingforconnector/assets/js/bfi_calendar.js',false, false);
			if(substr($language,0,2)!='en'){
				$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4',false, false);
			}
			$document->addScript('components/com_bookingforconnector/assets/js/recaptcha.js',false, false);

			$db   = JFactory::getDBO();
			$uriCart  = 'index.php?option=com_bookingforconnector&view=cart';
			$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriCart .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
			$itemIdCart= ($db->getErrorNum())? 0 : intval($db->loadResult());
			if ($itemIdCart<>0)
				$uriCart.='&Itemid='.$itemIdCart;

			$url_cart_page = JRoute::_($uriCart);
			if(COM_BOOKINGFORCONNECTOR_USESSL){
				$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
			}

			$document->addScriptDeclaration('
				var bfi_variable = {
					"bfi_urlCheck":"'.(JRoute::_('index.php?option=com_bookingforconnector')).'",
						"bfi_cultureCode":'.json_encode($language).',
						"bfi_defaultcultureCode":"en-gb",
						"defaultCurrency":'.json_encode(bfi_get_defaultCurrency()).',
						"currentCurrency":'.json_encode(bfi_get_currentCurrency()).',
						"CurrencyExchanges":'.json_encode(BFCHelper::getCurrencyExchanges()).',
						"bfi_defaultdisplay":'.json_encode(COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST).',
						"bfi_sendtocart":'.json_encode(COM_BOOKINGFORCONNECTOR_SENDTOCART).',			
						"bfi_eecenabled":'.json_encode(COM_BOOKINGFORCONNECTOR_EECENABLED).',			
						"bfi_carturl":"'. $url_cart_page.'"
					};
			');

			define('COM_BOOKINGFORCONNECTOR_SCRIPTS_LOADED', 1);
		}
	}
}

if ( ! class_exists( 'bfi_Meal' ) ) {
	class bfi_Meal {     
		const Breakfast = 1;
		const Lunch = 2;
		const Dinner = 4;
		const AllInclusive = 8;
		const BreakfastLunch = 3;
		const BreakfastDinner = 5;
		const LunchDinner = 6;
		const BreakfastLunchDinner = 7;

		// etc. }
	}
}
if ( ! class_exists( 'bfiAgeType' ) ) {
	
	class bfiAgeType {     
		public static $Adult = 0;
		public static $Seniors = 1;
		public static $Reduced = 2;
	

		// etc. }
	}
}

if ( ! function_exists( 'bfi_get_defaultCurrency' ) ) {
	function bfi_get_defaultCurrency() {
		$tmpDefaultCurrency = BFCHelper::getSession('defaultcurrency', null , 'com_bookingforconnector');
		if(empty($tmpDefaultCurrency)){
			$tmpDefaultCurrency = BFCHelper::getDefaultCurrency();
			BFCHelper::setSession('defaultcurrency', $tmpDefaultCurrency , 'com_bookingforconnector');
		}
		return $tmpDefaultCurrency;
	}
}
if ( ! function_exists( 'bfi_get_currentCurrency' ) ) {
	function bfi_get_currentCurrency() {
		$tmpCurrentCurrency = BFCHelper::getSession('currentcurrency', COM_BOOKINGFORCONNECTOR_CURRENTCURRENCY , 'com_bookingforconnector');
		if(empty($tmpCurrentCurrency)){
			$tmpCurrentCurrency = BFCHelper::getDefaultCurrency();
			BFCHelper::setSession('currentcurrency', $tmpCurrentCurrency , 'com_bookingforconnector');
		}
		return $tmpCurrentCurrency;
	}
}
if ( ! function_exists( 'bfi_set_currentCurrency' ) ) {
	function bfi_set_currentCurrency($selectedCurrency) {
		$tmpCurrentCurrency = BFCHelper::getSession('currentcurrency', null , 'com_bookingforconnector');
		$tmpCurrencyExchanges = BFCHelper::getCurrencyExchanges();
		if (isset($tmpCurrencyExchanges[$selectedCurrency]) ) {
			BFCHelper::setSession('currentcurrency', $selectedCurrency , 'com_bookingforconnector');
			$tmpCurrentCurrency = $selectedCurrency;
		}
		return $tmpCurrentCurrency;
	}
}
if ( ! function_exists( 'bfi_get_currencyExchanges' ) ) {
	function bfi_get_currencyExchanges() {
		$tmpCurrencyExchanges = BFCHelper::getSession('currencyexchanges', null , 'com_bookingforconnector');
		if(empty($tmpCurrencyExchanges)){
			$tmpCurrencyExchanges = BFCHelper::getCurrencyExchanges();
			//BFCHelper::setSession('currencyexchanges', $tmpCurrencyExchanges , 'com_bookingforconnector');
		}
		return $tmpCurrencyExchanges;
	}
}

if ( ! function_exists( 'bfi_get_file_icon' ) ) {
	function bfi_get_file_icon($fileExtension) {
	  $iconFile = '<i class="fa fa-file-o"></i>';
	  if (empty($fileExtension)) {
	      return $iconFile;
	  }
	  $fileExtension = strtolower($fileExtension);
	  // List of official MIME Types: http://www.iana.org/assignments/media-types/media-types.xhtml
	  static $font_awesome_file_icon_classes = array(
		// Images
		'gif' => '<i class="fa fa-file-image-o"></i>',
		'jpeg' => '<i class="fa fa-file-image-o"></i>',
		'jpg' => '<i class="fa fa-file-image-o"></i>',
		'png' => '<i class="fa fa-file-image-o"></i>',
		// Audio
		'mp3' => '<i class="fa fa-file-audio-o"></i>',
		'wma' => '<i class="fa fa-file-audio-o"></i>',
			
		// Video
		'avi' => '<i class="fa fa-file-video-o"></i>',
		'flv' => '<i class="fa fa-file-video-o"></i>',
		'mpg' => '<i class="fa fa-file-video-o"></i>',
		'mpeg' => '<i class="fa fa-file-video-o"></i>',
		// Documents
		'pdf' => '<i class="fa fa-file-pdf-o"></i>',
		'txt' => '<i class="fa fa-file-text-o"></i>',
		'html' => '<i class="fa fa-file-code-o"></i>',
		'json' => '<i class="fa fa-file-code-o"></i>',
		// Archives
		'gzip' => '<i class="fa fa-file-archive-o"></i>',
		'zip' => '<i class="fa fa-file-archive-o"></i>',
	  );

	  if (isset($font_awesome_file_icon_classes[$fileExtension])) {
		$iconFile = $font_awesome_file_icon_classes[$fileExtension];
	  }

	  return $iconFile;
	}
}


if ( ! class_exists( 'BFCHelper' ) ) {

	class BFCHelper {
		public static $defaultFallbackCode = 'en-gb';
		private static $sessionSeachParamKey = 'searchparams';
		private static $image_basePath = COM_BOOKINGFORCONNECTOR_BASEIMGURL;
		private static $image_basePathCDN = COM_BOOKINGFORCONNECTOR_IMGURL_CDN;
		private static $searchResults = array();
		private static $currentState = array();
		private static $defaultCheckMode = 5;
		private static $favouriteCookieName = "BFFavourites";
		private static $ordersCookieName = "BFOrders";

		private static $TwoFactorCookieName = "2faHSTDenabledWP";
		private static $TwoFactorAuthenticationDeviceExpiration = 30;
		private static $TwoFactorPrefixClaimName = "TwoFactor.DeviceCode.";

		private static $justloaded = false;

		public static $currencyCode = array(
			978 => 'EUR',
			191 => 'HRK',
			840 => 'USD',
			392 => 'JPY',
			124 => 'CAD',
			36 => 'AUD',
			643 => 'RUB',
			200 => 'CZK',
			702 => 'SGD',
			826 => 'GBP',
		);
		public static $listNameAnalytics = array(
			0 => 'Direct access',
			1 => 'Merchants Group List',
			2 => 'Resources Group List',
			3 => 'Resources Search List',
			4 => 'Merchants List',
			5 => 'Resources List',
			6 => 'Offers List',
			7 => 'Sales Resources List',
			8 => 'Sales Resources Search List',
		);
		private static $image_paths = array(
			'merchant' => '/merchants/',
			'resources' => '/products/unita/',
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
			$groupOnSearch = array();
			$merchantCategories = BFCHelper::getMerchantCategories($language);
			if(!empty($merchantCategories)){
				$groupOnSearch = array_unique(array_map(function ($i) { 
					if($i->GroupOnSearch){
						return $i->MerchantCategoryId;
					}
					return 0; 
					}, $merchantCategories));	
			}
			return $groupOnSearch;
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

	public static function priceFormat($number, $decimal=2,$sep1=',',$sep2='.') {
		if(empty($number)){
			$number =0;
		}
		//conversion valuta;
		$defaultcurrency = bfi_get_defaultCurrency();
		$currentcurrency = bfi_get_currentCurrency();

		if($defaultcurrency!=$currentcurrency){
			//try to convert
			$currencyExchanges = BFCHelper::getCurrencyExchanges();
			if (isset($currencyExchanges[$currentcurrency]) ) {
				$number = $number*$currencyExchanges[$currentcurrency];
			}
		}
		return number_format($number, $decimal, $sep1, $sep2);
	}
		
	/* -------------------------------- */

		public static function getCartMultimerchantEnabled() {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			return $model->getCartMultimerchantEnabled();
//			return true;
		}

		public static function GetPrivacy($language) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			return $model->getPrivacy($language);
		}

		public static function getCurrencyExchanges() {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
//			$model = new BookingForConnectorModelPortal;
			return $model->getCurrencyExchanges();
		}

		public static function getDefaultCurrency() {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
//			$model = new BookingForConnectorModelPortal;
			return $model->getDefaultCurrency();
		}

		public static function GetAdditionalPurpose($language) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			return $model->getAdditionalPurpose($language);
		}
		
		public static function GetPhoneByMerchantId($merchantId,$language,$number) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
			return $model->getPhoneByMerchantId($merchantId,$language,$number);
		}

		public static function GetProductCategoryForSearch($language='', $typeId = 1) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			return $model->getProductCategoryForSearch($language, $typeId);
		}


//		public static function GetFaxByMerchantId($merchantId,$language) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
//			return $model->GetFaxByMerchantId($merchantId,$language);
//		}
//
//		public static function setCounterByMerchantId($merchantId = null, $what='', $language='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
//			return $model->setCounterByMerchantId($merchantId, $what, $language);
//		}
//		public static function setCounterByResourceId($resourceId = null, $what='', $language='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnit', 'BookingForConnectorModel');
//			return $model->setCounterByResourceId($resourceId, $what, $language);
//		}

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

		public static function getMerchantOfferFromService($offerId, $language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
//			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantOfferFromService($offerId, $language);
		}
		
		public static function getCondominiumFromServicebyId($resourceId) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Condominium', 'BookingForConnectorModel');
			return $model->getResourceFromService($resourceId);
		}

		public static function getRatingByMerchantId($merchantId) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
			return $model->getMerchantRatingAverageFromService($merchantId);
		}
		public static function getMerchantRatings($start, $limit, $merchantId = null) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
			return $model->getMerchantRatingsFromService($start, $limit, $merchantId);
		}

//		public static function getRatingsByOrderId($orderId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
//			return $model->getRatingsByOrderIdFromService($orderId);
//		}
//		public static function getTotalRatingsByOrderId($orderId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
//			return $model->getTotalRatingsByOrderId($orderId);
//		}	

//		public static function GetMerchantBookingTypeList($SearchModel, $resourceId, $cultureCode) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->GetMerchantBookingTypeList($SearchModel, $resourceId, $cultureCode);
//		}
//
		public static function getResourceRatingAverage($merchantId, $resourceId) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->getRatingAverageFromService($merchantId, $resourceId);
		}
		public static function getResourceRatings($start, $limit, $resourceId = null) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->getRatingsFromService($start, $limit, $resourceId);
		}

		public static function getMerchantGroupsByMerchantId($merchantId) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
			return $model->getMerchantGroupsByMerchantIdFromService($merchantId);
		}
		

//		public static function getUnitCategories() {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getUnitCategories();
//		}
		
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
//		public static function getLastLocationZoneOnsell() {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getLastLocationZoneOnsell();
//		}

		public static function getLocations() {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
			return $model->getLocations();
		}
//		public static function getLocationById($locationId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
//			return $model->getLocationById($locationId);
//		}

//		public static function getMerchantTypes() {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
//			return $model->getMerchantTypes();
//		}
		
		public static function getMasterTypologies($onlyEnabled = true) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
			return $model->getMasterTypologies($onlyEnabled);
		}
		public static function GetAlternativeDates($checkin, $duration, $paxes, $paxages, $merchantId, $condominiumId, $resourceId, $cultureCode, $points, $userid, $tagids, $merchantsList, $availabilityTypes, $itemTypeIds, $domainLabel, $merchantCategoryIds = null, $masterTypeIds = null, $merchantTagsIds = null) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
			return $model->GetAlternativeDates($checkin, $duration, $paxes, $paxages, $merchantId, $condominiumId, $resourceId, $cultureCode, $points, $userid, $tagids, $merchantsList, $availabilityTypes, $itemTypeIds, $domainLabel, $merchantCategoryIds, $masterTypeIds, $merchantTagsIds);
		}

		public static function SearchByText($term, $language, $limit, $onlyLocations=0) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
			return $model->SearchResult($term, $language, $limit, $onlyLocations);
		}
		
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
//		
//		public static function getResourceByMasterTypologyId($masterTypologyId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
//			$mystate=$model->getState();
//			$model->setState('params', array(
//				'masterTypeId' => $masterTypologyId
//			));
//			return $model->getItems();
//		}
//		
//		public static function getResourceModel() {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model;
//		}
		
		public static function getMerchantCategories($language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
			return $model->getMerchantCategories($language);
		}
		public static function getMerchantCategoriesForRequest($language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
			return $model->getMerchantCategoriesForRequest($language);
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
		public static function GetPolicyById($policId,$language='') {
//			$model = new BookingForConnectorModelResource;
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->GetPolicyById($policId,$language);
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

//		public static function GetResourcesCalculateByIds($listsId,$language='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
//			return $model->GetResourcesCalculateByIds($listsId,$language);
//		}

	public static function GetAlternateResources($start, $limit, $ordering = null, $direction = null, $merchantid = null,  $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null, $language='') {
//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		$model = new BookingForConnectorModelResource;
		return $model->getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid, $ignorePagination, false, $excludedResources, $requiredOffers, $overrideFilters, $language);
	}
		
		public static function getDiscountDetails($ids, $language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->getDiscountDetails($ids,$language);
		}
//		public static function GetDiscountsByResourceId($resourcesId,$hasRateplans) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->GetDiscountsByResourceId($resourcesId,$hasRateplans);
//		}

//		public static function getRateplanDetails($rateplanId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getRateplanSimpleDetails($rateplanId);
//		}
		public static function GetResourcesOnSellByIds($listsId,$language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
			return $model->GetResourcesByIds($listsId,$language);
		}
		public static function GetResourcesOnSellById($Id,$language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('OnSellUnit', 'BookingForConnectorModel');
			return $model->getResourceFromServicebyId($Id,$language);
		}
		public static function GetServicesByIds($listsId,$language='') {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Services', 'BookingForConnectorModel');
//			return $model->getServicesByIds($listsId,$language);
			return $model->getServicesFromService($language);
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

//		public static function getResourcesOnSellShowcase($language='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getResourcesOnSellShowcase($language);
//		}
//
//		public static function getResourcesOnSellGallery($language='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getResourcesOnSellGallery($language);
//		}
//		
//		public static function getAvailableLocationsAverages($language='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getAvailableLocationsAverages($language);
//		}
//
//		public static function getCategoryPriceMqAverages($language='',$locationid) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getCategoryPriceMqAverages($language,$locationid);
//		}
//		
//		public static function getPriceAverages($language='',$locationid,$unitcategoryid) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getPriceAverages($language,$locationid,$unitcategoryid);
//		}
//
//		public static function getPriceHistory($language='',$locationid,$unitcategoryid) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getPriceHistory($language,$locationid,$unitcategoryid);
//		}
//
//		public static function getPriceMqAverageLastYear($language='',$locationid,$unitcategoryid ,$contracttype = 0) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
//			return $model->getPriceMqAverageLastYear($language,$locationid,$unitcategoryid, $contracttype);
//		}

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
//		public static function getTagsForSearch($language='',$categoryIds='') {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
//			return $model->getTagsForSearch($language,$categoryIds);
//		}

		public static function getMerchantsExt($tagids, $start = null, $limit = null) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
			return $model->getMerchantsExt($tagids, $start, $limit);
		}

//		public static function GetAlternateResources($start, $limit, $ordering = null, $direction = null, $merchantid = null,  $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid, $ignorePagination, false, $excludedResources, $requiredOffers, $overrideFilters);
//		}

//		public static function getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid, $ignorePagination, false, $excludedResources, $requiredOffers, $overrideFilters);
//		}

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
		public static function GetLastOrderPayment($orderId = 0) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
			return $model->GetLastOrderPayment($orderId);
		}	
		
		public static function GetOrderDetailsById($orderId,$culturecode='') {
//			$model = new BookingForConnectorModelOrders;
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->GetOrderDetailsById($orderId,$culturecode);
		}
		public static function setOrder($customerData = NULL, $suggestedStay = NULL, $creditCardData = NULL, $otherNoteData = NULL, $merchantId = NULL, $orderType = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processOrder = NULL, $priceType, $merchantBookingTypeId = NULL, $policyId = NULL) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->setOrder($customerData, $suggestedStay, $creditCardData, $otherNoteData, $merchantId, $orderType, $userNotes, $label, $cultureCode, $processOrder, $priceType,$merchantBookingTypeId, $policyId);
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

//		public static function getOrderPayments($start,$limit,$orderid) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
//			return $model->getOrderPayments($start,$limit,$orderid);
//		}
//
//		public static function getTotalOrderPayments($orderId = NULL)  {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
//			return $model->getTotalOrderPayments($orderId);
//		}
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

//		public static function setAlertOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processAlert = NULL, $enabled = NULL) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
//			return $model->setAlertOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processAlert, $enabled);
//		}
//		public static function sendRequestOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processRequest = NULL) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
//			return $model->sendRequestOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processRequest);
//		}
//		
//		public static function unsubscribeAlertOnSell($hash = NULL, $id = NULL)  {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
//			return $model->unsubscribeAlertOnSell($hash, $id);
//		}
//

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
//		
//		public static function getEndDate() {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getEndDate();
//		}

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
		public static function GetListCheckInDayPerTimes($resourceId = null,$ci = null, $limitTotDays = 0) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->GetListCheckInDayPerTimes($resourceId ,$ci, $limitTotDays);
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

		public static function GetMostRestrictivePolicyByIds($policyIds, $cultureCode, $stayConfiguration ='', $priceValue=null, $days=null) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->GetMostRestrictivePolicyByIds($policyIds, $cultureCode, $stayConfiguration, $priceValue, $days);
		}

		public static function GetPolicyByIds($policyIds, $cultureCode) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			return $model->GetPolicyByIds($policyIds, $cultureCode);
		}

//		public static function getCheckAvailabilityCalendar($resourceId = null,$checkIn= null,$checkOut= null) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getCheckAvailabilityCalendarFromService($resourceId,$checkIn,$checkOut);
//		}
		
		//Please attention it's another method into another model, this is for list, not for single
//		public static function getCheckAvailabilityCalendarFromlList($resourcesId = null,$checkIn= null,$checkOut= null) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
//			return $model->getCheckAvailabilityCalendarFromService($resourcesId,$checkIn,$checkOut);
//		}

		//Please attention it's another method into another model, this is for list, not for single
//		public static function getStayFromParameter($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$extras='',$packages,$pricetype='',$rateplanId=null,$variationPlanId=null,$hasRateplans=null ) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//			return $model->getStayFromServiceFromParameter($resourceId,$checkIn,$duration,$paxages,$extras,$packages,$pricetype,$rateplanId,$variationPlanId,$hasRateplans);
//		}
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
		public static function GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language="",$condominiumId=0 ){
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
		return $model->GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language,$condominiumId );
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
				$label = NULL, 
				$otherData = NULL
			) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
			return $model->setRating($name, $city, $typologyid, $email, $nation, $merchantId,$value1, $value2, $value3, $value4, $value5, $totale, $pregi, $difetti, $userId, $cultureCode,$checkin, $resourceId, $orderId, $label, $otherData);
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
						case 'nobr':
							$retVal = preg_replace("/\n+/", " ", $retVal);
							break;
						case 'bbcode':
							$search = array (
								'~\[b\](.*?)\[/b\]~s',
								'~\[i\](.*?)\[/i\]~s',
								'~\[u\](.*?)\[/u\]~s',
								'~\[s\](.*?)\[/s\]~s',
								'~\[ul\](.*?)\[/ul\]~s',
								'~\[li\](.*?)\[/li\]~s',
								'~\[ol\](.*?)\[/ol\]~s',
								'~\[size=(.*?)\](.*?)\[/size\]~s',
								'/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is'
							);
							$replace = array (
								'<b>$1</b>',
								'<i>$1</i>',
								'<u>$1</u>',
								'<s>$1</s>',
								'<ul>$1</ul>',
								'<li>$1</li>',
								'<ol>$1</ol>',
								'<font size="$1">$2</font>',
								''
							);
							$retVal = preg_replace($search, $replace, $retVal); // cleen for br

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

		public static function parseJsonDateTime($date, $format = 'd/m/Y') { 
			date_default_timezone_set('UTC');
			return DateTime::createFromFormat($format, BFCHelper::parseJsonDate($date,$format),new DateTimeZone('UTC'));
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
						} else {
							$path = str_replace($pathfilename, $resizedpath . "/".$pathfilename ,$path);
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
			}
			
			return $finalPath;
		}
		
		public static function getDefaultParam($param) {
			switch (strtolower($param)) {
				case 'checkin':
					return DateTime::createFromFormat('d/m/Y',self::getStartDate(),new DateTimeZone('UTC'));
					//return new DateTime('UTC');
					break;
				case 'checkout':
					$co = DateTime::createFromFormat('d/m/Y',self::getStartDate(),new DateTimeZone('UTC'));
					//$co = new DateTime('UTC');
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

//for analytics
		public static function AddToCookieOrders($id) {
			$expire=time()+60*60*24*30;
			$counter = 1;
			$lisTordersCookie = (string) $id;
			$varCook = BFCHelper::getCookie(self::$ordersCookieName);				
			if (isset($varCook))
			{
				$arr= explode("_", $varCook);
				if ( !self::IsInCookieOrders($id)){
					array_push($arr, (string)$id);
				}
				$arr = array_filter( $arr );
				$counter = count($arr);
				$lisTordersCookie = (string)implode("_", $arr);				
			}
			$config = JFactory::getConfig();
			$cookie_domain = $config->get('cookie_domain', '');
			$cookie_path = $config->get('cookie_path', '/');
			$ok = setcookie(self::$ordersCookieName, $lisTordersCookie, $expire, $cookie_path, '');
			return $counter;
		}
				
		public static function IsInCookieOrders($id) {
			$varCook = BFCHelper::getCookie(self::$ordersCookieName);
			
			
			if (isset($varCook))
			{
				$arr= explode("_", $varCook);
				return in_array($id, $arr);
			}
			return false;
		}
		

		public static function setSearchOnSellParamsSession($params) {
			$sessionkey = 'searchonsell.params';
			self::setSession($sessionkey, $params, 'com_bookingforconnector'); 
		}
		
		public static function getSearchOnSellParamsSession() {
			$sessionkey = 'searchonsell.params';
			$pars = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $pars;
		}
		
		public static function setSearchMerchantParamsSession($params) {
			$sessionkey = 'searchmerchant.params';
			$pars = array();
			$pars['merchantCategoryId'] = !empty($params['merchantCategoryId']) ? $params['merchantCategoryId']: 0;
			if(isset($params['searchid'])){
				$pars['searchid'] = $params['searchid'];
			}
			if(isset($params['newsearch'])){
				$pars['newsearch'] = $params['newsearch'];
			}
			if(isset($params['points'])){
				$pars['points'] = $params['points'];
			}
			$pars['locationzones'] = !empty($params['locationzones']) ? $params['locationzones']: "";
			$pars['locationzone'] = !empty($params['locationzone']) ? $params['locationzone']: "";
			$pars['stateIds'] = !empty($params['stateIds']) ? $params['stateIds']: "";
			$pars['regionIds'] = !empty($params['regionIds']) ? $params['regionIds']: "";
			$pars['cityIds'] = !empty($params['cityIds']) ? $params['cityIds']: "";
			$pars['zoneIds'] = !empty($params['zoneIds']) ? $params['zoneIds']: "";
			$pars['cultureCode'] = !empty($params['cultureCode']) ? $params['cultureCode']: "";
			$pars['merchantTagIds'] = !empty($params['merchantTagIds']) ? $params['merchantTagIds']:"";
			$pars['tags'] = !empty($params['tags']) ? $params['tags']:"";
			$pars['rating'] = !empty($params['rating']) ? $params['rating']:"";
			$pars['filters'] = !empty($params['filters']) ? $params['filters']: "";
			self::setSession($sessionkey, $pars, 'com_bookingforconnector'); 
		}
		public static function getSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.params';
			$pars = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $pars;
		}
		public static function setFilterSearchMerchantParamsSession($paramsfilters) {
			$sessionkey = 'searchmerchant.filterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getFilterSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.filterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}

		public static function setEnabledFilterSearchMerchantParamsSession($paramsfilters) {
			$sessionkey = 'searchmerchant.enabledfilterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getEnabledFilterSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.enabledfilterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}
		public static function setFirstFilterSearchMerchantParamsSession($paramsfilters) {
			$sessionkey = 'searchmerchant.firstfilterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getFirstFilterSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.firstfilterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}



		public static function setSearchParamsSession($params) {
			$sessionkey = 'search.params';
			$pars = array();
			if(isset($params['checkin'])){
				$pars['checkin'] = $params['checkin'];
			}
			if(isset($params['checkout'])){
				$pars['checkout'] = $params['checkout'];
			}
			if(isset($params['duration'])){
				$pars['duration'] = $params['duration'];
			}
			if(isset($params['paxes'])){
				$pars['paxes'] = $params['paxes'];
			}
			if(isset($params['paxages'])){
				$pars['paxages'] = $params['paxages'];
			}
			
			$pars['onlystay'] = !empty($params['onlystay']) ? $params['onlystay']: 0;
			$pars['searchtypetab'] = !empty($params['searchtypetab']) ? $params['searchtypetab']: "0";
			$pars['masterTypeId'] = !empty($params['masterTypeId']) ? $params['masterTypeId']: "0";
			$pars['merchantResults'] = !empty($params['merchantResults']) ? $params['merchantResults']: 0;
			$pars['merchantCategoryId'] = !empty($params['merchantCategoryId']) ? $params['merchantCategoryId']: 0;
			$pars['zoneId'] = !empty($params['zoneId']) ? $params['zoneId']: 0;
			$pars['cityId'] = !empty($params['cityId']) ? $params['cityId']: 0;
			$pars['locationzone'] = !empty($params['locationzone']) ? $params['locationzone']: "";
			$pars['locationzones'] = !empty($params['locationzones']) ? $params['locationzones']: "";
			$pars['zoneIds'] = !empty($params['zoneIds']) ? $params['zoneIds']: "";
			$pars['cultureCode'] = !empty($params['cultureCode']) ? $params['cultureCode']: "";
			$pars['filters'] = !empty($params['filters']) ? $params['filters']: "";
			$pars['resourceName'] = !empty($params['resourceName']) ? $params['resourceName']: "";
			$pars['refid'] = !empty($params['refid']) ? $params['refid']: "";
			$pars['pricerange'] = !empty($params['pricerange']) ? $params['pricerange']: 0;
			$pars['bookableonly'] = !empty($params['bookableonly']) ? $params['bookableonly']: 0;
			$pars['condominiumsResults'] = !empty($params['condominiumsResults']) ? $params['condominiumsResults']: 0;
			$pars['productTagIds'] = !empty($params['productTagIds']) ? $params['productTagIds']:"";
			$pars['merchantTagIds'] = !empty($params['merchantTagIds']) ? $params['merchantTagIds']:"";

			$pars['variationPlanIds'] = !empty($params['variationPlanIds']) ? $params['variationPlanIds']:"";

			if(isset($params['merchantId'])){
				$pars['merchantId'] = $params['merchantId'];
			}
			if(!empty($params['availabilitytype'])){
				$pars['availabilitytype'] = $params['availabilitytype'];
			}
			if(isset($params['itemtypes'])){
				$pars['itemtypes'] = $params['itemtypes'];
			}
			if(isset($params['groupresulttype'])){
				$pars['groupresulttype'] = $params['groupresulttype'];
			}
			if(isset($params['searchid'])){
				$pars['searchid'] = $params['searchid'];
			}
			if(isset($params['newsearch'])){
				$pars['newsearch'] = $params['newsearch'];
			}
			if(isset($params['stateIds'])){
				$pars['stateIds'] = $params['stateIds'];
			}
			if(isset($params['regionIds'])){
				$pars['regionIds'] = $params['regionIds'];
			}
			if(isset($params['cityIds'])){
				$pars['cityIds'] = $params['cityIds'];
			}
			if(isset($params['points'])){
				$pars['points'] = $params['points'];
			}
			self::setSession($sessionkey, $pars, 'com_bookingforconnector'); 

		}
		
		public static function getSearchParamsSession() {
			$sessionkey = 'search.params';
			$pars = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $pars;
		}
		
		public static function setFilterSearchParamsSession($paramsfilters) {
			$sessionkey = 'search.filterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getFilterSearchParamsSession() {
			$sessionkey = 'search.filterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}

		public static function setEnabledFilterSearchParamsSession($paramsfilters) {
			$sessionkey = 'search.enabledfilterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getEnabledFilterSearchParamsSession() {
			$sessionkey = 'search.enabledfilterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}
		public static function setFirstFilterSearchParamsSession($paramsfilters) {
			$sessionkey = 'search.firstfilterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getFirstFilterSearchParamsSession() {
			$sessionkey = 'search.firstfilterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
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
			date_default_timezone_set('UTC');
			$pars = self::getSearchParamsSession();

			switch (strtolower($param)) {
				case 'checkin':
					$strCheckin = BFCHelper::getString('checkin');
					if (($strCheckin == null || $strCheckin == '') && (isset($pars['checkin']) && $pars['checkin'] != null && $pars['checkin'] != '')) {
						return clone $pars['checkin'];
					}
					$checkin = DateTime::createFromFormat('d/m/Y',$strCheckin,new DateTimeZone('UTC'));
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
					$checkout = DateTime::createFromFormat('d/m/Y',$strCheckout,new DateTimeZone('UTC'));
					if ($checkout===false && isset($default)) {
						$checkout = $default;
					}
					return $checkout;
					break;
				case 'duration':
					$ci = self::getStayParam('checkin', new DateTime('UTC'));
					$dco = new DateTime('UTC');
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
					$adults = BFCHelper::getInt('adults', self::$defaultAdultsQt);
					$children = BFCHelper::getInt('children',0);
					$seniores = BFCHelper::getInt('seniores',0);
					if (($adults == null || $adults == '') && ($children == null || $children == '') && (isset($pars['paxages']) && $pars['paxages'] != null && $pars['paxages'] != '')) {
						return array_slice($pars['paxages'],0);
					}
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


	public static function encryptSupported()
	{
		$cryptoVersion= 0;

		if (function_exists('mcrypt_create_iv') && function_exists('mcrypt_get_iv_size') && function_exists('mcrypt_encrypt') && function_exists('mcrypt_decrypt'))
		{
			$cryptoVersion= 1;
		}
		if (function_exists('openssl_random_pseudo_bytes') && function_exists('openssl_cipher_iv_length') && function_exists('openssl_encrypt') && function_exists('openssl_decrypt'))
		{
			$cryptoVersion= 2;
		}

		return $cryptoVersion;
	}

	// OPENSSL
	// - funzione di criptazione/decriptazione basato su una chiave
	public static function encryptOpenSll($string,$key=null) {
		$cipher = 'AES-256-CBC';
		// Must be exact 32 chars (256 bit)
		$password = substr(hash('sha256', $key, true), 0, 32);
		// IV must be exact 16 chars (128 bit)
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		$encrypted_string = openssl_encrypt($string, $cipher, $password, OPENSSL_RAW_DATA, $iv);
		return base64_encode($encrypted_string);
	}
	public static function decryptOpenSll($string,$urldecode = false,$key=null) {
		if ($urldecode) {
			$string = urldecode($string);
		}
		$string = base64_decode($string);
		$cipher = 'AES-256-CBC';
		// Must be exact 32 chars (256 bit)
		$password = substr(hash('sha256', $key, true), 0, 32);
		// IV must be exact 16 chars (128 bit)
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		$encrypted_string = openssl_decrypt($string, $cipher, $password, OPENSSL_RAW_DATA, $iv);
		return $encrypted_string;
	}

	// MCRYPT
	// - funzione di criptazione/decriptazione basato su una chiave
		public static function encryptMcrypt($string,$key=null) {
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
		
	   public static function decryptMcrypt($string,$urldecode = false,$key=null) {
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


	public static function encrypt($string,$key=null) {
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==1) {
			return self::encryptMcrypt($string);
		}
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==2) {
			return self::encryptOpenSll($string,$key);
		}
		return null;
	}
	public static function decrypt($string,$urldecode = false,$key=null) {
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==1) {
			return self::decryptMcrypt($string,$urldecode);
		}
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==2) {
			return self::decryptOpenSll($string,$urldecode,$key);
		}
		return null;
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
		public static function getCustomerData($formData) {
			if ($formData == null) {
				$formData = $_POST['form'];
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
					$Culture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
					$UserCulture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
					$Culture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$Culture; // => self::getOptionsFromSelect($formData, 'Culture'),
					$UserCulture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$UserCulture; // => self::getOptionsFromSelect($formData, 'Culture'),
					$gender = isset($formData['Gender'])?self::getOptionsFromSelect($formData, 'Gender'):'';

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
					'UserCulture' => $UserCulture,
					//'BirthDate' => isset($formData['Birthday']) ? DateTime::createFromFormat('d/m/Y', $formData['Birthday'])->format("Y-m-d"): null,
					'Gender' => $gender,
			);
			if(isset($formData['Birthday'])){
				$customerData['BirthDate'] = DateTime::createFromFormat('d/m/Y', $formData['Birthday'],new DateTimeZone('UTC'))->format("Y-m-d");
			}

					
			return $customerData;
		}
		public static function canAcquireCCData($formData) {
			if ($formData == null) {
				$formData = $_POST['form'];
			}		
			
			if(!empty($formData['bookingType'])){
				$bt = $formData['bookingType'];
				if (is_array($bt)) { // if is an array (because it is sent using a select)
					$bt = $bt[0]; // keep only the first value
				}
				if ($bt != '') { // need to check for acquire cc data
					$btData = explode(':',$bt); // data is sent like 'ID:acquireccdata' -> '9:1' or '9:0' or '9:' (where zero is replaced by an empty char)
					if (count($btData) > 1) { // we have more than one value so data sent is correct
						if ($btData[1] != '') { // need to set mandatory for field credit card prefixed with 'cc_' (or other supplied prefix)
							return true;
						}
					}
				}
			}
			return false;
		}
		
		public static function getCCardData($formData) {
			if ($formData == null) {
				$formData = $_POST['form'];
			}
			if(isset($formData['cc_numero']) && !empty($formData['cc_numero'])) {
				$ccData = array(
						'Type' => self::getOptionsFromSelect($formData,'cc_circuito'),
						'TypeId' => self::getOptionsFromSelect($formData,'cc_circuito'),
						'Number' => $formData['cc_numero'],
						'Name' => $formData['cc_titolare'],
						'ExpiryMonth' => $formData['cc_mese'],
						'ExpiryYear' => $formData['cc_anno']
				);
				
				return $ccData;
			}
			return null;
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
			$gaaccount = COM_BOOKINGFORCONNECTOR_GAACCOUNT;
			if(!self::$justloaded){
				self::$justloaded = true;
				$writeJs = true;
				if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
				   $writeJs = false;
				}
				$document 	= JFactory::getDocument();
				if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT)) {
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
							ga("create","' . $gaaccount . '"' . (strpos(JURI::current(), 'localhost') !== false ? ', {
							  "cookieDomain": "none"
							}' : ', "auto"') . ');
						}');
					}
					if(COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
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
								jQuery("body").on("click", ".bfi-sort-item", function(e){
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
									var currList = jQuery(this).attr("data-list") || null;

									if( e.which <= 2 ) {
										callAnalyticsEEc("addProduct", [{
											id: jQuery(this).attr("data-id") + " - " + jQuery(this).attr("data-type"),
											name: jQuery(this).attr("data-itemname"), 
											category: jQuery(this).attr("data-category"),
											brand: jQuery(this).attr("data-brand"), 
											//variant: jQuery(this).attr("data-type"),
											position: parseInt(jQuery(this).attr("data-index")), 
										}], "viewDetail", currList, jQuery(this).attr("data-id"), jQuery(this).attr("data-type"));
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
										ga("set", "&cu", "'.self::$currencyCode[bfi_get_defaultCurrency()].'");
										ga("ec:setAction", "add", actiondetail);
										ga("send", "event", "Bookingfor - " + itemtype, "click", "addToCart");
										bookingfor_gapageviewsent++;
										break;
									case "removefromcart":
										ga("set", "&cu", "'.self::$currencyCode[bfi_get_defaultCurrency()].'");
										ga("ec:setAction", "remove", actiondetail);
										ga("send", "event", "Bookingfor - " + itemtype, "click", "addToCart");
										bookingfor_gapageviewsent++;
										break;
									case "purchase":
										ga("set", "&cu", "'.self::$currencyCode[bfi_get_defaultCurrency()].'");
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

			if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT)) {
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
		
		public static function SetTwoFactorCookie($id) {
			$expire=time()+60*60*24*self::$TwoFactorAuthenticationDeviceExpiration;
//			$ok = setcookie(self::$TwoFactorCookieName, $id, $expire,SITECOOKIEPATH, COOKIE_DOMAIN);
			$config = JFactory::getConfig();
			$cookie_domain = $config->get('cookie_domain', '');
			$cookie_path = $config->get('cookie_path', '/');
			$ok = setcookie(self::$TwoFactorCookieName, $id, $expire,$cookie_path, '');

		}
		public static function GetTwoFactorCookie() {
			$twofactorCookie = BFCHelper::getCookie(self::$TwoFactorCookieName);
			return $twofactorCookie;
		}
		public static function DeleteTwoFactorCookie() {
//			setcookie( self::$TwoFactorCookieName, '', 0,SITECOOKIEPATH, COOKIE_DOMAIN);
			$config = JFactory::getConfig();
			$cookie_domain = $config->get('cookie_domain', '');
			$cookie_path = $config->get('cookie_path', '/');
			setcookie( self::$TwoFactorCookieName, '', 0,$cookie_path, '');
			unset( $_COOKIE[self::$TwoFactorCookieName] );
		}

		public static function getLoginTwoFactor($email, $password, $twoFactorAuthCode,$deviceCodeAuthCode) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//WP->					$model = new BookingForConnectorModelPortal;
			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			return $model->getLoginTwoFactor($email, $password, $twoFactorAuthCode,$deviceCodeAuthCode);
		}	

		public static function getOptionsFromSelect($formData, $str){
			if ($formData == null) {
				$formData = $_POST['form'];
			}

			$aStr = isset($formData[$str])?$formData[$str]:null;
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

		public static function getSession($string, $defaultValue=null, $prefix ='') {
			if(empty(COM_BOOKINGFORCONNECTOR_ENABLECACHE)) return null;
			$session = JFactory::getSession();
			return $session->get($string, $defaultValue , $prefix);

		}
		public static function setSession($string, $value=null, $prefix ='') {
			$session = JFactory::getSession();
			$session->set($string, $value, $prefix);
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
					"CheckInDateTime" => count($array) > 2 && !empty($array[2]) ? DateTime::createFromFormat("YmdHis", $array[2],new DateTimeZone('UTC')) : null,
					"PeriodDuration" => count($array) > 3 && !empty($array[3]) ? intval($array[3]) : 0,
					"TimeSlotId" => count($array) > 4 && !empty($array[4]) ? intval($array[4]) : 0,
					"TimeSlotStart" => count($array) > 5 && !empty($array[5]) ? intval($array[5]) : 0,
					"TimeSlotEnd" => count($array) > 6 && !empty($array[6]) ? intval($array[6]) : 0,
					"TimeSlotDate" => count($array) > 7 && !empty($array[7]) ? DateTime::createFromFormat("Ymd", $array[7],new DateTimeZone('UTC')) : null,
					"CheckInDate" => count($array) > 8 && !empty($array[8]) ? DateTime::createFromFormat("Ymd", $array[8],new DateTimeZone('UTC')) : null,
					"CheckOutDate" => count($array) > 9 && !empty($array[9]) ? DateTime::createFromFormat("Ymd", $array[9],new DateTimeZone('UTC')) : null,
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
				$DateTimeMinValue = new DateTime('UTC');
				$DateTimeMinValue->setDate(1, 1, 1);

				$orderModel->SearchModel->FromDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkin,new DateTimeZone('UTC'));
				$orderModel->SearchModel->ToDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkout,new DateTimeZone('UTC'));
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
							$currModel->SearchModel->FromDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
												  
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
							array_push($paxages, COM_BOOKINGFORCONNECTOR_ADULTSAGE);
						}
						for ($i=0;$i<$currModel->SearchModel->SeniorCount ; $i++)	
						{
							array_push($paxages, COM_BOOKINGFORCONNECTOR_SENIORESAGE);
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
										$SelectedResource->CheckInTime = (isset($selRatePlan->SuggestedStay->CheckIn) )?BFCHelper::parseJsonDate($selRatePlan->SuggestedStay->CheckIn,'YmdHis'):$currModel->SearchModel->FromDate->format('YmdHis');
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
										$SelectedResource->PolicyId = 0;
										if(isset($selRatePlan->Policy) && !empty($selRatePlan->Policy->PolicyId) ){
											$SelectedResource->PolicyId = $selRatePlan->Policy->PolicyId;
										}
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
	//			$totalModel = json_decode(stripslashes($OrderJson));


// WP=>				$orderModel = json_decode(stripslashes($OrderJson));
				$orderModel = json_decode($OrderJson);
				$lstOrderStay = array();
				$DateTimeMinValue = new DateTime('UTC');
				$DateTimeMinValue->setDate(1, 1, 1);

	//            foreach ($totalModel as $orderModel)
	//            {
					
	//			if(isset($orderModel->SearchModel->checkin)){
	//				$orderModel->SearchModel->FromDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkin);
	//			}else{
	//				$orderModel->SearchModel->FromDate = new DateTime($orderModel->SearchModel->FromDate );
	//			}
	//			if(isset($orderModel->SearchModel->checkout)){
	//				$orderModel->SearchModel->ToDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkout);
	//			}else{
	//				$orderModel->SearchModel->ToDate = new DateTime($orderModel->SearchModel->ToDate );
	//			}
	//
	//			$orderModel->SearchModel->FromDate->setTime(0,0,0);
	//			$orderModel->SearchModel->ToDate->setTime(0,0,0);

	//				echo "<pre>";
	//				echo print_r($orderModel->SearchModel);
	//				echo "</pre>";
	//				die();

	//            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 && $orderModel->SearchModel->FromDate != $DateTimeMinValue)
				if ($orderModel->Resources != null && count($orderModel->Resources) > 0 )
				{
	//                $resourceDetail = null;
					foreach ($orderModel->Resources as $resource)
					{
	//                    $resourceDetail = BFCHelper::GetResourcesById($resource->ResourceId);
	//                    $order->MerchantId = $resourceDetail->MerchantId;
						
						$fromCart= !empty($resource->CartOrderId)?1:0;
						$services="";
						if(isset($resource->ExtraServicesValue)){
							$services = $resource->ExtraServicesValue;
						}
						if(isset($resource->ExtraServices)){
							$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
							if(!empty($servicesArray)){
								$services = implode("|",$servicesArray);
							}
						}

	//					$services = isset($resource->ExtraServicesValue)?$resource->ExtraServicesValue:(isset($resource->ExtraServices)?json_encode($resource->ExtraServices):"");
						
	//					$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
	//					if(!empty($servicesArray)){
	//						$services = implode("|",$servicesArray);
	//					}
	//					$currservices = BFCHelper::GetPriceParameters($services);
	//                    $selectablePrices = array_filter($currservices, function($t) {return $t["Quantity"] > 0;});
										
	//					$currModel = clone $orderModel;
						$currModel = new stdClass;
						$currModel->SearchModel = new stdClass;
						if($fromCart==0){
							$currModel->SearchModel->FromDate  = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->FromDate,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate  = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->ToDate,new DateTimeZone('UTC'));
						}else{
							$currModel->SearchModel->FromDate = new DateTime($resource->FromDate,new DateTimeZone('UTC') );
							$currModel->SearchModel->ToDate = new DateTime($resource->ToDate,new DateTimeZone('UTC') );
						}
						$currModel->SearchModel->FromDate->setTime(0,0,0);
						$currModel->SearchModel->ToDate->setTime(0,0,0);
						$currModel->SearchModel->MerchantId = $resource->MerchantId;
						$currModel->SearchModel->ProductAvailabilityType = $resource->AvailabilityType;
						$duration = 1;

						if ($resource->AvailabilityType== 2)
						{
							$duration = $resource->TimeDuration;
							$currModel->SearchModel->FromDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
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
						$paxages = $resource->PaxAges;
						if ($duration ==0 && $resource->AvailabilityType ==0) {
						    $duration = 1;
						}

	//					$paxages = array();
	//					for ($i=0;$i<$currModel->SearchModel->AdultCount ; $i++)	
	//					{
	//						array_push($paxages, COM_BOOKINGFORCONNECTOR_ADULTSAGE);
	//					}
	//					for ($i=0;$i<$currModel->SearchModel->SeniorCount ; $i++)	
	//					{
	//						array_push($paxages, COM_BOOKINGFORCONNECTOR_SENIORESAGE);
	//					}
	////					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
	//////					$paxages = implode("|",$paxages);
	//					$nchsarray = array($currModel->SearchModel->childages1,$currModel->SearchModel->childages2,$currModel->SearchModel->childages3,$currModel->SearchModel->childages4,$currModel->SearchModel->childages5,null);
	//					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
	//					{
	//						array_push($paxages, $nchsarray[$i]);
	//					}  										
											


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
								$order = new StdClass;
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
								//$order->SelectablePrices = json_decode($stay->CalculablePricesString);
								//$order->CalculatedPackages = json_decode($stay->PackagesString);
								//$order->MerchantBookingTypesString = json_decode($stay->MerchantBookingTypesString);
								$order->Variations = json_decode($stay->AllVariationsString);
								$order->DiscountVariation = !empty($stay->Discount) ? $stay->Discount : null;
								$order->SupplementVariation = !empty($stay->Supplement) ? $stay->Supplement : null;
								$order->TimeSlotId =  isset($resource->TimeSlotId)?$resource->TimeSlotId:"";
								$order->TimeSlotStart = isset($resource->TimeSlotStart)?$resource->TimeSlotStart:"";
								$order->TimeSlotEnd = isset($resource->TimeSlotEnd)?$resource->TimeSlotEnd:"";
								$order->CheckInTime = isset($resource->CheckInTime)?$resource->CheckInTime:"";
								$order->TimeDuration = isset($resource->TimeDuration)?$resource->TimeDuration:"";
								$order->ServiceConfiguration = $services;
								$order->PolicyId = 0;							

								if(isset($stay->Policy) && !empty($stay->Policy->PolicyId)) {
									$order->PolicyId = $stay->Policy->PolicyId;
								}
								if(isset($stay->Policy) && !empty($stay->Policy->PolicyId)) {
									$order->PolicyId = $stay->Policy->PolicyId;
								}
								
								unset($order->RatePlanStay->CalculatedPricesString);
								unset($order->RatePlanStay->CalculablePricesString);
								unset($order->RatePlanStay->PackagesString);
								unset($order->RatePlanStay->MerchantBookingTypesString);
								unset($order->RatePlanStay->Policy);
								unset($order->RatePlanStay->SuggestedStay);
								unset($order->RatePlanStay->AllVariationsString);
								
								


								foreach($order->CalculatedPricesDetails as $pr) {
									unset($pr->OriginalDays);
									unset($pr->Days);
									unset($pr->Variations);
								}

								
	//							if($fromCart==0){
	//								$order->CheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->FromDate);
	//								$order->CheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->ToDate);
	//							}
															
								array_push($lstOrderStay, $order);
							}						
						}
					}
							
				}
	//		}

							
				return $lstOrderStay;
		}

		public static function AddToCart($tmpUserId, $language, $OrderJson, $ResetCart) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//WP->			$model = new BookingForConnectorModelOrders;
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->AddToCart($tmpUserId, $language, $OrderJson, $ResetCart);
		}	

		public static function AddToCartByExternalUser($tmpUserId, $language, $OrderJson, $ResetCart) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//WP->			$model = new BookingForConnectorModelOrders;
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->AddToCartByExternalUser($tmpUserId, $language, $OrderJson, $ResetCart);
		}	
		public static function DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//WP->			$model = new BookingForConnectorModelOrders;
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId);
		}	
		public static function AddDiscountCodesCartByExternalUser($tmpUserId, $language, $bficoupons) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//WP->			$model = new BookingForConnectorModelOrders;
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->AddDiscountCodesCartByExternalUser($tmpUserId, $language, $bficoupons);
		}	
		public static function GetCartByExternalUser($tmpUserId, $language, $includeDetails = true) {
			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//WP->			$model = new BookingForConnectorModelOrders;
			$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			return $model->GetCartByExternalUser($tmpUserId, $language, $includeDetails);
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
				$user = BFCHelper::getSession('bfiUser',null, 'bfi-User');

//				$user = JFactory::getUser();
				if ($user !=null && $user->CustomerId != 0) {
					$tmpUserId=$user->CustomerId."|". $user->Name . "|" . $_SERVER["SERVER_NAME"];
				}
				if(empty($tmpUserId)){
					$tmpUserId = uniqid($_SERVER["SERVER_NAME"]);
				}
				BFCHelper::setSession('tmpUserId', $tmpUserId , 'com_bookingforconnector');
			}
			return $tmpUserId;
		}	

		public static function bfi_sortOrder($a, $b)
		{
			return $a->SortOrder - $b->SortOrder;
		}
		public static function bfi_sortResourcesRatePlans($a, $b)
		{
			return $a->RatePlan->TotalDiscounted - $b->RatePlan->TotalDiscounted;
	//		return $a->RatePlan->SortOrder - $b->RatePlan->SortOrder;
		}
		public static function bfi_returnFilterCount($a, $b, $offset)
		{
			$currA = intval($a);
			$currB = $currA;
			if(isset($b[$offset])){
				$currB = intval($b[$offset]);
			}
	//		if($currA>$currB){
	//			return  "+" . ($currA - $currB);
	//		}

			return $currB;
		}

		public static function string_sanitize($s) {
			$result = preg_replace("/[^a-zA-Z0-9\s]+/", "", html_entity_decode($s, ENT_QUOTES));
			return $result;
		}

		public static function bfi_get_clientdata() {
			$ipClient = BFCHelper::bfi_get_client_ip();
			$ipServer = $_SERVER['SERVER_ADDR'];
			$uaClient = $_SERVER['HTTP_USER_AGENT'];
			$RequestTime = $_SERVER['REQUEST_TIME'];
			$Referer = $_SERVER['HTTP_REFERER'];
			$clientdata =
				"ipClient:" . str_replace( ":", "_", $ipClient) ."|".
				"ipServer:" . str_replace( ":", "_", $ipServer) ."|".
				"uaClient:" . str_replace( "|", "_", str_replace( ":", "_", $uaClient)) ."|".
				"Referer:" . str_replace( "|", "_", str_replace( ":", "_", $Referer)) ."|".
				"RequestTime:" . $RequestTime;
			return $clientdata;
		}
		public static function bfi_get_client_ip() {
			$ipaddress = '';
			if (isset($_SERVER['HTTP_CLIENT_IP']))
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_X_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			else if(isset($_SERVER['REMOTE_ADDR']))
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			else
				$ipaddress = 'UNKNOWN';
		 
			return $ipaddress;
		}

		public static function bfi_get_template($file, $args = array()) {
			if ( ! empty( $args ) && is_array( $args ) ) {
				extract( $args );
			}
			
//			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
			jimport('joomla.filesystem.folder');
			$app          = JFactory::getApplication();
			$explodeArray = explode(DIRECTORY_SEPARATOR, $file);
			$name         = end($explodeArray);
			$template = $app->getTemplate();
			$htmlPath   = JPath::clean(JPATH_ROOT . '/templates/' . $template . '/html/com_bookingforconnector/' . $file);


			if (!JFile::exists($htmlPath))
			{
				$htmlPath   = JPath::clean(JPATH_COMPONENT . '/views/' . $file);
			}
			if (!file_exists($htmlPath))
			{
				return;
			}
			
			include($htmlPath); 
			return ;
		}

		public static function bfi_get_module($module, $file = 'default.php', $args = array()) {
			if (empty( $module )) {
				return;
			}
			if ( ! empty( $args ) && is_array( $args ) ) {
				extract( $args );
			}
			
//			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
			jimport('joomla.filesystem.folder');
			$app          = JFactory::getApplication();
			$explodeArray = explode(DIRECTORY_SEPARATOR, $file);
			$name         = end($explodeArray);
			$template = $app->getTemplate();
			$htmlPath   = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $file);

			if (!JFile::exists($htmlPath))
			{
				$htmlPath   = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'tmpl'. DIRECTORY_SEPARATOR . $file);
			}
			if (!file_exists($htmlPath))
			{
				return;
			}
			include($htmlPath); 
			return ;
		}

}
}
//setting constants
if (!defined('COM_BOOKINGFORCONNECTOR_CONFIG_LOADED')) {
		$config = JComponentHelper::getParams('com_bookingforconnector');

		$subscriptionkey= $config->get('wsurl', '');
		$apikey= $config->get('apikey', '');
		$form_key= $config->get('formlabel', '');
		$XGooglePosDef = $config->get('posx', 0);
		$YGooglePosDef = $config->get('posy', 0);
		$startzoom = $config->get('startzoom',15);
		$googlemapskey = $config->get('googlemapskey','');
		$itemperpage = $config->get('bfi_itemperpage_key',10);
		$googlerecaptchakey = $config->get('bfi_googlerecaptcha_key','');
		$googlerecaptchasecretkey = $config->get('bfi_googlerecaptcha_secret_key','');
		$googlerecaptchathemekey = $config->get('bfi_googlerecaptcha_theme_key','light');
		$googlerecaptchasizekey = $config->get('bfi_googlerecaptcha_size_key','normal');

		$isportal = $config->get('isportal', 1);
		$showdata = $config->get('showdata', 1);
		$sendtocart = $config->get('bfi_sendtocart_key', 0);
		$showbadge = 1; //$config->get('bfi_showbadge_key', 1);

		$enablecoupon = $config->get('bfi_enablecoupon_key', 0);
		$showlogincart = $config->get('bfi_showlogincart_key', 1);

		$usessl = $config->get('bfi_usessl_key',0);
		$ssllogo = $config->get('bfi_ssllogo_key','');

		$useproxy = $config->get('useproxy',0);
		$urlproxy = $config->get('urlproxy','127.0.0.1:8888');
		
		$gaenabled = $config->get('gaenabled', 0);
		$gaaccount = $config->get('gaaccount', '');
		$eecenabled = $config->get('eecenabled', 0);
		$criteoenabled = $config->get('bfi_criteoenabled_key', 0);

		$enablecache = $config->get('enablecache', 1);

		$bfi_adultsage_key = $config->get('adultsage', 18);
		$bfi_adultsqt_key = $config->get('adultsqt', 2);
		$bfi_childrensage_key = $config->get('childrensage', 12);
		$bfi_senioresage_key = $config->get('senioresage', 65);
		$bfi_maxqtSelectable_key = $config->get('bfi_maxqtSelectable_key', 20);
		$bfi_defaultdisplaylist_key = $config->get('bfi_defaultdisplaylist_key', 0);
		

		$bfi_currentcurrency = $config->get('bfi_currentcurrency_key', '');

		$nMonthinCalendar = 2;

		$useragent=$_SERVER['HTTP_USER_AGENT'];

		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			$nMonthinCalendar = 1;
		}
		$bfi_version = $config->get('version', '');
		bfiDefine( 'BFI_VERSION', $bfi_version );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR', $nMonthinCalendar );
		$subscriptionkey = strtolower($subscriptionkey);
		if(strpos($subscriptionkey,'https://') !== false){
			$subscriptionkey = str_replace("https://", "", $subscriptionkey);
			$subscriptionkey = str_replace(".bookingfor.com/modules/bookingfor/services/bookingservice.svc", "", $subscriptionkey);
			$subscriptionkey = str_replace("/", "", $subscriptionkey);
		}
		$bfiBaseUrl = 'https://' . $subscriptionkey . '.bookingfor.com';

		$cachetime = $config->get('cachetime ', 3600); // 1 hour default
		$cachedir = $config->get('cachedir', 'cache/com_bookingforconnector');
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_CACHEDIR', $cachedir );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_CACHETIME', $cachetime );
		$datacrawler = file_get_contents(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'crawler-user-agents.json');
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_CRAWLER', $datacrawler );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY', $subscriptionkey );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_API_KEY', $apikey );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_FORM_KEY', $form_key );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_WSURL', $bfiBaseUrl .'/modules/bookingfor/services/bookingservice.svc' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ORDERURL', $bfiBaseUrl .'/Public/{language}/orderlogin' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_PAYMENTURL', $bfiBaseUrl .'/Public/{language}/payment/' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_PRIVACYURL', $bfiBaseUrl .'/Public/{language}/privacy' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL', $bfiBaseUrl .'/Public/{language}/termsofuse' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ACCOUNTLOGIN', $bfiBaseUrl .'/Public/{language}/?openloginpopup=1' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ACCOUNTREGISTRATION', $bfiBaseUrl .'/Public/{language}/Account/Register' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ACCOUNTFORGOTPASSWORD', $bfiBaseUrl .'/Public/{language}/Account/sendforgotpasswordlink' );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_CURRENTCURRENCY', $bfi_currentcurrency );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_MAXATTACHMENTFILES', 3 );
		
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_IMGURL', $subscriptionkey . '/bookingfor/images' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_IMGURL_CDN', '//cdnbookingfor.blob.core.windows.net/' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_BASEIMGURL', 'https://cdnbookingfor.blob.core.windows.net/' . $subscriptionkey . '/bookingfor/images' );
//		bfiDefine( 'COM_BOOKINGFORCONNECTOR_BASEIMGURL', 'https://cdnbookingfor.blob.core.windows.net/bibione/bookingfor/images' );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_POSX', $XGooglePosDef );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_POSY', $YGooglePosDef );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM', $startzoom );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY', $googlemapskey );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY', $googlerecaptchakey );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASECRETKEY', $googlerecaptchasecretkey );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHATHEMEKEY', $googlerecaptchathemekey );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASIZEKEY', $googlerecaptchasizekey );
		
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDER', false);
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM', "");
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE', "3,4");
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ITEMPERPAGE', $itemperpage );
		
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ISPORTAL', $isportal );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SHOWDATA', $showdata );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SENDTOCART', $sendtocart );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SHOWBADGE', $showbadge );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ENABLECOUPON', $enablecoupon );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SHOWLOGINCART', $showlogincart );
		
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_USESSL', $usessl );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SSLLOGO', $ssllogo );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ADULTSAGE', $bfi_adultsage_key );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ADULTSQT', $bfi_adultsqt_key );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_CHILDRENSAGE', $bfi_childrensage_key );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_SENIORESAGE', $bfi_senioresage_key );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_USEPROXY', $useproxy );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_URLPROXY', $urlproxy );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GAENABLED', $gaenabled );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_GAACCOUNT', $gaaccount );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_EECENABLED', $eecenabled );
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_CRITEOENABLED', $criteoenabled );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_ENABLECACHE', $enablecache );

		bfiDefine( 'COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE', $bfi_maxqtSelectable_key );
		
		bfiDefine( 'COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST', $bfi_defaultdisplaylist_key );

		$cryptoVersion = BFCHelper::encryptSupported();
		bfiDefine('COM_BOOKINGFORCONNECTOR_CRYPTOVERSION', $cryptoVersion);

		define('COM_BOOKINGFORCONNECTOR_CONFIG_LOADED', 1);




//		// load scripts
//		JHtml::_('jquery.framework');
//		JHTML::script('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
//
//		JHTML::stylesheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
//		JHTML::stylesheet('components/com_bookingforconnector/assets/jquery-ui/themes/smoothness/jquery-ui.min.css');
//		JHTML::stylesheet('components/com_bookingforconnector/assets/css/font-awesome.min.css');
//		JHTML::stylesheet('components/com_bookingforconnector/assets/css/magnific-popup.css');
//		JHTML::stylesheet('components/com_bookingforconnector/assets/js/webui-popover/jquery.webui-popover.min.css');
//		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
//
//		JHTML::script('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
//		JHTML::script('components/com_bookingforconnector/assets/js/jquery.form.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/jquery-validation/jquery.validate.min.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/jquery-validation/additional-methods.min.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/jquery.validate.additional-custom-methods.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/jquery.magnific-popup.min.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/webui-popover/jquery.webui-popover.min.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/jquery.shorten.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/bfi.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/bfisearchonmap.js');
//		JHtml::script('components/com_bookingforconnector/assets/js/bfi_calendar.js');
//		$document 	= JFactory::getDocument();
//		$language 	= JFactory::getLanguage()->getTag();
//		if(substr($language,0,2)!='en'){
//			JHtml::script('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
//		}

}
