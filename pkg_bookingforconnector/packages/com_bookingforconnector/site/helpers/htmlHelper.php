<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//jimport( 'joomla.html.html.behavior' );
//jimport( 'joomla.html.html' );


class htmlHelper{
    /**
     * Displays a calendar control field
     *
     * @param   string  $value    The date value
     * @param   string  $name     The name of the text field
     * @param   string  $id       The id of the text field
     * @param   string  $format   The date format
     * @param   array   $attribs  Additional HTML attributes
     *
     * @return  string  HTML markup for a calendar field
     *
     * @since   11.1
     */
    public static function calendar($value, $name, $id, $inputformat = 'm/d/Y', $outputformat = 'm/d/Y', $formatjs = '%Y-%m-%d', $attribs = null, $usejquery = false, $jqueryOptions = array()) 
    {
        if ($usejquery) {
            return self::calendarJquery($value, $name, $id, $inputformat, $outputformat, $formatjs, $attribs, $jqueryOptions);
        }
        static $done;
        
        if ($done === null) {
            $done = array();
        }
        
        $readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
        $disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
        if (is_array($attribs)) {
            $attribs = JArrayHelper::toString($attribs);
        }
        
        $date = DateTime::createFromFormat($inputformat, $value);
        
        if (!$readonly && !$disabled) {
            // Load the calendar behavior
            JHtml::_('behavior.calendar');
            JHtml::_('behavior.tooltip');
            
            // Only display the triggers once for each control.
            if (!in_array($id, $done)) {
                $document = JFactory::getDocument();
                $document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
                // Id of the input field
                inputField: "' . $id . '",
                // Format of the input field
                ifFormat: "' . $formatjs . '",
                // Trigger for the calendar (button ID)
                button: "' . $id . '_img",
                // Alignment (defaults to "Bl")
                align: "Tl",
                singleClick: true,
                firstDay: ' . JFactory::getLanguage()->getFirstDay() . '
                });});');
                $done[] = $id;
            }
            
            
            
            return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($date->format($outputformat), ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />' . JHtml::_('image', 'system/calendar.png', JText::_('JLIB_HTML_CALENDAR'), array(
                'class' => 'calendar',
                'id' => $id . '_img'
            ), true);
        } else {
            return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" value="' . (0 !== (int) $value ? JHtml::_('date', $value, JFactory::getDbo()->getDateFormat()) : '') . '" ' . $attribs . ' /><input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($date->format($outputformat), ENT_COMPAT, 'UTF-8') . '" />';
        }
    }
    
    public static function calendarimage($value, $name, $id, $inputformat = 'm/d/Y', $outputformat = 'm/d/Y', $formatjs = '%Y-%m-%d', $attribs = null, $usejquery = false, $jqueryOptions = array()) {
        if ($usejquery) {
            return self::calendarimageJquery($value, $name, $id, $inputformat, $outputformat, $formatjs, $attribs, $jqueryOptions);
        }
        static $done;
        
        if ($done === null) {
            $done = array();
        }
        
        $readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
        $disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
        if (is_array($attribs)) {
            $attribs = JArrayHelper::toString($attribs);
        }
        
        $date = DateTime::createFromFormat($inputformat, $value);
        
        if (!$readonly && !$disabled) {
            // Load the calendar behavior
            JHtml::_('behavior.calendar');
            JHtml::_('behavior.tooltip');
            
            // Only display the triggers once for each control.
            if (!in_array($id, $done)) {
                $document = JFactory::getDocument();
                $document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
                // Id of the input field
                inputField: "' . $id . '",
                // Format of the input field
                ifFormat: "' . $formatjs . '",
                // Trigger for the calendar (button ID)
                button: "' . $id . '_img",
                // Alignment (defaults to "Bl")
                align: "Tl",
                singleClick: true,
                firstDay: ' . JFactory::getLanguage()->getFirstDay() . '
                });});');
                $done[] = $id;
            }
            
            
            
            return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($date->format($outputformat), ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />' . JHtml::_('image', 'system/calendar.png', JText::_('JLIB_HTML_CALENDAR'), array(
                'class' => 'calendar',
                'id' => $id . '_img'
            ), true);
        } else {
            return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" value="' . (0 !== (int) $value ? JHtml::_('date', $value, JFactory::getDbo()->getDateFormat()) : '') . '" ' . $attribs . ' /><input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($date->format($outputformat), ENT_COMPAT, 'UTF-8') . '" />';
        }
    }
    
    public static function calendarJquery($value, $name, $id, $inputformat = 'm/d/Y', $outputformat = 'm/d/Y', $formatjs = '%Y-%m-%d', $attribs = null, $jqueryOptions = array()) {
        static $done;
        
        if ($done === null) {
            $done = array();
        }
        
        $readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
        $disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
        
        if (is_array($attribs)) {
            $attribs = JArrayHelper::toString($attribs);
        }
        
        $date = DateTime::createFromFormat($inputformat, $value);
        
        if (!$readonly && !$disabled) {
            // Only display the triggers once for each control.
            if (!in_array($id, $done)) {
                $document  = JFactory::getDocument();
                $optString = '';
                foreach ($jqueryOptions as $key => $option) {
                    $optString .= ', ' . $key . ': ' . $option;
                }
                $scriptDeclare = '
                var ' . $id . ' = null;
                jQuery(function($) {
                    ' . $id . ' = function() { $("#' . $id . '").datepicker({
                        defaultDate: "+2d"
                        ,changeMonth: true
                        ,changeYear: true
                        ,dateFormat: "' . $formatjs . '"
                        ' . $optString . '
                    })};
                    ' . $id . '();
                });
                ';
                
                $document->addScriptDeclaration($scriptDeclare);
                $done[] = $id;
            }
            
            return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($date->format($outputformat), ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />';
        } else {
            return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" value="' . (0 !== (int) $value ? JHtml::_('date', $value, JFactory::getDbo()->getDateFormat()) : '') . '" ' . $attribs . ' />';
        }
    }
    
    public static function calendarimageJquery($value, $name, $id, $inputformat = 'm/d/Y', $outputformat = 'm/d/Y', $formatjs = '%Y-%m-%d', $attribs = null, $jqueryOptions = array()) {
        static $done;
        
        if ($done === null) {
            $done = array();
        }
        
        $readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
        $disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
        
        if (is_array($attribs)) {
            $attribs = JArrayHelper::toString($attribs);
        }
        
        $date = DateTime::createFromFormat($inputformat, $value);
        
        if (!$readonly && !$disabled) {
            // Only display the triggers once for each control.
            if (!in_array($id, $done)) {
                $document  = JFactory::getDocument();
                $optString = '';
                foreach ($jqueryOptions as $key => $option) {
                    $optString .= ', ' . $key . ': ' . $option;
                }
                $scriptDeclare = '
                var ' . $id . ' = null;
                jQuery(function($) {
                    ' . $id . ' = function() { $("#' . $id . '").datepicker({
                        defaultDate: "+2d"
                        ,dateFormat: "' . $formatjs . '"
                        ' . $optString . '
                    })};
                    ' . $id . '();
                });
                ';
                
                $document->addScriptDeclaration($scriptDeclare);
                $done[] = $id;
            }
        }
    }
    
}
