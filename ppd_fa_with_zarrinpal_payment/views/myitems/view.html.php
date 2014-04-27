<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');

class PayPerDownloadPlusViewMyItems extends JView
{
	function display($tpl = null)
	{
		$option = JRequest::getVar('option');
		JHTML::_('stylesheet', 'frontend.css', 'components/'. $option . '/css/');
		$model = $this->getModel();
		if($model)
		{
			$limit = JRequest::getVar( 'limit', 20 );
			$start = JRequest::getVar( 'limitstart', 0 );
			$downloadLinks = $model->getUserItems($start, $limit);
			$total = $model->getUserTotalItems();
			jimport( 'joomla.html.pagination' );
			$objPagination = new JPagination( $total, $start, $limit );
			$this->assignRef("pagination", $objPagination);
			$this->assignRef("downloadLinks", $downloadLinks);
			parent::display($tpl);
		}
		else
			echo "model not found";
	}
}

?>