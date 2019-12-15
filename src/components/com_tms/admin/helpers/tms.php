<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * TMS component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since  1.0.0
 */
abstract class TmsHelper extends ContentHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $submenu  submenu
	 *
	 * @return Bool
	 */
	public static function addSubmenu($submenu)
	{
		$app       = Factory::getApplication();
		$extension = $app->input->get('extension', '', "STRING");

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_ACCOUNTS'),
			'index.php?option=com_tms&view=accounts', (($submenu === 'accounts') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_ACCOUNT_CATEGORY'),
			'index.php?option=com_categories&view=categories&extension=com_tms.account', ($submenu == 'categories.account') ? true : false
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_TRANSACTIONS'),
			'index.php?option=com_tms&view=transactions', (($submenu === 'transactions') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_TRANSACTION_CATEGORY'),
			'index.php?option=com_categories&view=categories&extension=com_tms.transaction', ($submenu == 'categories.transaction') ? true : false
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_VEHICLES'),
			'index.php?option=com_tms&view=vehicles', (($submenu === 'vehicles') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_CHALANS'),
			'index.php?option=com_tms&view=chalans', (($submenu === 'chalans') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_FREIGHT'),
			'index.php?option=com_tms&view=freight', (($submenu === 'freight') ? true : false)
		);
	}

	/**
	 * Method to generate pdf from html
	 *
	 * @param   string  $html      replacement data for tags
	 * @param   string  $pdffile   name of pdf file
	 * @param   string  $download  int
	 *
	 * @return  string  html with css applied
	 */
	public static function generatePdf($html, $pdffile, $download = 0)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		require_once  JPATH_SITE . "/libraries/vendor/dompdf/autoload.inc.php";

		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="charset=utf-8" /><style type="text/css">* {font-family: "dejavu sans" !important}</style></head><body>' . $html . '</body></html>';

		if (get_magic_quotes_gpc())
		{
			$html = stripslashes($html);
		}

		// Set font for the pdf download.
		$options = new Options;
		$options->setDefaultFont('DeJaVu Sans');

		$dompdf = new DOMPDF($options);
		$dompdf->loadHTML($html, 'UTF-8');

		// Set the page size and oriendtation.
		$dompdf->setPaper('A4', 'portrait');

		$dompdf->render();
		$dompdf->stream($pdffile);
	}
}
