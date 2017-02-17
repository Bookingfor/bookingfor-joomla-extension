<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class BookingForConnectorViewBookingForConnector extends JViewLegacy
{
	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Set the toolbar
		$this->addToolBar();
 
		$this->sidebar = JHtmlSidebar::render();
		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JToolBarHelper::title(JText::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_TITLE'), 'BookingFor Connector');
//		JToolBarHelper::title('BookingFor Connector');
//JToolBarHelper::addNew('helloworld.add');
		JToolBarHelper::preferences('com_bookingforconnector');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_TITLE'));
	}
}
