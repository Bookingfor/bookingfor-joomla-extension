<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewCondominium extends BFCView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$params 	= $state->params;

		$this->state = $state;
		$this->params = $params;
		$this->item = $item;
		$this->config = $config;
		$this->language = $language;
		
		$resource = $this->item;
		$merchant = $resource->Merchant;
		$currencyclass = bfi_get_currentCurrency();
		$resourceId = 0;
		$condominiumId = resource->condominiumId;

		if(!empty(BFCHelper::getVar('refreshcalc',''))){
			bfi_setSessionFromSubmittedData();
		}
		$merchants = array();
		$merchants[] = $merchant->MerchantId;
		$criteoConfig = null;
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
		}
	
				
		$analyticsEnabled = $this->checkAnalytics("") && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		$this->assignRef('criteoConfig', $criteoConfig);			
		
		$this->setLayout('calc');
		
		parent::display($tpl);
	}
}
