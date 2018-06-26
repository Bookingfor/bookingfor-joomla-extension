<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
require_once $pathbase . 'helpers/wsQueryHelper.php';

$wsHelper = new wsQueryHelper(null, null);

$checkUrl = $wsHelper->url_exists();
$result = null;
$resultOk = false;
$msg="";
$monomerchant = false;

if($checkUrl){
	$options = array(
			'path' => '/Checkstatus',
			'data' => array(
					'$format' => 'json'
			)
	);
	$url = $wsHelper->getQuery($options);

	//$r = $this->helper->executeQuery($url);
	$r = $wsHelper->executeQuery($url);
	
	if (isset($r)) {
		$res = json_decode($r);

//echo "<pre>";
//echo print_r($res);
//echo "</pre>";
//
		if (!empty($res->d->results)){
			$result = $res->d->results;
		}elseif(!empty($res->d)){
			$result = $res->d;
		}elseif(!empty($res)){
			$result = $res;
		}
	}
	if(!empty($result)){
		if (!empty($result->error) ){
			if (!empty($result->error->message) ){
				if (!empty($result->error->message->value) ){
					$msg=$result->error->message->value;
				}
			}
		}else{
			if (!empty($result->IsActive) ){
				$resultOk = true;
			}else{
				$msg=" Utente non attivo";
			}
			
			
			if (!empty($result->CurrentManagingMerchantId) ){
				$monomerchant = true;
			}

			if(!empty($result->ValidationStart)){

				$validationStart = BFCHelper::parseJsonDate($result->ValidationStart);
				if($validationStart> new JDate('now ')){
					$resultOk = false;
					$msg = $msg . " - data inizio validità: " . $validationStart;
				}
			}
			if(!empty($result->ValidationEnd)){

				$validationEnd = BFCHelper::parseJsonDate($result->ValidationEnd);
				if($validationEnd< new JDate('now ')){
					$resultOk = false;
					$msg = $msg . " - data fine validità: " . $validationEnd;
				}
			}
		}
		// check date validità
		/*
		if ($result->ValidationStart=(null) ){
			$resultOk = true;
		}
		*/

	}
}
	

// load tooltip behavior
JHtml::_('behavior.tooltip');
$version=2;
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$version=3;

		}
		else
		{
			$version=2;
		}

