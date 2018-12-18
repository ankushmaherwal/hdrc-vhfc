<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * TMS - Transaction Model
 *
 * @since  1.0.0
 */
class TmsModelTransaction extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Transaction', $prefix = 'TmsTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_tms.transaction',
			'transaction',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_tms.edit.transaction.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to add transaction.
	 *
	 * @param   ARRAY  $data  Transaction data
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	public function save($data)
	{
		$crossTransactionData = array();
		$crossTransactionData = $data;

		if (parent::save($data))
		{
			$transactionId = empty($data['id']) ? (int) $this->getState($this->getName() . '.id') : $data['id'];

			$params = ComponentHelper::getParams('com_tms');
			$companyCashAccount = $params->get('company_cash_account', '', 'INT');

			$table = $this->getTable();
			$table->load(array('reference_id' => $transactionId));

			if ($companyCashAccount == $data['account_id'])
			{
				// In case of update if transaction account is selected as company cash account then remove the reference transaction
				if (!empty($table->id))
				{
					$table->delete();
				}

				return true;
			}

			$table->account_id = $companyCashAccount;
			$table->reference_id = $transactionId;
			$table->date = isset($data['date']) ? $data['date'] : Factory::getDate()->toSql();
			$table->published = $data['published'];
			$table->description = Text::sprintf("COM_TMS_TRANSACTION_CROSS_TRANSACTION_DESC", $transactionId);

			if (!empty($data['debit']))
			{
				$table->credit = $data['debit'];
				$table->debit = 0;
			}
			else
			{
				$table->credit = 0;
				$table->debit = $data['credit'];
			}

			$addNote = $this->getState('addNote', '0');

			// If its not a credit note or debit note then add a cross transaction against company cash account, also no need to add cross transaction if transaction is done on company cash account
			if (empty($addNote) && $companyCashAccount != $data['account_id'])
			{
				if ($table->store() === true)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function delete(&$pks)
	{
		$pks   = (array) $pks;
		$table = $this->getTable();
		$db    = Factory::getDbo();
		$app   = Factory::getApplication();
		$forceDelete = $this->getState('forceDelete', 0, 'INT');

		foreach ($pks as $pk)
		{
			$table->load($pk);
			$referenceId = $table->reference_id;
			$transactionId = $table->id;

			// Check if transaction is associated with paid entry, if soo then dont allow to delete the entry
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');
			$paidTable = Table::getInstance('BilltPaid', 'TmsTable', array('dbo', $db));

			// Check for reference ID
			$paidTable->load(array('transaction_id' => $referenceId));

			if (!empty($paidTable->id))
			{
				$app->enqueueMessage(Text::sprintf("COM_TMS_TRANSACTION_DELETE_ERROR_PAID_TRANSACTION", $paidTable->chalan_id) , 'error');

				return false;
			}

			// Check for transaction ID
			$paidTable->load(array('transaction_id' => $transactionId));

			if (!empty($paidTable->id) && $forceDelete == 0)
			{
				$app->enqueueMessage(Text::sprintf("COM_TMS_TRANSACTION_DELETE_ERROR_PAID_TRANSACTION", $paidTable->chalan_id) , 'error');

				return false;
			}

			// If there is any cross transaction for the transaction then delete the cross transaction entry
			if (!empty($referenceId) && $forceDelete == 0)
			{
				$table->load($referenceId);
				$table->delete();
			}

			// If the transaction to be deleted is reference transaction then delete parent transaction too
			if (!empty($transactionId))
			{
				$table->load(array('reference_id' => $transactionId));
				$table->delete();
			}

			if (!parent::delete($pk))
			{
				return false;
			}
		}

		return true;
	}
}
