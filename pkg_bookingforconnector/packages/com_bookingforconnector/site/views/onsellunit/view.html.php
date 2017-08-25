<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Bookingforconnector Component
 */
class BookingForConnectorViewOnSellUnit extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $language = null;
	protected $params = null;
	// Overwriting JView display method
	function display($tpl = null)
	{

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$params		= $this->params;		

		$this->state = $state;
		$this->params = $params;
		$this->item = $item;
		$this->config = $config;
		$this->sitename = $sitename;
		$this->language = $language;
		
		$resource = $this->item;
		$merchant = $resource->Merchant;

		if($this->checkAnalytics("Sales Resources Page") && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$obj = new stdClass;
			$obj->id = "" . $item->ResourceId . " - Sales Resource";
			$obj->name = $item->Name;
			$obj->category = $item->MerchantCategoryName;
			$obj->brand = $item->MerchantName;
			$obj->variant = 'NS';
			$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
		}
		
		$this->setBreadcrumb($item, 'onsellunits', $language);
		
		parent::display($tpl);
	}
	function setBreadcrumb($resource, $layout = '', $language) {
		if (!empty($resource)){
				$mainframe = JFactory::getApplication();
				$pathway   = $mainframe->getPathway();
				$count = count($pathway);
				$newPathway = array();
				if($count>1){
					$newPathway = array_pop($pathway);
				}
				$pathway->setPathway($newPathway);

				$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

				$pathway->addItem(
					$resource->MerchantName,
					JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName))
				);

				$pathway->addItem(
					$resourceName,
					JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName))
				);
		}
	}
}
