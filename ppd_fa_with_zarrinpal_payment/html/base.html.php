<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or
die( 'Direct Access to this location is not allowed.' );

/*** Class to generate HTML code ***/
class BaseHtmlForm
{
	var $extraValidateScript = null;
	var $enctype = "";
	var $showId = false;
	
	function __construct()
	{
	}

	/*** Generate code for admin form beginning***/
	function startForm($task, $option, $dataBindModel)
	{
		JHTML::_('behavior.tooltip');
		$format = JRequest::getVar('format');
		if($format != 'raw')
		{
		$this->renderScripts($dataBindModel, $option, $task);
		$this->renderVars($option);
	?>
		<form action="index.php" method="post" <?php if($this->enctype) echo "enctype=\"multipart/form-data\"";?> id="adminForm" name="adminForm">
	<?php
		}
	}
	
	function renderVars($option)
	{
		$url = JURI::root();
		?>
		<script language="Javascript">
		var site_root = '<?php echo addslashes($url); ?>';
		var site_option = '<?php echo addslashes($option); ?>';
		var site_adminpage = '<?php echo addslashes(JRequest::getVar("adminpage")); ?>';
		</script>
		<?php
	}
	
	/**
	Renders the table of elements
	*/
	function listItems($option, &$rows, &$pageNav, $head, $dataBindModel, $filters)
	{
		$columnCount = 3;
		$dataBinds = $dataBindModel->dataBinds;
		$key = $dataBindModel->getKeyField();
		$useSearchText = false;
		$filterControls = "";
		$resetCode = "";
		foreach($dataBinds as $dataBind)
		{
			if($dataBind->showInGrid)
				$columnCount++;
			if($dataBind->useForTextSearch)
				$useSearchText = true;
			if($dataBind->useForFilter)
			{
				$filterControls .= "&nbsp;&nbsp;" . $dataBind->renderFilter($filters);
				$resetCode .= $dataBind->renderResetFilter($filters);
			}
		}
		if($useSearchText || $filterControls != "")
		{
		?>
		<table style="width:100%">
		<tr>
		<?php
		if($useSearchText)
		{
		?>
		<td align="left">
		<?php echo JText::_('PAYPERDOWNLOADPLUS_FILTER_6'); ?>:
		<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($filters['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('PAYPERDOWNLOADPLUS_GO_7'); ?></button>

		<button onclick="document.getElementById('search').value='';<?php echo $resetCode;?>this.form.submit();">
		<?php echo JText::_('PAYPERDOWNLOADPLUS_RESET_8'); ?></button>
		</td>
		<?php
		}
		?>
		<?php
		if($filterControls)
		{
		?>
		<td align="right">
		<?php echo $filterControls;?>
		</td>
		<?php
		}
		?>
		</tr>
		</table>

		<?php
		}
	?>
		<table class="adminlist" id="table_adminlist">
		<thead>
		<tr>
		<th colspan="<?php echo $columnCount;?>">
		<?php echo htmlspecialchars($head);?>
		</th>
		</tr>
		<tr>
		<th width="2%" align="center">#</th>
		<th width="5%" align="center">
		<input type="checkbox" name="toggle"
		value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
		</th>
		<?php
		foreach($dataBinds as $dataBind)
		{
			if($dataBind->showInGrid)
				$dataBind->renderColumnHeading($filters, $rows);
		}
		if($this->showId)
		{
		?>
		<th class="title" width="2%">
		<?php echo JHTML::_('grid.sort',  htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_ID")), htmlspecialchars($key), @$filters['order_Dir'], @$filters['order'] ); ?>
		</th>
		<?php
		}
		?>
		</tr>
		</thead>
		<tfoot>
		<td colspan="<?php echo $columnCount;?>" align="center"><?php echo $pageNav->getListFooter(); ?></td>
		<input type="hidden" id="table_row_count" value="<?php echo count($rows);?>" />
		</tfoot>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++)
		{
			$row = $rows[$i];
			$checked = JHTML::_('grid.id', $i, $row->$key );
			?>
			<tr class="<?php echo "row$k"; ?>" id="tr<?php echo $i;?>">
			<td><?php echo $pageNav->getRowOffset($i);?></td>
			<td align="center">
			<?php echo $checked; ?>
			<input type="hidden" id="table_item_id<?php echo $i;?>" value="<?php echo htmlspecialchars($row->$key);?>"/>
			</td>
			<?php
			$columnNumber = 0;
			foreach($dataBinds as $dataBind)
			{
				if($dataBind->showInGrid)
				{
					echo "<td>";
					$columnNumber++;
					$dataBind->renderGridCell($row, $i, $columnNumber, $n);
					echo "</td>";
				}
			}
			if($this->showId)
			{
			?>
			<td>
			<?php
			echo htmlspecialchars($row->$key);
			?>
			</td>
			<?php
			}
			?>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<input type="hidden" name="filter_order" value="<?php echo htmlspecialchars($filters['order']); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo htmlspecialchars($filters['order_Dir']); ?>" />
	<?php
	}
	
	/**
	Renders the form to add a new record to a table
	*/
	function add($option, $task, $dataBindModel, $title)
	{
		$dataBinds = $dataBindModel->dataBinds;
		$key = $dataBindModel->keyField;
	?>
		<fieldset class="adminform">
		<legend><?php echo htmlspecialchars($title);?></legend>
		<script language="JavaScript">
		var html_insert_mode = 'add';
		</script>
		<table class="admintable">
		<?php
		for ($i=0, $n=count( $dataBinds ); $i < $n; $i++)
		{
			$databind = $dataBinds[$i];
			if($databind->showInInsertForm)
			{
				echo $databind->renderNew();
				if($databind->onRenderJavascriptRoutine != null)
					echo "<script>".$databind->onRenderJavascriptRoutine."</script>";
			}
		}
		?>
		</table>
		</fieldset>
		<input type="hidden" id="<?php echo $key?>" name="<?php echo $key?>" value=""/>
	<?php
	}
	
	/**
	Renders the form to edit a record from a table
	*/
	function edit($option, $task, &$row, $dataBindModel, $title)
	{
		$dataBinds = $dataBindModel->dataBinds;
		$key = $dataBindModel->keyField;
	?>
		<fieldset class="adminform">
		<legend><?php echo htmlspecialchars($title);?></legend>
		<table class="admintable">
		<script language="JavaScript">
		var html_insert_mode = 'edit';
		</script>
		<?php
		for ($i=0, $n=count( $dataBinds ); $i < $n; $i++)
		{
			$databind = $dataBinds[$i];
			if($databind->showInEditForm)
			{
				echo $databind->renderEdit($row);
				if($databind->onRenderJavascriptRoutine != null)
					echo "<script>".$databind->onRenderJavascriptRoutine."</script>";
			}
		}
		?>
		</table>
		</fieldset>
		<input type="hidden" id="<?php echo $key?>" name="<?php echo $key?>" value="<?php echo htmlspecialchars($row->$key);?>"/>
	<?php
	}
	
	/*** Generates admin form end ***/
	function endForm($task, $option)
	{
		$format = JRequest::getVar('format');
		if($format != 'raw')
		{
	?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="<?php echo $task;?>" />
		<input type="hidden" name="adminpage" value="<?php echo JRequest::getVar( 'adminpage', '' );?>" />
		<?php 
		$itemId = JRequest::getVar("Itemid", "");
		if($itemId != "")
		{
		?>
		<input type="hidden" name="Itemid" value="<?php echo htmlspecialchars($itemId);?>" />
		<?php
		}
		?>
		<?php 
		$view = JRequest::getVar("view", "");
		if($view != "")
		{
		?>
		<input type="hidden" name="view" value="<?php echo htmlspecialchars($view);?>" />
		<?php
		}
		?>
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php
		}
	}
	
	/**
	Renders javascript validation code
	*/
	function renderScripts($dataBindModel, $option, $task)
	{
		if($dataBindModel)
		{
			$dataBinds = $dataBindModel->dataBinds;
			$key = $dataBindModel->keyField;
			if($dataBinds != null)
			{
				if($task == "edit" || $task == "add" || $task == "apply")
				{
					?>
					<script language="JavaScript">
					function validateFormControls()
					{
					<?php
					
						for ($i=0, $n=count( $dataBinds ); $i < $n; $i++)
						{
							$databind = $dataBinds[$i];
							if(!$databind->disabled && (
								(($task == "edit" || $task == "apply") && $databind->showInEditForm) ||	
								($task == "add" && $databind->showInInsertForm) ))
								echo $databind->renderValidateJavascript();
						}
						if($this->extraValidateScript)
							echo $this->extraValidateScript;
					
					?>
					return true;
					}
					</script>
				<?php
				}
			}
			$version = new JVersion;
			if($version->RELEASE == "1.5")
			{
				$submitFunc = "function submitbutton(pressbutton)";
			}
			else
			{
				$submitFunc = "Joomla.submitbutton = function(pressbutton)";
			}
			?>
			<script language="JavaScript">
			<?php echo $submitFunc;?>
			{
				var t1 = '<?php echo JText::_("PAYPERDOWNLOADPLUS_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_SELECTED_ITEMS_9", true); ?>';
				switch(pressbutton)
				{
				case 'remove':
					if(!confirm(t1))
						return;
					break;
				case 'save':
				case 'apply':
					if(!validateFormControls())
						return;
					if(typeof(validateform) != "undefined")
					{
						if(!validateform())
						{
							return;
						}
					}
					break;
				default:
					if(typeof(validatetask) != "undefined")
					{
						if(!validatetask(pressbutton))
						{
							return;
						}
					}
					break;
				}
				submitform(pressbutton);
			}
			
			</script>
			<?php
		}
	}
}
?>