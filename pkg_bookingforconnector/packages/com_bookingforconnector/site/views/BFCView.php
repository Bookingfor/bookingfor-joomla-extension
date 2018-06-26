<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


JHtml::_('jquery.framework');
JHtml::_('jquery.ui');



/**
 * HTML View class for the Bookingfor Component
 */
class BFCView extends JViewLegacy 
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication('site');
		$this->params  = $app->getParams();
		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		
		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		// load scripts and css
		bfi_load_scripts();
		
		// Display the view
		parent::display($tpl);
	}
	
	public function checkAnalytics($listName="") {
		return BFCHelper::checkAnalytics($listName);
	}
}