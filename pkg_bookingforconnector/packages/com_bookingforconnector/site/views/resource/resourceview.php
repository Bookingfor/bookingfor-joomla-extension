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
class BookingForConnectorViewResourceBase extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $gotCalculator = null;
	protected $stay = null;
	protected $total = 0;
	protected $totalDiscounted = 0;
	protected $totalWithVariation = 0;

	// Overwriting JView display method
	function basedisplay($tpl = null) 
	{
		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');

		$document 	= JFactory::getDocument();
		//$language 	= $document->getLanguage();

		//$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
		
		//$document->addStyleSheet('components/com_bookingforconnector/assets/css/resource.css');

		$params = $state->params;

		// se è un pdf non elaboro tutte le richieste
		if($this->getLayout() != 'pdf') {
		
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
			$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		

		if ($this->getLayout() == 'ratings') {
			$items = $this->get('ItemsRating');
			$pagination	= $this->get('PaginationRatings');
			// load scripts
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.expander.min.js');
		}
		elseif ($this->getLayout() == 'rating') {
			// load css
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.rating.css');
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');

			// load scripts
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.rating.pack.js');
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
			}
		}		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

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

		//		$pathway->addItem(
		//			$resource->Merchant->Name,
		//			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->Merchant->MerchantId . ':' . BFCHelper::getSlug($resource->Merchant->Name))
		//		);
				
		//		$pathway->addItem(
		//			JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_' . strtoupper($layout) ),
		//			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&layout=' . $layout . '&merchantId=' . $resource->Merchant->MerchantId . ':' . BFCHelper::getSlug($resource->Merchant->Name))
		//		);
						
		//		$resourceName = BFCHelper::getLanguage($resource->Name, $language);
				$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		//		echo "<pre>";
		//		echo print_r($resource);
		//		echo "</pre>";		
				$pathway->addItem(
					$resourceName,
					JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName))
				);
		}
	}
}
