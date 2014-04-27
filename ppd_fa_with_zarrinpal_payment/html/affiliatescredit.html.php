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
class AffiliatesCreditHtmlForm extends BaseHtmlForm
{
	function renderPaymentForm($affiliate_user)
	{
		$root = JURI::root();
		if($affiliate_user->test_mode)
		{
			if($affiliate_user->use_simulator)
				$paypal = $root . "index.php?option=com_payperdownloadplus&task=paypalsim";
			else
				$paypal = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else
		{
			$paypal = "https://www.paypal.com/cgi-bin/webscr";
		}
	?>
		<fieldset class="adminform">
		<legend><?php echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_PAY_TO_USER"));?></legend>
		<table class="admintable">
		<tr>
		<td width="100" align="right" class="key">
		<?php
		echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_USER_NAME"));
		?>
		</td>
		<td>
		<?php echo htmlspecialchars($affiliate_user->name);?>
		</td>
		</tr>
		<tr>
		<td width="100" align="right" class="key">
		<?php
		echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_AMOUNT_TO_PAY"));
		?>
		</td>
		<td>
		<?php echo htmlspecialchars($affiliate_user->credit . " " . $affiliate_user->currency_code);?>
		</td>
		</tr>
		<tr>
		<td width="100" align="right" class="key">
		<?php
		echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_AFFILIATE_AFFILIATE_PROGRAM"));
		?>
		</td>
		<td>
		<?php echo htmlspecialchars($affiliate_user->program_name);?>
		</td>
		</tr>
		<tr>
		<td width="100" align="right" class="key">
		</td>
		<td>
		<form action="<?php echo $paypal;?>" method="post">
		<input type="hidden" name="cmd" value="_xclick"/>
		<input type="hidden" name="business" value="<?php echo htmlspecialchars($affiliate_user->paypal_account);?>"/>
		<input type="hidden" name="custom" value="0"/>
		<input type="hidden" name="item_number" value="<?php echo htmlspecialchars($affiliate_user->affiliate_user_id); ?>"/>
		<input type="hidden" name="item_name" value="<?php echo htmlspecialchars(JText::sprintf("PAYPERDOWNLOADPLUS_AFFILIATE_PAYPAL_ITEM_NAME", $affiliate_user->name)); ?>"/>
		<input type="hidden" name="amount" value="<?php echo htmlspecialchars($affiliate_user->credit); ?>"/>
		<input type="hidden" name="currency_code" value="<?php echo htmlspecialchars($affiliate_user->currency_code); ?>"/>
		<input type="hidden" name="notify_url" value="<?php echo $root;?>index.php?option=com_payperdownloadplus&task=confirmaffiliatepayment"/>
		<input type="hidden" name="return" value="<?php echo $root . "/administrator/index.php?option=com_payperdownloadplus&adminpage=affiliates";?>"/>
		<input type="hidden" name="no_note" value="1"/>
		<input type="hidden" name="no_shipping" value="1"/>
		<input type="hidden" name="rm" value="2"/>
		<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" name="submit" alt="Make payments with payPal - it's fast, free and secure!"/>
		<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
		</form>
		</td>
		</tr>
		</table>
		</fieldset>
		
	<?php
	}
	
	function startForm($task, $option, $dataBindModel)
	{
		if($task != "pay")
			parent::startForm($task, $option, $dataBindModel);
	}
	
	function endForm($task, $option)
	{
		if($task != "pay")
			parent::endForm($task, $option, $dataBindModel);
		else
		{
		?>
			<form action="index.php" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="<?php echo $task;?>" />
			<input type="hidden" name="adminpage" value="<?php echo JRequest::getVar( 'adminpage', '' );?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		<?php
		}
	}

}
?>