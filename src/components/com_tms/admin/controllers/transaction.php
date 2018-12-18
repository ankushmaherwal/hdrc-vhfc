<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

/**
 * TMS Transaction Controller
 *
 * @since  1.0.0
 */
class TmsControllerTransaction extends FormController
{
	/**
	 * Implement to allowAdd or not
	 *
	 * @return bool
	 */
	protected function allowAdd($data = Array())
	{
		return Factory::getUser()->authorise("core.create", "com_tms");
	}

	/**
	 * Implement to allow edit or not
	 *
	 * @return bool
	 */
	protected function allowEdit($data = Array(), $key = 'id')
	{
		return Factory::getUser()->authorise("core.edit", "com_tms");
	}

	public function addCreditNote()
	{
		// Redirect to the edit screen to add credit note.
		$this->setRedirect('index.php?option=com_tms&view=transaction&layout=edit&creditNote=1');
	}

	public function addDebitNote()
	{
		// Redirect to the edit screen to add credit note.
		$this->setRedirect('index.php?option=com_tms&view=transaction&layout=edit&debitNote=1');
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		$input = Factory::getApplication()->input;
		$model = $this->getModel();

		$creditNote = $input->get('creditNote', '0', 'INT');
		$debitNote  = $input->get('debitNote', '0', 'INT');

		// Check if only credit or debit note is to be added
		if (!empty($creditNote) || !empty($debitNote))
		{
			// If this is set then no cross transaction against company cash account will be added
			$model->setState('addNote', 1);
		}

		return parent::save();
	}
}
