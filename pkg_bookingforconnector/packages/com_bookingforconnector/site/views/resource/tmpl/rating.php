<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$config = $this->config;
$isportal = $config->get('isportal', 1);
$usessl = $config->get('usessl', 0);
$ssllogo = $config->get('ssllogo','');
$formlabel = $config->get('formlabel','');

$sitename = $this->sitename;

$language = $this->language;
$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());

$resource = $this->item;
$merchant = $resource->Merchant;

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());

$uriMerchant.='&merchantId=' . $this->item->MerchantId . ':' . BFCHelper::getSlug($this->item->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$route = JRoute::_($uriMerchant);
$uriMerchantthanks = $uriMerchant .'&layout=thanks';
$uriMerchantthanksKo = $uriMerchant .'&layout=errors';

$routeThanks = JRoute::_($uriMerchantthanks);
$routeThanksKo = JRoute::_($uriMerchantthanksKo);



$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";

if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	$cap = $resource->ZipCode;
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
}else{
	$addressData = $resource->AddressData;
	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
	$cap = BFCHelper::getItem($addressData, 'cap');
	$comune =  BFCHelper::getItem($addressData, 'comune');
	$provincia = BFCHelper::getItem($addressData, 'provincia');
}
if (empty($indirizzo) && empty($comune) ){

	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
		if (empty($indirizzo)){
			$indirizzo = $resource->MrcAddress;
			$cap = $resource->MrcZipCode;
			$comune = $resource->MrcCityName;
			$provincia = $resource->MrcRegionName;
		}
	}else{
		$addressData = $merchant->AddressData;
		$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
		$cap = BFCHelper::getItem($addressData, 'cap');
		$comune =  BFCHelper::getItem($addressData, 'comune');
		$provincia = BFCHelper::getItem($addressData, 'provincia');
	}
}


$merchantname = BFCHelper::getLanguage($merchant->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));

$hashorder = BFCHelper::getVar('hash');
if (empty($hashorder)){
	if ($merchant->RatingsContext !== 2 && $merchant->RatingsContext !== 3) {
	//redirect alla risorsa senza possibilità di renensirla
				header ("Location: ". JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1)); 
				$app = JFactory::getApplication();
				$app->close();
	}
}
$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$user = JFactory::getUser();
 
//if (!$user->guest) {
//  echo 'You are logged in as:<br />';
//  echo 'User name: ' . $user->username . '<br />';
//  echo 'Real name: ' . $user->name . '<br />';
//  echo 'User ID  : ' . $user->id . '<br />';
//}
//echo ("<pre>");	
//echo (print_r($user));	
//echo ("</pre>");	

?>
<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> ">
	<?php if ($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) :?>
		<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RETURN') ,array('class' => ' pull-right'));?>
	<?php endif; ?>
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> 
		<span class="com_bookingforconnector_resource-rating com_bookingforconnector_resource-rating<?php echo  $merchant->Rating ?>">
			<!-- <span class="com_bookingforconnector_resource-ratingText">Rating <?php echo  $merchant->Rating ?></span> -->
		</span>
	</h2>
	<div class="com_bookingforconnector_resource-address">
		<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong>
		<?php if(COM_BOOKINGFORCONNECTOR_ENABLEFAVORITES):?>
			 - <a href="javascript:addCustomURlfromfavTranfert('.com_bookingforconnector_resource-name','<?php echo JURI::current() ?>','<?php echo  $resourceName ?>')" class="com_bookingforconnector_resource_addfavorites"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_RESOURCE_ADDFAVORITES') ?></a>
		<?php endif ?>
	</div>	
	<div class="clear"></div>
	<br />
	<!-- Pagina di riassunto delle recensioni  -->
	<?php

/* recupero i dati dell'ordine se viene passato*/
$showForm = true;

