<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

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
					if(!JFile::exists($dest_file))
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

function com_uninstall()
{
	uninstall_plugin('payperdownloadplus');
	uninstall_plugin('content', 'payperdownloadplus', null, array('content_plugin.js'), array('en-GB.plg_payperdownloadplus_content.ini', 'es-ES.plg_payperdownloadplus_content.ini'));
	uninstall_plugin('phocadownload', 'payperdownloadplus', null, array('phoca_plugin.js'), array('en-GB.plg_payperdownloadplus_phocadownload.ini', 'es-ES.plg_payperdownloadplus_phocadownload.ini'));
	uninstall_plugin('kunena', 'payperdownloadplus', null, array('kunena.jpg'), array('en-GB.plg_payperdownloadplus_kunena.ini', 'es-ES.plg_payperdownloadplus_kunena.ini'));
	uninstall_plugin('jdownload', 'payperdownloadplus', null, array('jdownload_plugin.js'), array('en-GB.plg_payperdownloadplus_jdownload.ini', 'es-ES.plg_payperdownloadplus_jdownload.ini'));
	uninstall_plugin('referer', 'user');
	uninstall_plugin('paytoreadmore', 'content', null, null, array('en-GB.plg_content_paytoreadmore.ini'));
	uninstall_plugin('paytoreadmore', 'editors-xtd', null, array('paytoreadmore.css', 'paytoreadmore.png'), array('en-GB.plg_editor-xtd_paytoreadmore.ini', 'es-ES.plg_editor-xtd_paytoreadmore.ini'));
}

?>