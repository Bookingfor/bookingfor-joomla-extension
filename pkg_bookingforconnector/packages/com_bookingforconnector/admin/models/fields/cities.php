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
JFormHelper::loadFieldClass('list');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
/**
 * 
 */
class JFormFieldCities extends JFormFieldList
{

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{

		
		$locationZones = BFCHelper::getLocations();
		$options = array();
		if ($locationZones)
		{
		foreach($locationZones as $lz)
			{
				if(!empty($lz->CityId)){
					$options[] = JHtml::_('select.option', $lz->CityId, $lz->Name);
				}
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
