<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
/**
 * 
 */
class JFormFieldCurrencyExchanges extends JFormFieldList
{

	public function getInput() {
		$currSelected = $this->value;
		if(empty( $currSelected )){
			$currSelected  = bfi_get_defaultCurrency();
		}
		$currencyExchanges = bfi_get_currencyExchanges();

		$currency_text = array('978' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_978'), //Euro
								'191' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_191'), //Kune
								'840' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_840'), //U.S. dollar
								'392' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_392'), //Japanese yen
								'124' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_124'), //Canadian dollar
								'36' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_36'), //Australian dollar
								'643' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_643'), //Russian Ruble
								'200' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_200'), //Czech koruna
								'702' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_702'), //Singapore dollar
								'826' => JTEXT::_('MOD_BOOKINGFORCURRENCYSWITCHER_826') //Pound sterling
							);
		
		if ($currencyExchanges)
		{
			$output = '<select id="'.$this->id.'" name="'.$this->name.'">';
			foreach($currencyExchanges as $currencyExchangeCode => $currencyExchange)
				{
					$options[] = JHtml::_('select.option', $currencyExchangeCode, $currency_text[$currencyExchangeCode] );
					$output .= '<option value="'.$currencyExchangeCode.'"';
					if($currencyExchangeCode == $currSelected) { $output .= 'selected="selected" '; }
					$output .= '>'.$currency_text[$currencyExchangeCode].'</option>';
				}
			$output .= '</select>';
		}
		return $output;
	}
}
