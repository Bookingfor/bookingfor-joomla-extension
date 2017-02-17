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
class BookingForConnectorModelCriteo extends JModelList
{
		
	private $helper = null;
	private $urlGetOrdersCount = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->GetCriteoConfiguration = '/GetCriteoConfiguration';
	}
	public function getCriteoConfiguration($pagetype = 0, $merchantsList = array(), $orderId = null)
	{
		
		$sessionkey = 'criteoenabled' ;
		$criteoenabled = BFCHelper::getSession($sessionkey, 1 , 'com_bookingforconnector');
		$return = null;

		if($criteoenabled==1){
			$language = JFactory::getLanguage()->getTag();

			$options = array(
					'path' => $this->GetCriteoConfiguration,
					'data' => array(
						'cultureCode' => BFCHelper::getQuotedString($language),
						'$format' => 'json',
						'pagetype' => $pagetype,
						'callerUrl' => BFCHelper::getQuotedString(JURI::current()),
						'merchantsList' => BFCHelper::getQuotedString(join(',', $merchantsList))
					)
			);
			if(isset($orderId) && !empty($orderId)) {
				$options["data"]["orderId"] = $orderId;
			}
			$url = $this->helper->getQuery($options);
			
			
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$return = $res->d->results->GetCriteoConfiguration;
				}elseif(!empty($res->d)){
					$return = json_decode($res->d->GetCriteoConfiguration);
				}
			}
			$criteoenabled = 0;
			if (isset($return) && isset($return->enabled)) {
				$criteoenabled = 0;
			}
			
			BFCHelper::setSession($sessionkey, $criteoenabled,'com_bookingforconnector');
		}


		return $return;
	}
}