$name = $user->name;
$email = $user->email;
$ratingError=0;
$merchantId = $this->item->MerchantId;
$jdate  = new JDate('now'); // 3:20 PM, December 1st, 2012
$endjdate  = new JDate('now -1 year'); // 3:20 PM, December 1st, 2012

$listDateArray = array();
while ($jdate > $endjdate) {
	$listDateArray[$jdate->format('Ym01')] = $jdate->format('F Y');
	$jdate->modify('-1 month');
}

$selectdate = true;
$nation="";
$listfiltered="";
$checkin="";
$city="";

if (!empty($hashorder)){
//	 controllo se ho un ordine
	$orderid = BFCHelper::decrypt($hashorder);

//	controllo se è un ordine numerico	
	if (is_numeric($orderid))
	{
//		controllo se esiste già una recensione per quell'ordine altrimenti no la faccio vedere
		$ratingCount =  BFCHelper::getTotalRatingsByOrderId($orderid);
		if ($ratingCount>0){
			//ordine con già una recensione
			$ratingError=2;
			$showForm = false;		
		}else{
			$order  = BFCHelper::getSingleOrderFromService($orderid);
	//		controllo se esiste l'ordine
			if(isset($order) && ($order->Status===5 || $order->Status===20)){
				$dateCheckin = BFCHelper::parseJsonDate($order->StartDate,'Y-m-d');
				$dateCheckin  = new JDate($dateCheckin);
				$dateCheckout = BFCHelper::parseJsonDate($order->EndDate,'Y-m-d');
				$dateCheckout  = new JDate($dateCheckout);
				$expirationjdate  = new JDate('now -1 month'); // 3:20 PM, December 1st, 2012
				$checkin = $dateCheckin->format('Ym01');
																														
				if(($dateCheckout<$expirationjdate || $dateCheckout>$jdate) && !array_key_exists($checkin,$listDateArray) ){
					$ratingError=5;
					$showForm = false;
				}
				$selectdate = false ;
				$name = BFCHelper::getItem($order->CustomerData, 'nome');
				$email = BFCHelper::getItem($order->CustomerData, 'email');
				$nation = BFCHelper::getItem($order->CustomerData, 'stato');
				$city = BFCHelper::getItem($order->CustomerData, 'citta');
				$unitiid = BFCHelper::getItem($order->NotesData, 'idunita','unita');
	//			controllo se l'ordine è riferito a questa risorsa
				if(!isset($unitiid) && $unitiid!==$resource->ResourceId ){
					// ordine non riferito a questa risorsa
					$ratingError=4;
					$showForm = false;
				}
			}else{
				// ordine inesistente o a cui non è possibile associare una recensione 
				$ratingError=3;
				$showForm = false;		
			}
		}
	}else{
		//ordine non numerico
		$ratingError=1;
		$showForm = false;
	}

}


$list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
$aNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));

$nationsList = JHTML::_('select.genericlist',$aNationList, 'nation','class="'. COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL .'12"','value', 'text', $nation);

// rimuovo il "Tutti"
unset($list[0]);
$genericlist = JHTML::_('select.genericlist', $list, 'typologyid',null, 'value', 'text', $listfiltered);

$formRoute = "index.php?option=com_bookingforconnector&task=sendRating"; 

$privacy = BFCHelper::GetPrivacy($this->language);

$listDate = JHTML::_('select.genericlist',$listDateArray, 'checkin','','value', 'text', $checkin);


?>
<!-- {emailcloak=off} -->

<br /><br />
<div class="alert alert-success" id="msgOk" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">x</a>
	<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_OK') ?>
</div>
<div class="alert alert-error" id="msgKo" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">x</a>
	<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_KO') ?>
