<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
if(BFCHelper::getVar( 'view')!=="search") return ;


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$document		= JFactory::getDocument();
$language 	= $document->getLanguage();
$mainframe = JFactory::getApplication();
$db   = JFactory::getDBO();

//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

//-----------------------------------------------------------------------------------------
//	implementazione css 
//	per uno style personalizzato usare il seguente file css nella cartella css del template
//	"mod_bookingforsearchbymerchanttype.css"
//-----------------------------------------------------------------------------------------

JHTML::stylesheet('modules/'.$module->module.'/assets/css/style.css');

if (is_file(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css")){
	JHTML::stylesheet('templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css");
}

JHTML::script('components/com_bookingforconnector/assets/js/jquery.form.js');
JHTML::script('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
JHTML::script('components/com_bookingforconnector/assets/js/jquery.ui.touch-punch.min.js');

$uri  = 'index.php?option=com_bookingforconnector&view=search';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND language='. $db->Quote($language) .' AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId.'' );
else
	$formAction = JRoute::_($uri);

require JModuleHelper::getLayoutPath('mod_bookingforsearchfilter', $params->get('layout', 'default'));
