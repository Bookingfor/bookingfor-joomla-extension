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

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$document		= JFactory::getDocument();
$language 	= $document->getLanguage();
$mainframe = JFactory::getApplication();
$currentComponent = BFCHelper::getCmd( 'option' );
$currentView = BFCHelper::getCmd( 'view' );

//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

//-----------------------------------------------------------------------------------------
//	implementazione css 
//	per uno style personalizzato usare il seguente file css nella cartella css del template
//	"mod_bookingforsearchbymerchanttype.css"
//-----------------------------------------------------------------------------------------

JHTML::stylesheet('modules/'.$module->module.'/assets/style.css');

if (is_file(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css")){
	JHTML::stylesheet('templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css");
}

$document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/js/bootstrap-select/css/bootstrap-select.min.css');
$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
//		$document->addScript('components/com_bookingforconnector/assets/js/bootstrap-select/js/bootstrap-select.min.js');
$document->addScript('components/com_bookingforconnector/assets/js/bf.js');

JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
JHTML::stylesheet('modules/'.$module->module.'/assets/css/style.css');

JHTML::script('components/com_bookingforconnector/assets/js/jquery.blockUI.js');


JHTML::stylesheet('modules/'.$module->module.'/assets/css/slick-theme.css');
$document->addStyleSheet('//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css');
$document->addScript('//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js');



require JModuleHelper::getLayoutPath('mod_bookingformerchants', $params->get('layout', 'default'));