</div>
<?php if ($showForm) :?>
<form action="<?php echo $formRoute; ?>" method="post" class="form-horizontal" id="formRating">
	<input type="hidden" id="hashorder" name="hashorder" value="<?php echo $hashorder ?>">
	<input type="hidden" id="merchantid" name="merchantid" value="<?php echo $merchantId ?>">
	<input type="hidden" id="resourceId" name="resourceId" value="<?php echo $resource->ResourceId  ?>">
	<input type="hidden" id="cultureCode" name="cultureCode" value="<?php echo $this->language ?>">
	<input type="hidden" id="label" name="label" value="<?php echo $formlabel ?>" />
	<input type="hidden" id="redirect" name="Redirect" value="<?php echo $routeThanks;?>" />
	<input type="hidden" id="redirecterror" name="Redirecterror" value="<?php echo $routeThanksKo;?>" />

	<div class="com_bookingforconnector_rating">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NAME') ?></label>
				<input name="name" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" placeholder="" value="<?php echo $name ; ?>" >    
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CITY') ?></label>
				<input name="city" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 input-mini" placeholder="" value="<?php echo $city ; ?>" >   
			</div><!--/span-->
		</div><!--/row-->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_TYPOLOGY') ?></label>
				<?php echo $genericlist; ?>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NATION') ?></label>
				<?php echo $nationsList; ?>
			</div><!--/span-->
		</div><!--/row-->                              
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_EMAIL') ?>*</label>
				<input name="email" id="email" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 input-mini" placeholder="email" value="<?php echo $email; ?>" >    
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_EMAILCONFIRM') ?>*</label>
				<input name="email2" id="email2" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 input-mini" placeholder="email" value="<?php echo $email; ?>" >    
			</div><!--/span-->
		</div><!--/row-->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CHECKINDATEFROM_LABEL') ?></label>
				<?php if($selectdate) :?>
					<?php echo $listDate; ?>
				<?php else: ?>
					<input type="hidden" id="checkin" name="checkin" value="<?php echo $checkin ?>">
					<?php echo $listDateArray[$checkin] ?>
				<?php endif // $selectdate ?>   
			</div><!--/span-->
		</div><!--/row-->                              

		<br />
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE1') ?>:
					<input type="hidden" id="hfvalue1" name="hfvalue1" value="6">
					<span id="starscap1">6</span><br />
					<input title="1" type="radio" value="1" name="personale" class="starswrapper1 required">
					<input title="2" type="radio" value="2" name="personale" class="starswrapper1">
					<input title="3" type="radio" value="3" name="personale" class="starswrapper1">
					<input title="4" type="radio" value="4" name="personale" class="starswrapper1">
					<input title="5" type="radio" value="5" name="personale" class="starswrapper1">
					<input title="6" type="radio" checked value="6" name="personale" class="starswrapper1">
					<input title="7" type="radio" value="7" name="personale" class="starswrapper1">
					<input title="8" type="radio" value="8" name="personale" class="starswrapper1">
					<input title="9" type="radio" value="9" name="personale" class="starswrapper1">
					<input title="10" type="radio" value="10" name="personale" class="starswrapper1">
				<br />
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE2') ?>:
					<input type="hidden" id="hfvalue2" name="hfvalue2" value="6">
					<span id="starscap2">6</span><br />
					<input title="1" type="radio" value="1" name="servizi" class="starswrapper2 required">
					<input title="2" type="radio" value="2" name="servizi" class="starswrapper2">
					<input title="3" type="radio" value="3" name="servizi" class="starswrapper2">
					<input title="4" type="radio" value="4" name="servizi" class="starswrapper2">
					<input title="5" type="radio" value="5" name="servizi" class="starswrapper2">
					<input title="6" type="radio" checked value="6" name="servizi" class="starswrapper2">
					<input title="7" type="radio" value="7" name="servizi" class="starswrapper2">
					<input title="8" type="radio" value="8" name="servizi" class="starswrapper2">
					<input title="9" type="radio" value="9" name="servizi" class="starswrapper2">
					<input title="10" type="radio" value="10" name="servizi" class="starswrapper2">
				<br />
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE3') ?>:
					<input type="hidden" id="hfvalue3" name="hfvalue3" value="6">
					<span id="starscap3">6</span><br />
					<input title="1" type="radio" value="1" name="pulizia" class="starswrapper3 required">
					<input title="2" type="radio" value="2" name="pulizia" class="starswrapper3">
					<input title="3" type="radio" value="3" name="pulizia" class="starswrapper3">
					<input title="4" type="radio" value="4" name="pulizia" class="starswrapper3">
					<input title="5" type="radio" value="5" name="pulizia" class="starswrapper3">
					<input title="6" type="radio" checked value="6" name="pulizia" class="starswrapper3">
					<input title="7" type="radio" value="7" name="pulizia" class="starswrapper3">
					<input title="8" type="radio" value="8" name="pulizia" class="starswrapper3">
					<input title="9" type="radio" value="9" name="pulizia" class="starswrapper3">
					<input title="10" type="radio" value="10" name="pulizia" class="starswrapper3">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE4') ?>:
					<input type="hidden" id="hfvalue4" name="hfvalue4" value="6">
					<span id="starscap4">6</span><br />
					<input title="1" type="radio" value="1" name="comfort" class="starswrapper4 required">
					<input title="2" type="radio" value="2" name="comfort" class="starswrapper4">
					<input title="3" type="radio" value="3" name="comfort" class="starswrapper4">
					<input title="4" type="radio" value="4" name="comfort" class="starswrapper4">
					<input title="5" type="radio" value="5" name="comfort" class="starswrapper4">
					<input title="6" type="radio" checked value="6" name="comfort" class="starswrapper4">
					<input title="7" type="radio" value="7" name="comfort" class="starswrapper4">
					<input title="8" type="radio" value="8" name="comfort" class="starswrapper4">
					<input title="9" type="radio" value="9" name="comfort" class="starswrapper4">
					<input title="10" type="radio" value="10" name="comfort" class="starswrapper4">
				<br />
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE5') ?>:
					<input type="hidden" id="hfvalue5" name="hfvalue5" value="6">
					<span id="starscap5">6</span><br />
					<input title="1" type="radio" value="1" name="rapporto" class="starswrapper5 required">
					<input title="2" type="radio" value="2" name="rapporto" class="starswrapper5">
					<input title="3" type="radio" value="3" name="rapporto" class="starswrapper5">
					<input title="4" type="radio" value="4" name="rapporto" class="starswrapper5">
					<input title="5" type="radio" value="5" name="rapporto" class="starswrapper5">
					<input title="6" type="radio" checked value="6" name="rapporto" class="starswrapper5">
					<input title="7" type="radio" value="7" name="rapporto" class="starswrapper5">
					<input title="8" type="radio" value="8" name="rapporto" class="starswrapper5">
					<input title="9" type="radio" value="9" name="rapporto" class="starswrapper5">
					<input title="10" type="radio" value="10" name="rapporto" class="starswrapper5">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 text-center">
				<div class="com_bookingforconnector_rating_valuation">
					<div ><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION') ?> </div>
					<div class="com_bookingforconnector_rating_value" id="totale">6</div>
					<input type="hidden" id="hftotale" name="hftotale" value="6">
				</div>
			</div>
		</div>

		<br />
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_ADVANTAGES') ?></label>
				<textarea name="pregi" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;"></textarea>    
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DEFECTS') ?></label>
				<textarea name="difetti" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;"></textarea>    
			</div>
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
			  <br />
			  <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CONFIRM') ?></label>
			</div>
		</div>
			  
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 form-inline ">
				<input  type="checkbox" value="true" name="confirmprivacy" id="confirmprivacytrue"/>
				<label for="confirmprivacytrue"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CONFIRMPRIVACYTRUE') ?></label>
			</div>
		</div><!--/row-->
		<?php if(false) :?>   -----------------------
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
			  <br />
			  <label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CONFIRM2') ?></label>
			  <textarea name="privacy" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" readonly >{loadposition privacy2}</textarea>    
			</div>
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 form-inline ">
				<input  type="radio" value="true" name="confirmprivacy2" id="confirmprivacy2true" />
				<label for="confirmprivacytrue"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CONFIRMPRIVACYTRUE') ?></label>
			</div>
			 <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 form-inline ">
				 <input  type="radio" value="false" name="confirmprivacy2" id="confirmprivacy2false" />
				<label for="confirmprivacyfalse"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CONFIRMPRIVACYFALSE') ?></label>
		  </div><!--/span-->        
		</div><!--/row-->
		<?php endif ?>   
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 privacyrating form-inline" >
				<input  type="checkbox" value="true" name="privacyrating" id="privacyrating"  />
				<label for="privacyrating"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_PRIVACY'),$sitename); ?></label>    
			</div>
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>
				<button type="submit" class="btn btn-info"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_SEND') ?></button>
			</div>
		</div><!--/row-->

	</div>
