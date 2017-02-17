<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('checkboxes');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
/**
 * 
 */
class JFormFieldMerchantCategories extends JFormFieldList
{

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$merchantCategories = BFCHelper::getMerchantCategories();
		$options = array();
		if ($merchantCategories)
		{
		foreach($merchantCategories as $merchantCategory)
			{
//				$currOpt = JHtml::_('select.option', $merchantCategory->MerchantCategoryId, BFCHelper::getLanguage($merchantCategory->Name, $language));
//				$currOpt->checked = null;
//				$options[] = $currOpt;
				$options[] = JHtml::_('select.option', $merchantCategory->MerchantCategoryId, BFCHelper::getLanguage($merchantCategory->Name, $language));
			}
		}
		$options = array_merge(parent::getOptions(), $options);
				
		return $options;
	}
}
