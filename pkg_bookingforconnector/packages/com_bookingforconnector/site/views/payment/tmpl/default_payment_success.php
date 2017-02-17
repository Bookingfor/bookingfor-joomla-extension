<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$order = $this->item->order;
$crew = $this->getModel()->getCrewFromOrder($order->OrderId);
$resource = $this->getModel()->getResourceFromResourceId($order->RequestedItemId);
$tick_img = JURI::base() . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR . 'assets/images/tick-icon.png';
//echo '<PRE>';print_r($resource);print_r($this->getModel()->getCrewFromOrder($order->OrderId));print_r($order);die();
?>
<div class="thanku-section">
    <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
    		<div class="thanku-part">
            	<img src="<?php echo $tick_img; ?>" class="trick-pic">
                	<h3><?php echo  JText::_('COM_BOOKINGFORCONNECTOR_ORDER_VIEW_ORDER_NUMBER'); ?></h3>
                    <h3><?php echo $order->OrderId; ?></h3>
                    <h3>Gentile <span><?php echo $crew->FirstName; ?> <?php echo $crew->LastName; ?></span></h3>
                    <h3><?php echo  JText::_('COM_BOOKINGFORCONNECTOR_ORDER_VIEW_ORDER_THANKS_TEXT'); ?></h3>
            </div>
    </div>
    
    
    <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
    		<div class="thanku-part">
    			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
                	<div class="viaro-section">
                    	  <h3><?php echo $crew->FirstName; ?></h3>
                        <h3><?php echo $crew->LastName; ?></h3>
                        <h4><?php echo $crew->Address; ?></h4>
                        <h4><?php echo $crew->Phone; ?></h4>
                        <h4><?php echo $crew->Email; ?></h4>
                                                
                    </div>
             </div>
             <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
                	<div class="viaro-section">
                    	<h3><?php echo $resource->Name; ?></h3>
                        
                        <h4>dal</h4>
                        <h4>sab 25/06 </h4>
                        <h4>al</h4>
                        <h4>sab 02/07 </h4>
                        <h4>1 Camer\a, 2 adult</h4>
                        <h4><?php echo $resource->Address; ?></h4>
                        
                        <div class="left-text"><?php echo  JText::_('COM_BOOKINGFORCONNECTOR_ORDER_VIEW_ORDER_PRICE'); ?></div>
                        <div class="right-text">€ <?php echo number_format($order->TotalAmount, 2);?></div>
                                                
                    </div>
             </div>
    	</div>
    </div>
    
    
    <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
    		<div class="thanku-part">
            	<div class="thanku-heading">
                    <h3>Servizi aggiuntivi:</h3>
                    <h3>Ombrellone + Lettino Prezzo € 100,00</h3>
                </div>
                
                <div class="thanku-heading">
                	<h3>Carta di credito:</h3>
                    <h3>Numero: ************{Creditcard.Number}</h3>
                </div>
                
                <div class="thanku-heading">
                	<h3>Messaggio:</h3>
                	<h3>{Order.Notes}</h3>
                </div>
                
                <div class="total-heading"><h2>Totale: € 306,20</h2></div>
                
                <div class="button-area">
                	<ul>
                    	<li><a href="" class="btn btn-danger">Gestisci la tua prenotazione</a></li>
                        <li><a href="" class="btn btn-danger">Stampa la tua prenotazione</a></li>                        
                    </ul>                
                </div>
                
                <div class="address-heading">
                	<h3>Europa Tourist Group</h3>
                	<h4>Corso del Sole 102, 30028, Bibione (Venezia)</h4>
                    <h4>Tel. + 39 0431 430144</h4>
                </div>
                    
			</div>
    </div>    
    
</div>