</form>
<?php else: ?>
<div class="alert alert-error" id="msgKo">
	<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_ERROR_ORDER') ?>
	<div class="errorRatingReason"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_ERROR_CODE' . $ratingError) ?></div>
</div>

<?php endif // $showform ?>   

</div>
<script type="text/javascript">

function setRating(field){
	jQuery('.starswrapper'+field).rating({
		split: 2,
		focus: function(value, link){
			jQuery('#starscap'+field).html(value || '');
			},
		blur: function(value, link){
			jQuery('#starscap'+field).html(jQuery('#hfvalue'+field).val() || '');
			},
		callback: function(value, link){ 
			jQuery('#starscap'+field).html(value || '');
			jQuery('#hfvalue'+field).val(value)
			sommatoria();
			}
		});
}

jQuery(function() {
	jQuery('.moduletable-insearch').show();
	for (i=1;i<6 ;i++ )
	{
		setRating(i)
	}
// validation *--------/
		    jQuery("#formRating").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        rules:
		        {
		            email:
		            {
		                required: true,
		                email: true
		            },
					email2: {
						  equalTo: "#email"
					},
		        	confirmprivacy : "required",
					privacyrating : "required"
		        },
		        messages:
		        {
		        	confirmprivacy: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CONFIRM_ERROR') ?>",
		        	privacyrating: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_ERROR_REQUIRED') ?>",
		            email: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED') ?>",
		            email2: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED') ?>"
		        },
		        highlight: function(label) {
			    	jQuery(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.closest('.control-group').removeClass('error').addClass('success');
			    		//.text('ok!').addClass('valid')
			    },
			   submitHandler: function(form) {
					if (typeof grecaptcha === 'object') {
						var response = grecaptcha.getResponse();
						//recaptcha failed validation
						if(response.length == 0) {
							$('#recaptcha-error').show();
							return false;
						}
						//recaptcha passed validation
						else {
							$('#recaptcha-error').hide();
						}					 
					}
//
//					jQuery('#formRating').ajaxSubmit({
//						beforeSubmit: function(arr, $form, options) {
//							jQuery('#msgKo').hide()
//							$form.toggle();
//						},
//						success:   processJson
//					}); 
					 jQuery.blockUI();
					 form.submit();

			   }

		    });


//end validation *---------/

});


function processJson(data) {     
	// 'data' is the json object returned from the server     
	if (data!=="")
	{
		jQuery('#msgOk').show()
		jQuery('#msgKo').hide()
	}else{
		jQuery('#msgOk').hide()
		jQuery('#msgKo').show()
		jQuery('#formRating').toggle();
	}
	jQuery('html, body').animate({ scrollTop: 0 }, 0);

}

function sommatoria(){
	var sommatotale = 0;
	for (i=1;i<6 ;i++ )
	{
		sommatotale += parseInt(jQuery('#hfvalue'+i).val());
	}
	jQuery('#totale').html(Math.round( (sommatotale/5) *100 ) / 100);
	jQuery('#hftotale').val(Math.round( (sommatotale/5) *100 ) / 100);
}

</script>
