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
 * TMS - Transactionnote Model
 *
 * @since  1.0.0
 */
abstract class TmsModelTransactionnote extends JModelAdmin
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
			'com_tms.transactionnote',
			'transactionnote',
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
			'com_tms.edit.transactionnote.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get transaction data.
	 *
	 * @param   INT  $pk  Transaction id
	 *
	 * @return  OBJECT  Transaction data.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem();

		if (!empty($item->id))
		{
			// Get reference transactions
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__transport_transaction_reference'));
			$query->where($db->quoteName('reference_id') . ' = ' . $item->id);
			$db->setQuery($query);
			$transactionReference = $db->loadObject();

			$item->account_id = $transactionReference->account_id;
			$item->amount = ($transactionReference->debit != 0) ? $transactionReference->debit : $transactionReference->credit;

			return $item;
		}
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
		$creditNote = isset($data['creditnote']) ? 1 : 0;
		$data['note'] = 1;

		if (empty($data['amount']))
		{
			$this->setError(Text::_('COM_TMS_TRANSACTION_ADD_NOTE_INVALID_AMOUNT'));

			return false;
		}

		$db = Factory::getDbo();

		if (parent::save($data))
		{
			$transactionId = empty($data['id']) ? (int) $this->getState($this->getName() . '.id') : $data['id'];

			if (!empty($transactionId))
			{
				// In case of update delete all the reference transactions
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__transport_transaction_reference'));
				$query->where($db->quoteName('reference_id') . ' = ' . $transactionId);
				$db->setQuery($query);
				$db->execute();
			}

			$referenceTransaction = new stdClass();
			$referenceTransaction->account_id = $data['account_id'];
			$referenceTransaction->reference_id = $transactionId;

			if ($creditNote)
			{
				$referenceTransaction->debit = 0;
				$referenceTransaction->credit = $data['amount'];
			}
			else
			{
				$referenceTransaction->debit = $data['amount'];
				$referenceTransaction->credit = 0;
			}

			$db->insertObject('#__transport_transaction_reference', $referenceTransaction);

			return true;
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

			if ($forceDelete == 0)
			{
				// Check if transaction is associated with paid entry, if soo then dont allow to delete the entry
				Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');
				$paidTable = Table::getInstance('BilltPaid', 'TmsTable', array('dbo', $db));
				$paidTable->load(array('transaction_id' => $table->id));

				if (!empty($paidTable->id))
				{
					$app->enqueueMessage(Text::sprintf("COM_TMS_TRANSACTION_DELETE_ERROR_PAID_TRANSACTION", $paidTable->chalan_id) , 'error');

					return false;
				}
			}

			if (!parent::delete($pk))
			{
				return false;
			}
			else
			{
				// Delete all the reference transactions
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__transport_transaction_reference'));
				$query->where($db->quoteName('reference_id') . ' = ' . $transactionId);
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}
}
