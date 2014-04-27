<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR . DS . "components" . DS . "com_payperdownloadplus" . DS . "import.php");
require_once(JPATH_ADMINISTRATOR . DS . "components" . DS . "com_payperdownloadplus" . DS . "export.php");

function uninstall_plugin($element, $folder = 'system', $extra_folders = null, $extra_files = null, $language_files = null)
{
	$db = JFactory::getDBO();
	$e_element = $db->getEscaped($element);
	$e_folder = $db->getEscaped($folder);
	$version = new JVersion;
	if($version->RELEASE == "1.5")
	{
		$db->setQuery("DELETE FROM #__plugins WHERE element='$e_element' AND folder='$e_folder'");
		$db->query();
		if(is_array($extra_folders))
		{
			foreach($extra_folders as $extra_folder)
			{
				$dest_folder = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$extra_folder;
				if(JFolder::exists($dest_folder))
				{
					JFolder::delete($dest_folder);
				}
			}
		}
		$dest_file_php = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.'.php';
		if(JFile::exists($dest_file_php))
		{
			JFile::delete($dest_file_php);
		}
		$dest_file_xml = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.'.xml';
		if(JFile::exists($dest_file_xml))
		{
			JFile::delete($dest_file_xml);
		}
		if($extra_files)
		{
			foreach($extra_files as $extra_file)
			{
				$dest_file = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$extra_file;
				if(JFile::exists($dest_file))
				{
					JFile::delete($dest_file);
				}
			}
		}
		if($language_files)
		{
			foreach($language_files as $language_file)
			{
				$dot_pos = strpos($language_file, ".");
				if($dot_pos !== false)
				{
					$language = substr($language_file, 0, $dot_pos);
					$dest_file = JPATH_ADMINISTRATOR.DS.'language'.DS.$language.DS.$language_file;
					if(JFile::exists($dest_file))
					{
						JFile::delete($dest_file);
					}
				}
			}
		}
	}
	else if($version->RELEASE >= "1.6")
	{
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__extensions WHERE element='$e_element' AND folder='$e_folder' AND type='plugin'");
		$db->query();
		$dest_folder = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element;
		if(JFolder::exists($dest_folder))
		{
			JFolder::delete($dest_folder);
		}
		if($language_files)
		{
			foreach($language_files as $language_file)
			{
				$dot_pos = strpos($language_file, ".");
				if($dot_pos !== false)
				{
					$language = substr($language_file, 0, $dot_pos);
					$dest_file = JPATH_ADMINISTRATOR.DS.'language'.DS.$language.DS.$language_file;
					if(JFile::exists($dest_file))
					{
						JFile::delete($dest_file);
					}
				}
			}
		}
	}
}