?>
<h1><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_CONFIGURATION')?></h1>
<h2><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_PREFERENCE')?></h2>
<table class="table table-striped table-bordered table-condensed">
	<tbody>
	<tr>
		<td><?php echo JTEXT::_('CONFIG_VERSION_LABEL')  ?></td>
		<td>
			<?php echo BFI_VERSION ?>
		</td>
	</tr>
	<tr>
		<td><?php echo JTEXT::_('CONFIG_WSURL_LABEL')  ?></td>
		<td>
			<?php echo COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY ?>
			<?php
				if(empty(COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY)){
				echo '<div class="error" style="margin:10px 0 0">' . JText::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_WSURL_MESSAGE') . '</div>';
				}
			?>
		</td>
	</tr>
	<tr>
		<td><?php echo JTEXT::_('CONFIG_FORMLABEL_LABEL')  ?></td>
		<td>
			<?php echo COM_BOOKINGFORCONNECTOR_FORM_KEY?>
		</td>
	</tr>
	<tr>
		<td>WS online</td>
		<td><span class="badge" style="<?php echo ($checkUrl)? "background-color: #398439;": " background-color: #d43f3a; " ?>">&nbsp;</span></td>
	</tr>
	<tr>
		<td>WS Connettivity </td>
		<td> 
	<?php
		if(empty($wsHelper->errmsg)){
	?>
			<span class="badge" style="background-color: #398439;"><?php echo $wsHelper->infomsg ?></span>
	<?php
	}else{
	?>
			<span class="badge" style="background-color: #d43f3a;"><?php echo $wsHelper->errmsg ?></span>
	<?php

		$curlversion=curl_version();
		if (version_compare($curlversion["version"], '7.29', 'le'))
		{
			echo "<br />Curl Version Out-of-Date (min request: 7.30 attual version " . $curlversion["version"] . ") ";
		} else {
			echo "<br />Curl Version OK ";
		}

		if(OPENSSL_VERSION_NUMBER < 0x10001000) {
			echo "<br />OpenSSL Version Out-of-Date";
		} else {
			echo "<br />OpenSSL Version OK ";
		}
	}
	?>
		</td>
	</tr>
	<tr>
		<td><?php echo JTEXT::_('CONFIG_APIKEY_LABEL')  ?> (<?php	echo (!empty( $resultOk ) && $monomerchant)? "API merchant" :"API sotttoscrizione";?>)</td>
		<td>
			<?php
				if(empty(COM_BOOKINGFORCONNECTOR_API_KEY)){
				echo '<div class="error" style="margin:10px 0 0">' . JText::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_APIKEY_MESSAGE') . '</div>';
				}
			?>
			<?php echo COM_BOOKINGFORCONNECTOR_API_KEY?>
			
		</td>
	</tr>
	<tr>
		<td>Account</td>
		<td>
			<span class="badge" style="<?php echo (!empty($resultOk)? "background-color: #398439;" : "background-color: #d43f3a;") ?>">&nbsp;</span> <?php echo $msg ?>
		</td>
	</tr>
	<tr>
		<td>PHP version</td>
		<td><?php echo PHP_VERSION ?>
			<?php
				if (version_compare(PHP_VERSION, '5.6.0', '<')) {

					echo '<span class="badge" style="background-color: #d43f3a;">Min Version 5.6 </span>';
				}
			?>
		
		</td>
	</tr>
	<tr>
		<td>Joomla version</td>
		<td><?php echo $version ?></td>
	</tr>
	<tr>
		<td>Encrypts data</td>
		<td><?php 
				if (version_compare(COM_BOOKINGFORCONNECTOR_CRYPTOVERSION, '1', '<')) {

					echo '<span class="badge" style="background-color: #d43f3a;">No Cryptography Extensions find </span>';
				}
				if (version_compare(PHP_VERSION, '7.0.0', '>') && version_compare(COM_BOOKINGFORCONNECTOR_CRYPTOVERSION, '2', '<')) {

					echo '<span class="badge" style="background-color: #d43f3a;">No Cryptography Extensions find for this PHP version '.PHP_VERSION.'  </span>';
				}
				if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION == 1) {

					echo 'Cryptography Extensions find for this PHP version '.PHP_VERSION.': Mcrypt ';
				}
				if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION == 2) {

					echo 'Cryptography Extensions find for this PHP version '.PHP_VERSION.': OpenSSL ';
				}
		?>
		</td>
	</tr>

	<tr>
		<td><?php echo JTEXT::_('CONFIG_SHOWDATA_LABEL')  ?></td>
		<td><?php echo JTEXT::_(COM_BOOKINGFORCONNECTOR_SHOWDATA ? 'JYES' : 'JNO')  ?></td>
	</tr>
	<tr>
		<td>SEF</td>
		<td>
			<?php
			if (JFactory::getConfig()->get('sef') || JFactory::getConfig()->get('sef_rewrite'))
			{
				echo JText::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_SEF_MESSAGE');
			?>
		</td>
	</tr>
	</tbody>
</table>

		

<h2><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_ADMINISTRATION_LANGUAGES')?></h2>
<div>
	<?php
		$languages = JLanguageHelper::getLanguages('lang_code');
		$db   = JFactory::getDBO();
		$uriResource  = 'index.php?option=com_bookingforconnector&view=resource';
		$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
		$app    = JApplication::getInstance('site');
		$router = $app->getRouter();
		$tmpMerchantId = 123;
		$tmpMerchantName = "MerchantName";
		$titleUrlMerchant = "Merchant";
		$layoutUrlMerchant = "";
		
		if(!empty( $resultOk ) && $monomerchant){
			$tmpMerchantId = $result->CurrentManagingMerchantId;
			$currMerchant = BFCHelper::getMerchantFromServicebyId($tmpMerchantId);
			$tmpMerchantName =  BFCHelper::getSlug($currMerchant->Name); 
			$titleUrlMerchant = "Merchant contacts";
			$layoutUrlMerchant = "&layout=contactspopup";
		}

//$uri = $router->build($myURL);
//$parsed_url = $uri->toString();		
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriResource ) .' AND language='.$db->Quote('*').'  AND published = 1 LIMIT 1' );
		$itemId = intval($db->loadResult());

		if ($itemId<>0)
			$resourceRoute =  $router->build($uriResource.'&resourceId=' . '123' . ':' . 'ResourceName' . '&Itemid='.$itemId );
		else
			$resourceRoute =  $router->build($uriResource.'&resourceId=' . '123' . ':' . 'ResourceName');
		
		$resourceRoute = str_replace('/administrator/','/',$resourceRoute->toString());
		
		
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND language='.$db->Quote('*').'  AND published = 1 LIMIT 1' );
		$itemIdMerchant = intval($db->loadResult());
		if ($itemIdMerchant<>0)
			$merchantRoute = $router->build($uriMerchant.'&merchantId=' . $tmpMerchantId . ':' . $tmpMerchantName . '&Itemid='.$itemIdMerchant . $layoutUrlMerchant) ;
		else
			$merchantRoute = $router->build($uriMerchant.'&merchantId=' . $tmpMerchantId . ':' . $tmpMerchantName . $layoutUrlMerchant);
		
		$merchantRoute = str_replace('/administrator/','/',$merchantRoute->toString());
		
		
