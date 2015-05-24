<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/Mobilpay/Payment/Request/Abstract.php');
include (dirname(__FILE__) . '/Mobilpay/Payment/Request/Card.php');
include (dirname(__FILE__) . '/Mobilpay/Payment/Request/Notify.php');
include (dirname(__FILE__) . '/Mobilpay/Payment/Invoice.php');
include (dirname(__FILE__) . '/Mobilpay/Payment/Address.php');

function mobilpay_syslog ($data) {
	@file_put_contents (dirname (__FILE__) . '/.mobilpay.log',
		sprintf ("%s\t%s\n", date ('d-m-Y H:i:s'), $data),
		FILE_APPEND);
	}

mobilpay_syslog (sprintf ("GET: %s\nPOST: %s\n", print_r ($_GET, TRUE), print_r ($_POST, TRUE)));

if ($_GET['orderId']) {
	$order = rca_getorder ($_GET['orderId']);
	$details = $order['ord_details'];

	if (isset($_POST['env_key']) && isset($_POST['data'])) { # mobilpay
		$errorMessage = '';

		$privateKeyFilePath = dirname(__FILE__) . '/security/postasig.key';
		try {
			$objPmReq = Mobilpay_Payment_Request_Abstract::factoryFromEncrypted($_POST['env_key'], $_POST['data'], $privateKeyFilePath);

			mobilpay_syslog (sprintf ("action: %s\nerrorcode: %s\n", $objPmReq->objPmNotify->action, $objPmReq->objPmNotify->errorCode));

			if ($objPmReq->objPmNotify->errorCode > 0) {
				$errorMessage = $objPmReq->objPmNotify->getCrc();
				$data = array (
					'status' => $objPmReq->objPmNotify->errorCode,
					'details' => array (
						'stamp' => time(),
						'mentions' => date('d-m-Y H:i:s' . "\n" . $errorMessage),
						'mobilpay' => 'rejected',
						'errcode' => $objPmReq->objPmNotify->errorCode,
						'purchase' => $objPmReq->purchaseId
						),
					'record' => $order['id']
					);
				
				rca_updateorder ($data);
				mobilpay_syslog (sprintf ("ERROR\nREMOTE_ADDR: %s\nHTTP_REFERER: %s\nACTION: %s\nERROR_CODE: %s\n", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'], $objPmReq->objPmNotify->action, $objPmReq->objPmNotify->errorCode));
				}
			else {
				$data = array (
					'status' => 2,
					'details' => array (
						'stamp' => time(),
						'mentions' => date('d-m-Y H:i:s' . "\n" . 'Confirmed!'),
						'mobilpay' => 'confirmed',
						'errcode' => 0,
						'purchase' => $objPmReq->purchaseId
						),
					'record' => $order['id']
					);
				
				rca_updateorder ($data);
				mobilpay_syslog (sprintf ("SUCCESS\nREMOTE_ADDR: %s\nHTTP_REFERER: %s\n", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER']));
				}
	
			}
		catch (Exception $e) {
			$errorType = Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_TEMPORARY;
			$errorCode = $e->getCode();
			$errorMessage = $e->getMessage();
			$data = array (
				'status' => 3,
				'details' => array (
					'stamp' => time(),
					'mobilpay' => $objPmReq->objPmNotify->errorCode ? 'exception' : 'invalid',
					'errcode' => $objPmReq->objPmNotify->errorCode,
					),
				'record' => $order['id']
				);
			rca_updateorder ($data);
			mobilpay_syslog (sprintf ("EXCEPTION\nREMOTE_ADDR: %s\nHTTP_REFERER: %s\nACTION: %s\nERROR_CODE: %s\n", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'], $objPmReq->objPmNotify->action, $objPmReq->objPmNotify->errorCode));
			}

		header('Content-type: application/xml');
		echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
		echo $errorCode == 0 ?
				('<crc>'.$errorMessage.'</crc>') :
				('<crc error_type="'.$errorType.'" error_code="'.$objPmReq->objPmNotify->errorcode.'">'.$errorMessage.'</crc>');
		exit (0);
		}
	elseif ($_POST['TransId']) {
		$mentions = '';
		foreach ($_POST as $key => $val)
			$mentions .= $key.' = '.$val."\n";

		$data = array (
			'status' => 2,
			'details' => array (
				'stamp' => time(),
				'transaction' => $_POST['TransId'],
				'mentions' => $mentions
				),
			'record' => $order['id']
			);
		rca_updateorder ($data);
		mobilpay_syslog (sprintf ("SUCCESS\nREMOTE_ADDR: %s\nHTTP_REFERER: %s\nTRANS_ID: %s\n", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'], $_POST['TransId']));

		echo 'Plata dumneavoastra a fost inregistrata cu succes. Apasati <a href="' . site_url() . '">aici</a> pentru a reveni pe site-ul PostAsig.';
		exit (0);
		}
	else {
		$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
		$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
		$errorMessage 	= 'mobilpay.ro posted invalid parameters';
		header('Content-type: application/xml');
		echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
		echo $errorCode == 0 ?
				('<crc>'.$errorMessage.'</crc>') :
				('<crc error_type="'.$errorType.'" error_code="'.$errorCode.'">'.$errorMessage.'</crc>');
		
		$data = array (
			'status' => 4,
			'details' => array (
				'stamp' => time(),
				'mobilpay' => $errorCode ? 'missing' : 'invalid',
				'errcode' => $errorCode
				),
			'record' => $order['id']
			);
		rca_updateorder ($data);
		mobilpay_syslog (sprintf ("REMOTE_ADDR: %s\nHTTP_REFERER: %s\nERROR_CODE: %s\n", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'], $errorCode));

		exit (0);
		}
	}
else {
	$errorCode 		= 0;
	$errorType		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_NONE;
	$errorMessage	= '';
	header('Content-type: application/xml');
	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo $errorCode == 0 ?
			('<crc>'.$errorMessage.'</crc>') :
			('<crc error_type="'.$errorType.'" error_code="'.$errorCode.'">'.$errorMessage.'</crc>');
	exit (0);
	}
?>