function install_plugin($component, $element, $folder = 'system', $extra_folders = null, $extra_files = null, $language_files = null)
{
	uninstall_plugin($element, $folder, $extra_folders);
	$db = JFactory::getDBO();
	$name = $folder . ' - ' . $element;
	$e_name = $db->getEscaped($name);
	$e_element = $db->getEscaped($element);
	$e_folder = $db->getEscaped($folder);
	$version = new JVersion;
	if($version->RELEASE == "1.5")
	{
		$dest_folder = JPATH_SITE.DS.'plugins'.DS.$folder;
		$dest_file_php = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.'.php';
		$dest_file_xml = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.'.xml';
		$result = true;
		
		if(!JFolder::exists($dest_folder))
			JFolder::create($dest_folder);
		
		if(is_array($extra_folders))
		{
			foreach($extra_folders as $extra_folder)
			{
				$new_folder = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$extra_folder;
				if(!JFolder::move($new_folder, $dest_folder))
				{
					echo "Error copying folder ($new_folder) to ($dest_folder) folder<br/>";
					$result = false;
				}
			}
		}
		
		$file_php = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.php';
		if(!JFile::exists($file_php) || !JFile::move($file_php, $dest_file_php))
		{
			echo "Error copying file ($file_php) to ($dest_file_php)<br/>";
			$result = false;
		}
		$file_xml = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.xml';
		if(!JFile::exists($file_xml) || !JFile::move($file_xml, $dest_file_xml))
		{
			echo "Error copying file ($file_xml) to ($dest_file_xml)<br/>";
			$result = false;
		}
		
		if($extra_files)
		{
			foreach($extra_files as $extra_file)
			{
				$source_file = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$extra_file;
				$dest_file = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$extra_file;
				if(!JFile::exists($source_file) || !JFile::copy($source_file, $dest_file))
				{
					echo "Error copying file ($source_file) to ($dest_file)<br/>";
					$result = false;
				}
			}
		}
		
		if($language_files)
		{
			foreach($language_files as $language_file)
			{
				$dot_pos = strpos($language_file, ".");
				if($dot_pos !== false)
				{
					$language = substr($language_file, 0, $dot_pos);
					$source_file = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$language_file;
					$dest_file = JPATH_ADMINISTRATOR.DS.'language'.DS.$language.DS.$language_file;
					if(JFolder::exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$language) && JFile::exists($source_file))
					{
						JFile::copy($source_file, $dest_file);
					}
				}
			}
		}
		
		$query = "INSERT INTO #__plugins(name, element, folder, published) VALUES('$e_name', '$e_element', '$e_folder', 1)";
		$db->setQuery($query);
		if(!$db->query())
		{
			echo "Error inserting plugin record<br/>";
			$result = false;
		}
		
		if(!$result)
			uninstall_plugin($component, $element, $folder, $extra_folders);
		return $result;
	}
	else if($version->RELEASE >= "1.6")
	{
		$result = true;
	
		$dest_folder = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element;
		$dest_file_php = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.php';
		$dest_file_xml = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.xml';

		if(!JFolder::exists($dest_folder))
		{
			JFolder::create($dest_folder);
		}
		
		if(is_array($extra_folders))
		{
			foreach($extra_folders as $extra_folder)
			{
				$new_folder = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$extra_folder;
				if(!JFolder::move($new_folder, $dest_folder))
				{
					echo "Error copying folder ($new_folder) to ($dest_folder) folder<br/>";
					$result = false;
				}
			}
		}
		
		$file_php = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.php';
		if(!JFile::exists($file_php) || !JFile::copy($file_php, $dest_file_php))
		{
			echo "Error copying file ($file_php) to ($dest_file_php)<br/>";
			$result = false;
		}
		$file_xml = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.xml';
		if(!JFile::exists($file_xml) || !JFile::copy($file_xml, $dest_file_xml))
		{
			echo "Error copying file ($file_xml) to ($dest_file_xml)<br/>";
			$result = false;
		}
		
		if($extra_files)
		{
			foreach($extra_files as $extra_file)
			{
				$source_file = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$extra_file;
				$dest_file = JPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.DS.$extra_file;
				if(!JFile::exists($source_file) || !JFile::copy($source_file, $dest_file))
				{
					echo "Error copying file ($source_file) to ($dest_file)<br/>";
					$result = false;
				}
			}
		}
		
		if($language_files)
		{
			foreach($language_files as $language_file)
			{
				$dot_pos = strpos($language_file, ".");
				if($dot_pos !== false)
				{
					$language = substr($language_file, 0, $dot_pos);
					$source_file = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'extensions'.DS.'plugins'.DS.$folder.DS.$element.DS.$language_file;
					$dest_file = JPATH_ADMINISTRATOR.DS.'language'.DS.$language.DS.$language_file;
					if(JFile::exists($source_file) && JFolder::exists(JPATH_ADMINISTRATOR.DS.'language'.DS.$language))
					{
						JFile::copy($source_file, $dest_file);
					}
				}
			}
		}
		
		$query = "INSERT INTO #__extensions(name, type, element, folder, enabled, access) 
			VALUES('$e_name', 'plugin', '$e_element', '$e_folder', 1, 1)";
		$db->setQuery($query);
		if(!$db->query())
		{
			echo "Error inserting plugin record<br/>";
			$result = false;
		}
		if(!$result)
			uninstall_plugin($element, $folder );
		return false;
	}
}

