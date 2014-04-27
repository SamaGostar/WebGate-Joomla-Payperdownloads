<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');

class PayPerDownloadPlusViewNoAccess extends JView
{
	function display($tpl = null)
	{
		$option = JRequest::getVar('option');
		JHTML::_('stylesheet', 'frontend.css', 'components/'. $option . '/css/');
		$model = $this->getModel();
		if($model)
		{
			$content = $model->getNoAccessPage();
			$this->assignRef("content", $content);
			parent::display($tpl);
		}
		else
			echo "model not found";
	}
}

?>