<?php
// import the JPlugin class
jimport('joomla.event.plugin');

global $mainframe;

class plgPayperDownloadPlusJDownload extends JPlugin
{
	public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject);
		// load the language file
		$lang = JFactory::getLanguage();
		$lang->load('plg_payperdownloadplus_jdownload', JPATH_SITE.DS.'administrator');
	}

	function onIsActive(&$plugins)
	{
		$version = new JVersion;
		if(!JFolder::exists(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jdownloads'))
			return false;
		$component = JComponentHelper::getComponent('com_jdownloads', true);
		if($component->enabled)
		{
			jimport('joomla.filesystem.file');
			$image = "";
			if(JFile::exists(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jdownloads' . DS .
					'images' . DS . 'jd_logo_48.png'))
				$image = "administrator/components/com_jdownloads/images/jd_logo_48.png";
				
			$plugins[] = array("name" => "JDownloads", "description" => JText::_("JDownloads file"), 
				"image" => $image);
		}
	}
	
	function reorderCats(&$cats_ordered, $cats, $parent_id, $depth)
	{
		$count = count($cats);
		for($i = 0; $i < $count; $i++)
		{
			$cat = $cats[$i];
			if($cat->parent_id == $parent_id)
			{
				$cat->depth = $depth;
				$cats_ordered[] = $cat;
				$this->reorderCats($cats_ordered, $cats, $cat->cat_id, $depth + 1);
			}
		}
	}
	
	function getJDownloadCategories()
	{
		$db = JFactory::getDBO();
		$db->setQuery('SELECT cat_id, cat_title, parent_id FROM #__jdownloads_cats');
		$cats = $db->loadObjectList();
		$cats_ordered = array();
		$this->reorderCats($cats_ordered, $cats, 0, 0);
		return $cats_ordered;
	}
	
	function getFiles($cat_id)
	{
		$db = JFactory::getDBO();
		$db->setQuery('SELECT file_id as id, file_title as title FROM #__jdownloads_files WHERE cat_id = ' . (int)$cat_id);
		return $db->loadObjectList();
	}
	
	function onRenderConfig($pluginName, $resource)
	{
		if($pluginName == "JDownloads")
		{
			if($resource)
			{
				$file_id = $resource->resource_id;
				$category_id = $resource->resource_params;
				if($category_id)
					$files = $this->getFiles($category_id);
			}
			$uri = JURI::root();
			$scriptPath = "administrator/components/com_payperdownloadplus/js/";
			JHTML::script('ajax_source.js', $scriptPath, false);
			$version = new JVersion;
			if($version->RELEASE >= "1.6")
				$plugin_path = "plugins/payperdownloadplus/jdownload/";
			else
				$plugin_path = "plugins/payperdownloadplus/";
			$scriptPath = $uri . $plugin_path;
			JHTML::script('jdownload_plugin.js', $scriptPath, false);
			$cats = $this->getJDownloadCategories();
			?>
			<tr>
			<td  width="100" align="right" class="key"><?php echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_CATEGORY"));?></td>
			<td>
			<select id="jdownload_category" name="jdownload_category" onchange="jdownload_plugin_category_change();">
			<option value="0"><?php echo JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_ALL_CATEGORIES");?></option>
			<?php
			foreach($cats as $cat)
			{
				$space = '';
				for($i = 0; $i < $cat->depth; $i++)
					$space .= '&nbsp;&nbsp;&nbsp;&nbsp;';
				$selected = $cat->cat_id == $category_id ? "selected":"";
				echo "<option value=\"" . htmlspecialchars($cat->cat_id) . "\" $selected>" . $space . htmlspecialchars($cat->cat_title) . "</option>";
			}
			?>
			</select>
			</td>
			</tr>
			<tr>
			<td  width="100" align="right" class="key"><?php echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_DOWNLOAD"));?></td>
			<td>
			<select id="jdownload_file" name="jdownload_file">
			<option value="0"><?php echo JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_ALL_DOWNLOADS");?></option>
			<?php
			if($files)
			foreach($files as $file)
			{
				$selected = $file->id == $file_id ? "selected":"";
				echo "<option value=\"" . htmlspecialchars($file->id) . "\" $selected>" . htmlspecialchars($file->title) . "</option>";
			}
			?>
			</select>
			</td>
			</tr>
			<?php
		}
	}
	
	function onGetSaveData(&$resourceId, 
		$pluginName, &$resourceName, &$resourceParams, &$optionParameter,
		&$resourceDesc)
	{
		if($pluginName == "JDownloads")
		{
			$optionParameter = "com_jdownloads";
			$resourceId = JRequest::getInt('jdownload_file');
			$categoryId = JRequest::getInt('jdownload_category');
			$db = JFactory::getDBO();
			$query = "";
			$resourceDesc = JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_ALL_DOWNLOADS");
			if($resourceId)
			{
				$query = "SELECT file_id, file_title as title FROM #__jdownloads_files WHERE file_id = " . $resourceId;
				$resourceDesc = JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_DOWNLOAD");
			}
			else if($categoryId)
			{
				$resourceId = -1;
				$query = "SELECT cat_id, cat_title as title FROM #__jdownloads_cats WHERE cat_id = " . $categoryId;
				$resourceDesc = JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_CATEGORY");
			}
			else
			{
				$resourceId = -1;
			}
			$resourceName = JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_ALL_DOWNLOADS");
			if($query)
			{
				$db->setQuery($query);
				$resource = $db->loadObject();
				if($resource)
					$resourceName = $resource->title;
			}
			$resourceParams = $categoryId;
		}
	}
	
	function getParentCategories($id)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT cat_id FROM #__jdownloads_files WHERE file_id = " . (int)$id);
		$category = $db->loadResult();
		$parentCategories = array();
		while($category)
		{
			$parentCategories []= $category;
			$db->setQuery("SELECT parent_id FROM #__jdownloads_cats WHERE cat_id = " . (int)$category);
			$category = (int)$db->loadResult();
		}
		return $parentCategories;
	}
	
	function onValidateAccess($option, $resources, &$allowAccess, &$requiredLicenses, &$resourcesId)
	{
		if($option == 'com_jdownloads')
		{
			$task = JRequest::getVar('task');
			$view = JRequest::getVar('view');
			if($task == 'finish' || $view == 'finish')
			{
				$requiredLicenses = array();
				$resourcesId = array();
				$downloads = array();
				$download = JRequest::getInt('cid', 0);
				$multiple_downloads = false;
				if($download)
				{
					$downloads[] = $download;
				}
				else
				{
					$multiple_downloads = true;
					$downloads = explode(',', JRequest::getVar('list'));
				}
				if(count($downloads) == 0)
					return;
				
				foreach($resources as $resource)
				{
					if(array_search($resource->resource_id, $downloads) !== false)
					{
						if($resource->license_id)
						{
							if(array_search($resource->license_id, $requiredLicenses) === false)
								$requiredLicenses[] = $resource->license_id;
						}
						else
						{
							if(array_search($resource->resource_license_id, $resourcesId) === false)
								$resourcesId[] = $resource->resource_license_id;
						}
						$allowAccess = false;
					}
				}
				
				if(count($requiredLicenses) == 0 && count($resourcesId) == 0)
				{
					$allParentCategories = array();
					foreach($downloads as $d)
					{	
						$parentCategories = $this->getParentCategories($d);
						$allParentCategories = array_merge($allParentCategories, $parentCategories);
					}
					foreach($resources as $resource)
					{
						if($resource->resource_id == -1 && array_search($resource->resource_params, $allParentCategories) !== false)
						{
							if($resource->license_id)
							{
								if(array_search($resource->license_id, $requiredLicenses) === false)
									$requiredLicenses[] = $resource->license_id;
							}
							else
							{
								if(array_search($resource->resource_license_id, $resourcesId) === false)
									$resourcesId[] = $resource->resource_license_id;
							}
							$allowAccess = false;
						}
					}
				}
				
				if(count($requiredLicenses) == 0 && count($resourcesId) == 0)
				{
					foreach($resources as $resource)
					{
						if($resource->resource_id == -1 && $resource->resource_params == 0)
						{
							if($resource->license_id)
							{
								if(array_search($resource->license_id, $requiredLicenses) === false)
									$requiredLicenses[] = $resource->license_id;
							}
							else
							{
								if(array_search($resource->resource_license_id, $resourcesId) === false)
									$resourcesId[] = $resource->resource_license_id;
							}
							$allowAccess = false;
						}
					}
				}
				
				if($multiple_downloads && !$allowAccess)
				{
					//multiple licenses for multiple downloads is not allowed
					if(count($resourcesId) > 1 || count($requiredLicenses) > 1)
					{
						$returnPage = $this->_innerGetReturnPage();
						$this->addMenuItemParameter($returnPage);
						$app & JFactory::getApplication();
						$app->redirect($returnPage, JText::_("PAYPERDOWNLOADPLUS_JDOWNLOAD_PLUGIN_NO_MULTIPLE_DOWNLOAD"));
						exit;
					}
				}
			}
		}
	}
	
	function addMenuItemParameter(&$url)
	{
		if(strpos($url, "Itemid=") !== false)
			return;
		$db = JFactory::getDBO();
		$query = "SELECT id FROM #__menu WHERE link = '" . $db->getEscaped($url) . "'";
		$db->setQuery($query);
		$itemId = $db->loadResult();
		if(!$itemId)
			$itemId = JRequest::getVar('Itemid');
		if($itemId)
			$url .= "&Itemid=" . urlencode($itemId);
	}
	
	function onAjaxCall($plugin, &$output)
	{
		if($plugin == "jdownload")
		{
			$x = JRequest::getInt('x', 0);
			$db = JFactory::getDBO();
			$db->setQuery('SELECT file_id, file_title as title FROM #__jdownloads_files WHERE cat_id = ' . $x);
			$files = $db->loadObjectList();
			$output = '<<' . count($files);
			foreach($files as $file)
			{
				$output .= '>' . htmlspecialchars($file->file_id) . "<" . htmlspecialchars($file->title);
			}
			$output .= '>>';
		}
	}
	
	function _innerGetReturnPage()
	{
		$downloads = array();
		$download = JRequest::getInt('cid', 0);
		if($download)
		{
			$downloads[] = $download;
		}
		else
		{
			$downloads = split(',', JRequest::getVar('list'));
		}
		if(count($downloads) == 0)
			return;
		$ds = (int)$downloads[0];
		$db = JFactory::getDBO();
		$db->setQuery("SELECT cat_id FROM #__jdownloads_files WHERE file_id = $ds");
		$cat = $db->loadResult();
		if(!$cat)
			return;
		if(count($downloads) > 1)
			$returnPage = "index.php?option=com_jdownloads&view=viewcategory&catid=" . 
				urlencode($cat);
		else
			$returnPage = "index.php?option=com_jdownloads&view=summary&catid=" . 
				urlencode($cat). "&cid=" . urlencode($downloads[0]);
		return $returnPage;
	}
	
	function getReturnPage($option, &$returnPage)
	{
		if($option == "com_jdownloads")
		{
			$returnPage = $this->_innerGetReturnPage();
		}
	}
	
	function onCheckDecreaseDownloadCount($option, $resources, $requiredLicenses, $resourcesId, &$decreaseDownloadCount)
	{
		if($option == 'com_jdownloads')
		{
			$decreaseDownloadCount = true;
		}
	}
	
	/*
	Returns item id for current article. 
	*/
	function onGetItemId($option, &$itemId)
	{
		if($option == 'com_jdownloads')
		{
			$itemId = JRequest::getInt('cid', 0);
		}
	}
	
	function onGetLink($resource, &$link)
	{
		if($resource->resource_option_parameter == 'com_jdownloads')
		{
			if($resource->resource_id != -1)
			{
				$db = JFactory::getDBO();
				$db->setQuery("SELECT cat_id FROM #__jdownloads_files WHERE file_id = " . (int)$resource->resource_id);
				$catid = $db->loadResult();
				$link = "index.php?option=com_jdownloads&view=summary&cid=" . (int)$resource->resource_id . 
					"&catid=" . (int)$catid;
			}
			else
			{
				$categoryId = (int)$resource->resource_params;
				if($categoryId != 0)
					$link = "index.php?option=com_jdownloads&view=viewcategory&catid=" . (int)$categoryId;
				else
					$link = "index.php?option=com_jdownloads&view=viewcategories";
			}
		}
	}
	
}
?>