function checkPreviousVersion()
{
	$config = new JConfig();
	$db = JFactory::getDBO();
	$dbname = $db->getEscaped($config->db);
	$table = $db->getEscaped($config->dbprefix . 'payperdownloadplus_licenses');
	$query = "SELECT COUNT(*)
				FROM information_schema.tables 
				WHERE table_schema = '$dbname' 
				AND table_name = '$table'";
	$db->setQuery($query);
	return $db->loadResult() > 0;
}

function backupCurrentData()
{
	if(!checkPreviousVersion())
	{
		return true;
	}
	$config = JFactory::getConfig();
	$filePath = $config->getValue('config.tmp_path').DS.'payperdownloadplus.bk.xml';
	jimport('joomla.filesystem.file');
	if(JFile::exists($filePath))
		JFile::delete($filePath);
	$exporter = new XML_Exporter();
	if($exporter->open($filePath))
	{
		$exporter->write_root_open("payperdownloadplus");
		$exporter->export_table("payperdownloadplus_config", "config_id");
		$exporter->write_root_close();
		return $exporter->close();
	}
	else
		return false;
}

function restoreData()
{
	$config = JFactory::getConfig();
	$filePath = $config->getValue('config.tmp_path').DS.'payperdownloadplus.bk.xml';
	$importer = new XML_Importer();
	if(!$importer->importFromXml($filePath, "payperdownloadplus_"))
	{
		echo $importer->errorMsg."<br/>";
		return false;
	}
	jimport('joomla.filesystem.file');
	if(JFile::exists($filePath))
		JFile::delete($filePath);
	return true;
	
}


function executeSqlFile($file = "install.mysql.sql", $showErrors = true)
{
	$install_file = JPATH_ADMINISTRATOR . DS . "components" . DS . "com_payperdownloadplus" . DS . $file;
	if(!$fp = @fopen($install_file, "r"))
	{
		echo "Error opening $install_file file<br/>";
		return;
	}
	$text = "";
	while ($data = @fread($fp, 4096))
	{
		$text .= $data;
	}
	$db = JFactory::getDBO();
	$prevDebugState = 0;
	$version = new JVersion;
	if($version->RELEASE > "1.5")
	{
		$prevDebugState = $db->setDebug(false);
	}
	else
	{
		$db->debug(0);
	}
	$queries = $db->splitSql( $text );
	foreach($queries as $query)
	{
		$query = trim( $query );
		if($query)
		{
			$db->setQuery( $query );
			if(!$db->query())
			{
				if($showErrors)
					echo $db->stderr()."<br/>";
			}
		}
	}
	if($version->RELEASE > "1.5")
		$db->setDebug($prevDebugState);
}

function initConfiguration()
{
	$user_component = "com_users";
	$version = new JVersion;
	if($version->RELEASE == "1.5")
	{
		$user_component = "com_user";
	}
	$db = JFactory::getDBO();
	$db->setQuery("SELECT COUNT(*) FROM #__payperdownloadplus_config");
	$count = $db->loadResult();
	if($count == 0)
	{
		$params = array();
		$params['usepayplugin'] = 0;
		$params['paypalaccount'] = '51ea09bd-6994-4c11-8f2e-70115ee8a9d4';
		$params['testmode'] = 1;
		$params['paymentnotificationemail'] = '';
		$params['notificationsubject'] = '';
		$params['notificationtext'] = '';
		$params['usernotificationsubject'] = '';
		$params['usernotificationtext'] = '';
		$params['multilicenseview'] = 0;
		$params['showresources'] = 0;
		$params['loginurl'] = "index.php?option=$user_component&view=login";
		$params['return_param'] = 'return';
		$params['thank_you_page'] = '<span size="16">Thank you for your payment</span>' . 
			'<div class="front_thank_you_continue_url"><a href="{continue}">' . 
			'Continue</a></div>';
		$params['thank_you_page_resource'] = '<span size="16">Thank you for your payment</span>' . 
			'<br/>Check your e-mail for the download link';
		
		$fields = "";
		$values = "";
		foreach($params as $paramkey => $paramvalue)
		{
			if($fields)
				$fields .= ", ";
			if($values)
				$values .= ", ";
			$fields .= $paramkey;
			$values .= "'" . $db->getEscaped($paramvalue) . "'";
		}
		
		$query = "INSERT INTO #__payperdownloadplus_config($fields) VALUES($values)";
		$db->setQuery($query);
		$db->query();
	}
}

