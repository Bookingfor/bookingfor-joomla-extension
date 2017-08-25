<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of com_bookingforconnector component.
 *
 * The name of this class is dependent on the component being installed.
 * The class name should have the component's name, directly followed by
 * the text InstallerScript (ex:. com_helloWorldInstallerScript).
 *
 * This class will be called by Joomla!'s installer, if specified in your component's
 * manifest file, and is used for custom automation actions in its installation process.
 *
 * In order to use this automation script, you should reference it in your component's
 * manifest file as follows:
 * <scriptfile>script.php</scriptfile>
 *
 * @package     bookingforconnector
 * @subpackage  com_bookingforconnector
 *
 * @copyright   Copyright (c)2006-2017 Ipertrade s.r.l. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
class com_bookingforconnectorInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent) 
    {
        $parent->getParent()->setRedirectURL('index.php?option=com_bookingforconnector');
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {
        echo '<p>' . JText::_('COM_BOOKINGFORCONNECTOR_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {
        echo '<p>' . JText::sprintf('COM_BOOKINGFORCONNECTOR_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * Runs just before any installation action is preformed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {
        echo '<p>' . JText::_('COM_BOOKINGFORCONNECTOR_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * Runs right after any installation action is preformed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
	public function postflight($action, $installer)
    {
        echo '<p>' . JText::_('COM_BOOKINGFORCONNECTOR_POSTFLIGHT_' . $action . '_TEXT') . '</p>';
		if ($action === 'install' || $action === 'update'  )
		{
				/*
				 * Do a check if the menu item exists, skip if it does. Only needed when we are in pre stable state.
				 */
				$db = JFactory::getDbo();
				
				$query = $db->getQuery(true)
					->select('extension_id')
					->from($db->quoteName('#__extensions'))
					->where($db->quoteName('type') . ' = ' . $db->quote('component'))
					->where($db->quoteName('element') . ' = ' . $db->quote('com_bookingforconnector'));

				$componentid = $db->setQuery($query)->loadResult();

				$query = $db->getQuery(true)
					->select('id')
					->from($db->quoteName('#__menu_types'))
					->where($db->quoteName('menutype') . ' = ' . $db->quote('bookingfor'))
					->where($db->quoteName('title') . ' = ' . $db->quote('Bookingfor'));

				$bookingforMenu = $db->setQuery($query)->loadResult();

				if (empty($bookingforMenu))
				{
					/*
					 * Add a menu item for com_associations, we need to do that here because with a plain sql statement we
					 * damage the nested set structure for the menu table
					 */
					$newMenuType = JTable::getInstance('MenuType');

					$data              = array();
					$data['menutype']  = 'bookingfor';
					$data['title']     = 'Bookingfor';

					if (!$newMenuType->save($data))
					{
						// Install failed, roll back changes
						$installer->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_ROLLBACK', $newMenuType->getError()));

						return false;
					}

					$bookingforMenu = $newMenuType->id;
				}

				/*
				* make menu items for:
				* merchants
				* resources
				* for all languages
				*/
				$languages = JLanguageHelper::getLanguages('lang_code');
				$itemsMenu = array();
				$itemsMenu['en-GB'] = array(
					array('Search availability','search-availability','search-availability','index.php?option=com_bookingforconnector&view=search'),
					array('Accommodation details ','accommodation-details','accommodation-details','index.php?option=com_bookingforconnector&view=resource'),
					array('Merchant details','merchant-details','merchant-details','index.php?option=com_bookingforconnector&view=merchantdetails'),
					array('Properties For sale','properties-for-sale','properties-for-sale','index.php?option=com_bookingforconnector&view=searchonsell'),
					array('Property for sale','property-for-sale','property-for-sale','index.php?option=com_bookingforconnector&view=onsellunit'),
					array('Cartdetails','cart','cart','index.php?option=com_bookingforconnector&view=cart'),
					array('Orders','orders','orders','index.php?option=com_bookingforconnector&view=orders'),
					array('Payment','payment','payment','index.php?option=com_bookingforconnector&view=payment'),
					
					);
				$itemsMenu['it-IT'] = array(
					array('Ricerca Disponibilità','ricerca-disponibilita','ricerca-disponibilita','index.php?option=com_bookingforconnector&view=search'),
					array('Risorsa ','risorsa','risorsa','index.php?option=com_bookingforconnector&view=resource'),
					array('Scheda esercente','scheda-esercente','scheda-esercente','index.php?option=com_bookingforconnector&view=merchantdetails'),
					array('Case in Vendita','ricerca-vendite','ricerca-vendite','index.php?option=com_bookingforconnector&view=searchonsell'),
					array('Annuncio in vendita','annuncio-in-vendita','annuncio-in-vendita','index.php?option=com_bookingforconnector&view=onsellunit'),
					array('Carrello','carrello','carrello','index.php?option=com_bookingforconnector&view=cart'),
					array('Ordini','ordini','ordini','index.php?option=com_bookingforconnector&view=orders'),
					array('Pagamento','pagamento','pagamento','index.php?option=com_bookingforconnector&view=payment'),
					);
				$itemsMenu['de-DE'] = array(
					array('Verfügbarkeit','verfuegbarkeit','verfuegbarkeit','index.php?option=com_bookingforconnector&view=search'),
					array('Unterkunft Info ','unterkunft-info','unterkunft-info','index.php?option=com_bookingforconnector&view=resource'),
					array('Händlerinfos','haendlerinfos','haendlerinfos','index.php?option=com_bookingforconnector&view=merchantdetails'),
					array('Immobilien verkauf ','immobilien-verkauf','immobilien-verkauf','index.php?option=com_bookingforconnector&view=searchonsell'),
					array('Haus verkaufen','haus-verkaufen','haus-verkaufen','index.php?option=com_bookingforconnector&view=onsellunit'),
					array('Warenkorb','warenkorb','warenkorb','index.php?option=com_bookingforconnector&view=cart'),
					array('Bestellung','bestellung','bestellung','index.php?option=com_bookingforconnector&view=orders'),
					array('Zahlung','zahlung','zahlung','index.php?option=com_bookingforconnector&view=payment'),
					);

				if (empty($languages))
				{
					
					foreach ($itemsMenu['en-GB'] as $itemMenu)
					{
						$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $itemMenu[3] ) .' AND language='.$db->Quote('*').'  AND published = 1 LIMIT 1' );
						$itemId = intval($db->loadResult());
						if ($itemId==0){
							$newMenuItem = JTable::getInstance('Menu');

							$data              = array();
							$data['menutype']  = 'bookingfor';
							$data['title']     = $itemMenu[0];
							$data['alias']     = $itemMenu[1];
							$data['path']      = $itemMenu[2];
							$data['link']      = $itemMenu[3];
							$data['type']      = 'component';
							$data['published'] = 1;
							$data['parent_id'] = 1;
							$data['component_id'] = $componentid;
							$data['language']     = '*';
							$data['client_id']    = 0;

							$newMenuItem->setLocation($data['parent_id'], 'last-child');

							if (!$newMenuItem->save($data))
							{
//								// Install failed, roll back changes
//								$installer->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_ROLLBACK', $newMenuItem->getError()));
//
//								return false;
							}
						}

					}

				}else{

					foreach ($languages as $key => $lang)
					{
						$language = $lang->lang_code;
						foreach ($itemsMenu[$language] as $itemMenu)
						{
							$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $itemMenu[3] ) .' AND language='.$db->Quote('*').'  AND published = 1 LIMIT 1' );
							$itemId = intval($db->loadResult());
							if ($itemId==0){
								$newMenuItem = JTable::getInstance('Menu');

								$data              = array();
								$data['menutype']  = 'bookingfor';
								$data['title']     = $itemMenu[0];
								$data['alias']     = $itemMenu[1];
								$data['path']      = $itemMenu[2];
								$data['link']      = $itemMenu[3];
								$data['type']      = 'component';
								$data['published'] = 1;
								$data['parent_id'] = 1;
								$data['component_id'] = $componentid;
								$data['language']     = $language;
								$data['client_id']    = 0;

								$newMenuItem->setLocation($data['parent_id'], 'last-child');

								if (!$newMenuItem->save($data))
								{
//									// Install failed, roll back changes
//									$installer->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_ROLLBACK', $newMenuItem->getError()));
//
//									return false;
								}
							}
						}
					}
				}



		}

		return true;
    }
}