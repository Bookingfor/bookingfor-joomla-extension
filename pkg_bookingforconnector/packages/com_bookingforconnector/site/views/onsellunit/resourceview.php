<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

//Vista base da cui derivano le altre viste di Resource.

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewOnSellUnitBase extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;

	// Overwriting JView display method
	function basedisplay($tpl = null) 
	{
		// Initialise variables
		$state		= $this->get('State');
		//$item		= $this->get('Item');

		//$document 	= JFactory::getDocument();
		//$language 	= $document->getLanguage();

		//$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
		
		//$document->addStyleSheet('components/com_bookingforconnector/assets/css/resource.css');

		$params = $state->params;

		// se è un pdf non elaboro tutte le richieste
		if($this->getLayout() != 'pdf') {
		
		
			// Check for errors.
			if (count($errors = $this->get('Errors'))) {
				BFCHelper::raiseWarning(500, implode("\n", $errors));
				return false;
			}
			
//			BFCHelper::setState($item->Merchant, 'merchant', 'resource');
			
			/* creating totals */			
		}
		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		//$this->assignRef('item', $item);

	}
	
	function setBreadcrumb($resource, $layout = '', $language) {
		if (!empty($resource)){
				$mainframe = JFactory::getApplication();
				$pathway   = $mainframe->getPathway();
				// resetto il pathway				
//				$pathway->setPathway(null);
				$count = count($pathway);
				$newPathway = array();
				if($count>1){
					$newPathway = array_pop($pathway);
				}
				$pathway->setPathway($newPathway);

//				$resourceName = BFCHelper::getLanguage($resource->Name, $language);
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