function createIndexFileAtFolderPlugin($component, $folder)
{
	$dest_folder = JPATH_SITE.DS.'plugins'.DS.$folder;
	$dest_file_html = JPATH_SITE.DS.'plugins'.DS.$folder.DS.'index.html';
	
	if(!JFolder::exists($dest_folder))
		JFolder::create($dest_folder);
		
	$source_file_html = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.'index.html';
	if(!JFile::exists($dest_file_html))
		JFile::copy($source_file_html, $dest_file_html);
}

function sendMail()
{
	$mail = JFactory::getMailer();
	$root = JURI::root();
	$mail->setSubject("PayperdownloadPlus installed at " . $root);
	$mail->setBody("PayperdownloadPlus installed at " . $root);
	$mail->addRecipient("ratmil@ratmilwebsolutions.com");
	$mail->IsHTML(true);
	$joomla_config = new JConfig();
	$mail->setSender(array($joomla_config->mailfrom, $joomla_config->fromname));
	$mail->Send();
}

function setupViewXmlToJ15($view)
{
	$folder = JPATH_SITE . DS . "components" . DS . "com_payperdownloadplus" . DS . "views" . DS . $view . DS . 'tmpl';
	$xmlfile = $folder . DS . 'default.xml';
	$xml15file = $folder . DS . 'default.xml.15';
	$xml16file = $folder . DS . 'default.xml.16';
	if(JFile::exists($xml15file))
	{
		if(JFile::exists($xmlfile))
		{
			if(!JFile::move($xmlfile, $xml16file))
				echo "error moving $xmlfile, $xml16file";
		}
		else
			echo "XML for view Pay doesn't exist ($xmlfile)";
		if(!JFile::move($xml15file, $xmlfile))
			echo "error moving $xml15file, $xmlfile";
	}
	else
		echo "XML for view Pay J15 doesn't exist ($xml15file)";
}

function setupViewsXmlToJ15()
{
	$version = new JVersion;
	if($version->RELEASE == "1.5")
	{
		setupViewXmlToJ15('pay');
		
		$folder = JPATH_SITE . DS . "components" . DS . "com_payperdownloadplus" . DS . "views" . DS . 'alllicenses' . DS . 'tmpl';
		$xmlfile = $folder . DS . 'default.xml';
		$xml16file = $folder . DS . 'default.xml.16';
		if(JFile::exists($xmlfile))
		{
			if(!JFile::move($xmlfile, $xml16file))
				echo "error moving $xmlfile, $xml16file";
		}
		$folder = JPATH_SITE . DS . "components" . DS . "com_payperdownloadplus" . DS . "views" . DS . 'licenses' . DS . 'tmpl';
		$xmlfile = $folder . DS . 'default.xml';
		$xml16file = $folder . DS . 'default.xml.16';
		if(JFile::exists($xmlfile))
		{
			if(!JFile::move($xmlfile, $xml16file))
				echo "error moving $xmlfile, $xml16file";
		}
		$folder = JPATH_SITE . DS . "components" . DS . "com_payperdownloadplus" . DS . "views" . DS . 'membership' . DS . 'tmpl';
		$xmlfile = $folder . DS . 'default.xml';
		$xml16file = $folder . DS . 'default.xml.16';
		if(JFile::exists($xmlfile))
		{
			if(!JFile::move($xmlfile, $xml16file))
				echo "error moving $xmlfile, $xml16file";
		}
	}
}

function startTransaction()
{
	$db = JFactory::getDBO();
	$db->setQuery("START TRANSACTION");
	$db->query();
}

function commitTransaction()
{
	$db = JFactory::getDBO();
	$db->setQuery("COMMIT");
	$db->query();
}