?>

<table class="table table-striped table-bordered table-condensed">
	<thead>
	<tr>
		<th>Language</th>
		<th>Resource</th>
		<th><?php echo $titleUrlMerchant ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>no language</td>
		<td><?php echo $resourceRoute ?></td>
		<td><?php echo $merchantRoute ?></td>
	</tr>
<?php
		if (!empty($languages))
		{
			foreach ($languages as $key => $lang)
			{
				//echo $key . ":" . $lang->title;
				
				
				$language = $lang->lang_code;
				$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriResource ) .' AND language='. $db->Quote($key) .'  AND published = 1 LIMIT 1' );
				$itemId = intval($db->loadResult());
				if ($itemId<>0)
					$resourceRoute =  $router->build($uriResource.'&resourceId=' . '123' . ':' . 'ResourceName' . '&Itemid='.$itemId );
				else
					$resourceRoute =  $router->build($uriResource.'&resourceId=' . '123' . ':' . 'ResourceName');
				$resourceRoute = str_replace('/administrator/','/'.$lang->sef .'/',$resourceRoute->toString());

				$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND language='. $db->Quote($language) .' AND published = 1 LIMIT 1' );
				$itemIdMerchant = intval($db->loadResult());
				if ($itemIdMerchant<>0)
					$merchantRoute = $router->build($uriMerchant.'&merchantId=' . $tmpMerchantId . ':' . $tmpMerchantName . '&Itemid='.$itemIdMerchant . $layoutUrlMerchant) ;
				else
					$merchantRoute = $router->build($uriMerchant.'&merchantId=' . $tmpMerchantId . ':' . $tmpMerchantName . $layoutUrlMerchant);

				$merchantRoute = str_replace('/administrator/','/'.$lang->sef .'/',$merchantRoute->toString());
?>
	<tr>
		<td><?php echo $lang->title  ?></td>
		<td><?php echo $resourceRoute ?></td>
		<td><?php echo $merchantRoute ?></td>
	</tr>
<?php
			}
		}
		
//		//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//		$itemId = intval($db->loadResult());
//		$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//		//-------------------pagina per il redirect di tutti i merchant
//
//		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//		//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//		$itemIdMerchant = intval($db->loadResult());
//		//-------------------pagina per il redirect di tutti i merchant
//
//		//-------------------pagina per il redirect di tutte le risorse in vendita favorite
//
	}
	?>
	</tbody>
</table>
<div>
