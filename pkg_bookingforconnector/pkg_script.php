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
 * @license   GNU General Public License version 3, or later
 */
class pkg_bookingforconnectorInstallerScript
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
        $parent->getParent()->setRedirectURL('index.php?option=com_config&view=component&component=com_bookingforconnector');
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
//        echo '<p>' . JText::_('COM_BOOKINGFORCONNECTOR_UNINSTALL_TEXT') . '</p>';
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
//        echo '<p>' . JText::sprintf('COM_BOOKINGFORCONNECTOR_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
        $parent->getParent()->setRedirectURL('index.php?option=com_config&view=component&component=com_bookingforconnector');
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
//        echo '<p>' . JText::_('COM_BOOKINGFORCONNECTOR_PREFLIGHT_' . $type . '_TEXT') . '</p>';
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
    function postflight($type, $parent) 
    {
//        echo '<p>' . JText::_('COM_BOOKINGFORCONNECTOR_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
    }
}