function updatePlugins()
{
	uninstall_plugin('payperdownloadplus');
	uninstall_plugin('content', 'payperdownloadplus', null, array('content_plugin.js'), array('en-GB.plg_payperdownloadplus_content.ini', 'es-ES.plg_payperdownloadplus_content.ini'));
	uninstall_plugin('phocadownload', 'payperdownloadplus', null, array('phoca_plugin.js'), array('en-GB.plg_payperdownloadplus_phocadownload.ini', 'es-ES.plg_payperdownloadplus_phocadownload.ini'));
	uninstall_plugin('kunena', 'payperdownloadplus', null, array('kunena.jpg'), array('en-GB.plg_payperdownloadplus_kunena.ini', 'es-ES.plg_payperdownloadplus_kunena.ini'));
	uninstall_plugin('jdownload', 'payperdownloadplus', null, array('jdownload_plugin.js'), array('en-GB.plg_payperdownloadplus_jdownload.ini', 'es-ES.plg_payperdownloadplus_jdownload.ini'));
	uninstall_plugin('referer', 'user');
	uninstall_plugin('paytoreadmore', 'content', null, null, array('en-GB.plg_content_paytoreadmore.ini'));
	uninstall_plugin('paytoreadmore', 'editors-xtd', null, array('paytoreadmore.css', 'paytoreadmore.png'), array('en-GB.plg_editor-xtd_paytoreadmore.ini', 'es-ES.plg_editor-xtd_paytoreadmore.ini'));
	install_plugin('com_payperdownloadplus', 'payperdownloadplus', 'system', null, null, array('en-GB.plg_system_payperdownloadplus.ini', 'es-ES.plg_system_payperdownloadplus.ini'));
	install_plugin('com_payperdownloadplus', 'content', 'payperdownloadplus', null, array('content_plugin.js'), array('en-GB.plg_payperdownloadplus_content.ini', 'es-ES.plg_payperdownloadplus_content.ini'));
	install_plugin('com_payperdownloadplus', 'phocadownload', 'payperdownloadplus', null, array('phoca_plugin.js'), array('en-GB.plg_payperdownloadplus_phocadownload.ini', 'es-ES.plg_payperdownloadplus_phocadownload.ini'));
	install_plugin('com_payperdownloadplus', 'kunena', 'payperdownloadplus', null, array('kunena.jpg'), array('en-GB.plg_payperdownloadplus_kunena.ini', 'es-ES.plg_payperdownloadplus_kunena.ini'));
	install_plugin('com_payperdownloadplus', 'jdownload', 'payperdownloadplus', null, array('jdownload_plugin.js'), array('en-GB.plg_payperdownloadplus_jdownload.ini', 'es-ES.plg_payperdownloadplus_jdownload.ini'));
	install_plugin('com_payperdownloadplus', 'referer', 'user');
	install_plugin('com_payperdownloadplus', 'paytoreadmore', 'content', null, null, array('en-GB.plg_content_paytoreadmore.ini', 'es-ES.plg_content_paytoreadmore.ini'));
	install_plugin('com_payperdownloadplus', 'paytoreadmore', 'editors-xtd', null, array('paytoreadmore.css', 'paytoreadmore.png'), array('en-GB.plg_editors_xtd_paytoreadmore.ini', 'es-ES.plg_editors_xtd_paytoreadmore.ini'));
}

function com_install()
{
	$lang = JFactory::getLanguage();
	$lang->load('com_payperdownloadplus', JPATH_SITE.DS.'administrator');
	if(!checkPreviousVersion())
	{
		executeSqlFile("install.mysql.sql");
		executeSqlFile("install.cons.mysql.sql");
		initConfiguration();
		createIndexFileAtFolderPlugin('com_payperdownloadplus', 'payperdownloadplus');
		updatePlugins();
		setupViewsXmlToJ15();
	}
	else
	{
		if(backupCurrentData())
		{
			startTransaction();
			executeSqlFile("install.mysql.sql");
			executeSqlFile("install.changes.mysql.sql", false);
			restoreData();
			initConfiguration();
			commitTransaction();
			createIndexFileAtFolderPlugin('com_payperdownloadplus', 'payperdownloadplus');
			updatePlugins();
			setupViewsXmlToJ15();
		}
		else
		{
			echo JText::_("PAYPERDOWNLOADPLUS_ERROR_DOING_BACK_UP");
		}
	}
	echo "<br/>";
	echo "<font color=\"#ff0000\">" . JText::_("PAYPERDOWNLOADPLUS_MIN_REQUIREMENTS") . "</font>";
	
}

?>