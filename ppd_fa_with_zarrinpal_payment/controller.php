<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
?>
<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class PayPerDownloadPlusController extends JController
{
	function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'pay');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		if($view != null)
		{
			$model = $this->getModel( $viewName, 'ModelPayPerDownloadPlus' );
			if($model)
				$view->setModel( $model, true );
			$view->setLayout("default");
			$view->display();
		}
	}

	
	function getfree()
	{
		$result = false;
		$model = $this->getModel( "Pay", 'ModelPayPerDownloadPlus' );
		if($model)
		{
			$user = JFactory::getUser();
			if($user->id)
			{
				$license_id = JRequest::getInt('license_id');
				$result = $model->getFree($license_id, $user->id);
			}
		}
		$mainframe = JFactory::getApplication();
		$returnUrl = base64_decode(JRequest::getVar("returnurl"));
		$msg = "";
		if(!$result)
			$msg = JText::_("PAYPERDOWNLOADPLUS_DISCOUNT_GET_FREE_ERROR");
		$mainframe->redirect($returnUrl, $msg);
	}
	
	function joinaffiliate()
	{
		$aff = JRequest::getInt('aff');
		$Itemid = JRequest::getInt('Itemid');
		$model = $this->getModel( "Affiliate", 'ModelPayPerDownloadPlus' );
		$isUpdate = false;
		$result = $model->updateAffiliateData($isUpdate);
		$mainframe = JFactory::getApplication();
		if($result)
		{
			if($isUpdate)
				$msg = JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_UPDATE_SUCCESSFULL");
			else
				$msg = JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_JOIN_SUCCESSFULL");
			$mainframe->redirect("index.php?option=com_payperdownloadplus&view=affiliate&aff=$aff&Itemid=$Itemid",
				$msg);
		}
		else
		{
			if($isUpdate)
				$msg = JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_UPDATE_FAILED");
			else
				$msg = JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_JOIN_FAILED");
			$mainframe->redirect("index.php?option=com_payperdownloadplus&view=affiliate&aff=$aff&Itemid=$Itemid",
				$msg, "error");
		}
	}
	
	function confirmaffiliatepayment()
	{
		$model = $this->getModel( "PayAffiliate", 'ModelPayPerDownloadPlus' );
		if($model)
		{
			$model->handleResponse();
		}
	}
		
	function confirm()
	{
		$model = $this->getModel( "Pay", 'ModelPayPerDownloadPlus' );
		if($model)
		{
			$model->handleResponse();
		}
	}
	
	function confirmres()
	{
		$model = $this->getModel( "PayResource", 'ModelPayPerDownloadPlus' );
		if($model)
		{
			$model->handleResponse();
		}
	}	
	
	function paypalsim()
	{	
		$level = JRequest::getVar('level');
		$lid = JRequest::getVar('item_number');
		$type = JRequest::getVar('type');
		$user = jfactory::getuser();
		
		if( $type == 'res' ){
			$db = jfactory::getDBO();
			$db->setQuery("select *from #__payperdownloadplus_resource_licenses where resource_license_id=".$lid);
			$l = $db->loadObject();				
		}else{
			$db = jfactory::getDBO();
			$db->setQuery("select *from #__payperdownloadplus_licenses where license_id=".$lid);
			$l = $db->loadObject();			
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__payperdownloadplus_config", 0, 1);
		$config = $db->loadObject();
		
		if( $level == 'payment' ){
			$req = "receiver_email=" . urlencode($user->email);
			$req .= "&business=" . urlencode(JRequest::getVar('business'));
			$req .= "&payer_email=" . urlencode("paypalsimulator@paypal.com");
			$req .= "&txn_id=" . urlencode(rand(111111111,999999999));
			$req .= "&test_ipn=1";
			$req .= "&custom=" . urlencode(JRequest::getVar('custom'));
			$req .= "&item_number=" . urlencode(JRequest::getVar('item_number'));
			$req .= "&mc_gross=" . urlencode(JRequest::getVar('amount'));
			$req .= "&mc_currency=" . urlencode(JRequest::getVar('currency_code'));
			$req .= "&payment_status=COMPLETED";
			$req .= "&mc_fee=0.01";
			$req .= "&tax=0.00";	
			jfactory::getsession()->set('PPD_ZAR_RETURN',JRequest::getVar('return'));
			jfactory::getsession()->set('PPD_ZAR_notify_url',JRequest::getVar('notify_url'));
			jfactory::getsession()->set('PPD_ZAR_req',$req);
			$MerchantID = $config->paypalaccount;  //Required
			if( $type == 'res' ){
				$Amount = (int)$l->resource_price/10;	
				$Description = $l->resource_name;
			}else{
				$Amount = (int)$l->price/10;
				$Description = $l->license_name;	
			}

			$Email = 'UserEmail@Mail.Com';
			$Mobile = '09123456789';
			$CallbackURL = JURI::BASE().'index.php?option=com_payperdownloadplus&task=paypalsim&level=verify&item_number='.$lid.'&type='.$type;
			$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8')); 
			$result = $client->PaymentRequest(
								array(
										'MerchantID' 	=> $MerchantID,
										'Amount' 	=> $Amount,
										'Description' 	=> $Description,
										'Email' 	=> $Email,
										'Mobile' 	=> $Mobile,
										'CallbackURL' 	=> $CallbackURL
									)
			);
			
			if($result->Status == 100)
			{
				$url = "https://www.zarinpal.com/pg/StartPay/".$result->Authority;
				header("location: ".$url);
				echo "<a href='$url' style='color: red'> برای انتقال به دروازه پرداخت کلیک نمایید... </a>";
			} else {
				echo'ERR: '.$result->Status;
			}			
		}else{
			$MerchantID = $config->paypalaccount;
			if( $type == 'res' ){
				$Amount = (int)$l->resource_price/10;	
			}else{
				$Amount = (int)$l->price/10;	
			}
			$Authority = $_GET['Authority'];
			if($_GET['Status'] == 'OK'){
				$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8')); 
				$result = $client->PaymentVerification(
									array(
											'MerchantID'	 => $MerchantID,
											'Authority' 	 => $Authority,
											'Amount'	 => $Amount
										)
				);
				if( $result->RefID == 0 ){
					echo 'کاربر از انجام تراکنش منصرف شده است';
					return;
				}
				$return = jfactory::getsession()->get('PPD_ZAR_RETURN');
				$notify_url = jfactory::getsession()->get('PPD_ZAR_notify_url');
				$req = jfactory::getsession()->get('PPD_ZAR_req');
				
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch,CURLOPT_URL, $notify_url);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$req);
				curl_exec($ch);
				curl_close($ch);
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_payperdownloadplus&view=paypalsim&return=" . 
					urlencode($return));					
			} else {
				echo 'کاربر از انجام تراکنش منصرف شده است';
			}					
			
			return;

	
		}
		
		
		

	}
	
	function quickRegister()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_users', JPATH_SITE . DS . 'administrator');
	
		$return = JRequest::getVar('return');
		if($return)
			$return = base64_decode($return);
	
		$mainframe = JFactory::getApplication();
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$newUsertype = $usersConfig->get( 'new_usertype' );
		
		jimport('joomla.user.user');
		$userName = JRequest::getVar('regusername');
		$userFullName = JRequest::getVar('name');
		$userPassword = JRequest::getString('regpassword', '', 'post', JREQUEST_ALLOWRAW);
		$userPassword2 = JRequest::getString('regpassword2', '', 'post', JREQUEST_ALLOWRAW);
		
		$email = JRequest::getVar('email');
		$email2 = JRequest::getVar('email2');
		$params = array("name" => $userFullName, "username" => $userName, 
			"password" => $userPassword, "password2" => $userPassword2, 
			"email" => $email);
		$user = new JUser();
		$version = new JVersion();
		if($version->RELEASE == "1.5")
		{
			if (!$newUsertype) {
				$newUsertype = 'Registered';
			}
			$acl = JFactory::getACL();
			$user->gid = $acl->get_group_id($newUsertype);
			$user->usertype = $newUsertype;
		}
		else
		{
			$user->groups = array();
			$user->groups []= $newUsertype;
		}
		if(!$user->bind($params))
		{
			$mainframe->redirect($return, JText::_( $user->getError()));
			exit;
		}
		if($email != $email2)
		{
			$mainframe->redirect($return, JText::_('PAYPERDOWNLOADPLUS_REGISTER_EMAILS_DO_NOT_MATCH'));
			exit;
		}
		$date = JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());
		if ( !$user->save() )
		{
			$mainframe->redirect($return, JText::_( $user->getError()));
			exit;
		}
		$options = array();
		$options['return'] = $return;
		$options['remember'] = JRequest::getBool('remember', false);
		$credentials = array();
		$credentials['username'] = $userName;
		$credentials['password'] = $userPassword;
		$mainframe->login($credentials, $options);
		$user = JFactory::getUser();
		if(!$user->id)
		{
			$mainframe->redirect($return, JText::_('PAYPERDOWNLOADPLUS_REGISTER_LOGIN_ERROR'));
		}
		else
		{
			if($return)
			{
				$this->_sendmail($email, $userName);
				$mainframe->redirect($return, JText::_( 'PAYPERDOWNLOADPLUS_REGISTER_YOU_HAVE_BEEN_SUCCESSFULLY_REGISTERED' ));
			}
			else
			{
				$mainframe->redirect('index.php', JText::_( 'PAYPERDOWNLOADPLUS_REGISTER_YOU_HAVE_BEEN_SUCCESSFULLY_REGISTERED' ));
			}
		}
	}
	
	function buyWithAup()
	{
		require_once(JPATH_COMPONENT . DS . 'models' . DS . 'pay.php');
		$model = new ModelPayPerDownloadPlusPay();
		$return = JRequest::getVar("return");
		if($return)
			$return = base64_decode($return);
		$mainframe = JFactory::getApplication();
		$alpha_integration = $model->getAlphaIntegration();
		if($alpha_integration == 2)
		{
			$user = JFactory::getUser();
			if($user->id)
			{
				$user_points = $model->getAUP();
				$license_id = JRequest::getInt("lid", 0);
				$license = $model->getLicense($license_id);
				if($license && $license->aup > 0 && $license->aup <= $user_points)
				{
					if($model->removeAUPFromUser($user->id, $license))
					{
						$model->assignLicense($user->id, $license_id, 0, true);
						$mainframe->redirect($return, JText::_("PAYPERDOWNLOADPLUS_LICENSE_BOUGHT_WITH_AUP"));
					}
					else
						$mainframe->redirect($return, JText::_("PAYPERDOWNLOADPLUS_AUP_NOT_ENABLED"), "error");
				}
				else
					$mainframe->redirect($return, JText::_("PAYPERDOWNLOADPLUS_NOT_ENOUGH_AUP"), "error");
			}
			else
				$mainframe->redirect($return, JText::_("PAYPERDOWNLOADPLUS_NOT_LOGGEDIN"), "error");
		}
		else
			$mainframe->redirect($return, JText::_("PAYPERDOWNLOADPLUS_AUP_NOT_ENABLED"), "error");
	}
	
	function sendLink()
	{
		$access = JRequest::getVar('access');
		$m = JRequest::getVar('m', '');
		$regexp = "/^\s*\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*\s*$/";
		if(preg_match($regexp, $m))
		{
			list($downloadId, $hash, $rand) = explode("-", $access);
			$db = JFactory::getDBO();
			$downloadId = (int)$downloadId;
			$rand = $db->getEscaped($rand);
			$query = "SELECT * FROM #__payperdownloadplus_download_links 
				WHERE download_id = $downloadId AND random_value = '$rand' AND payed <> 0 AND
				(expiration_date > NOW() OR expiration_date IS NULL) AND 
				(link_max_downloads = 0 OR download_hits < link_max_downloads)";
			$db->setQuery( $query );
			$downloadLink = $db->loadObject();
			if($downloadLink)
			{
				if($hash == sha1($downloadLink->secret_word . $downloadLink->random_value))
				{
					$mail =& JFactory::getMailer();
					$mail->setSubject($downloadLink->email_subject);
					$mail->setBody($downloadLink->email_text);
					$mail->ClearAddresses();
					$mail->addRecipient($m);
					$mail->IsHTML(true);
					$joomla_config = new JConfig();
					$mail->setSender(array($joomla_config->mailfrom, $joomla_config->fromname));
					$mail->Send();
					echo "<<1>>";
					exit();
				}
			}
		}
		echo "<<0>>";
		exit();
	}
	
	function _sendmail($useremail, $username)
	{
		$mainframe = JFactory::getApplication();
		$mailfrom = $mainframe->getCfg( 'mailfrom' );
		$fromname = $mainframe->getCfg( 'fromname' );
		$siteURL  = JURI::base();
		$subject = JText::_("PAYPERDOWNLOADPLUS_USER_REGISTER_SUBJECT");
		$text = JText::sprintf("PAYPERDOWNLOADPLUS_USER_REGISTER_MAIL", $siteURL, $username);
		JUtility::sendMail($mailfrom, $fromname, $useremail, $subject, $text);
	}
	
	function _getGroupId($groupName)
	{
		$db = JFactory::getDBO();
		$groupName = $db->getEscaped($groupName);
		$query = "SELECT id FROM #__usergroups WHERE title = '$groupName'";
		$db->setQuery( $query );
		return $db->loadResult();
	}
}

?>
