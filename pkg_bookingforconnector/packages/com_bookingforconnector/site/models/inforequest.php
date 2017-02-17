<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';

/**
 * BookingForConnectorModelOrders Model
 */
class BookingForConnectorModelInfoRequests extends JModelList
{
	private $urlCreateInfoRequest = null;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlCreateOrder = '/CreateInfoRequest';
	}
	
	public function setInfoRequest($customerData = NULL, $suggestedStay = NULL, $creditCardData = NULL, $otherNoteData = NULL, $merchantId = 0, $type = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processInfoRequest = NULL, $mailFrom = NULL) {
		$options = array(
				'path' => $this->urlCreateInfoRequest,
				'data' => array(
					'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
					'suggestedStay' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($suggestedStay)),
					'otherNoteData' => BFCHelper::getQuotedString($otherNoteData),
//					'merchantId' => $merchantId,
					'infoRequestType' => BFCHelper::getQuotedString($type),
					'userNotes' => BFCHelper::getQuotedString($userNotes),
					'label' => BFCHelper::getQuotedString($label),
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'processInfoRequest' => $processInfoRequest,
					'mailFrom' => $mailFrom,
					'$format' => 'json'
				)
			);

		if (!empty($merchantId) && intval($merchantId)>0){
			$options['data']['merchantId'] = $merchantId;
		}

		$url = $this->helper->getQuery($options);
		
		$order = null;
		
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}
		return $order;		

	}
	
	protected function populateState($ordering = NULL, $direction = NULL) {
//		$filter_order = BFCHelper::getCmd('filter_order','Name');
//		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');
// 
//		$this->setState('params', array(
//			'typeId' => BFCHelper::getInt('typeId'),
//			'categoryId' => BFCHelper::getVar('categoryId')
//		));
		
		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		return $this->cache[$store];
	}
}
