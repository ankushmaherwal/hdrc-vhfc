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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Component\ComponentHelper;

/**
 * TMS - Chalan Model
 *
 * @since  1.0.0
 */
class TmsModelChalan extends AdminModel
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
	public function getTable($type = 'Chalan', $prefix = 'TmsTable', $config = array())
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
			'com_tms.chalan',
			'chalan',
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
			'com_tms.edit.chalan.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
			$data->chalan_items     = $this->getChalanItems($data->id);
			$thirdPartyPaids = $this->getBilltPaid($data->id, true);
			$data->third_party_paid = array();

			if (!empty($thirdPartyPaids))
			{
				foreach ($thirdPartyPaids as $thirdPartyPaid)
				{
					$pData = array();

					$pData['third_party_paid_id'] = $thirdPartyPaid['id'];
					$pData['third_party_paid'] = $thirdPartyPaid['account_id'];
					$pData['chalan_billt_paid'] = $thirdPartyPaid['amount'];
					$data->third_party_paid[] = $pData;
				}
			}
		}

		return $data;
	}

	/**
	 * Function to save chalan
	 *
	 * @param   ARRAY  $data  chalan data
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function save($data)
	{
		// Load tms tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');

		// Validate chalan items
		$valid = $this->validateChalanItems($data);

		if (!$valid)
		{
			return false;
		}

		if (parent::save($data))
		{
			$chalanId = (int) $this->getState($this->getName() . '.id');
			$result = $this->saveChalanItem($data['chalan_items'], $chalanId);

			$paidEntries = $this->getBilltPaid($chalanId, true);
			$thirdPartyPaidIds = array();

			if (!empty($paidEntries))
			{
				foreach ($paidEntries as $paidEntry)
				{
					$thirdPartyPaidIds[] = $paidEntry['id'];
				}
			}

			// Save third party billt paid entry
			if (!empty($data['third_party_paid']))
			{
				foreach ($data['third_party_paid'] as $paid)
				{
					$paidData = new stdclass;
					$paidData->id = !empty($paid['third_party_paid_id']) ? $paid['third_party_paid_id'] : '';
					$paidData->account_id = $paid['third_party_paid'];
					$paidData->amount = $paid['chalan_billt_paid'];
					$paidData->chalan_id = $chalanId;

					$this->saveBilltPaid($paidData);

					// Do not delete the updated paid entries
					if (in_array($paidData->id, $thirdPartyPaidIds) && !empty($paidData->id))
					{
						unset($thirdPartyPaidIds[array_search($paidData->id, $thirdPartyPaidIds)]);
					}
				}

				// Delete removed bill-t paid entires
				if (!empty($thirdPartyPaidIds))
				{
					foreach ($thirdPartyPaidIds as $thirdPartyPaidId)
					{
						$this->deletePaidEntry($thirdPartyPaidId);
					}
				}
			}
			else
			{
				// If all third part paid entries removed then remove all paid entry
				if (!empty($thirdPartyPaidIds))
				{
					foreach ($thirdPartyPaidIds as $thirdPartyPaidId)
					{
						$this->deletePaidEntry($thirdPartyPaidId);
					}
				}
			}

			if (!$result)
			{
				$this->setError(Text::_('COM_TMS_CHALAN_CHALAN_ITEM_SAVE_ERROR'));

				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Function to get chalan items
	 *
	 * @param   ARRAY  $data      chalan item data
	 * @param   INT    $chalanId  chalan id
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function saveChalanItem($data, $chalanId)
	{
		// Get previous paid entries against the chalan items in the chalan
		$paidEntries = $this->getBilltPaid($chalanId, false);
		$chalanItemPaidIds = array();

		if (!empty($paidEntries))
		{
			foreach ($paidEntries as $paidEntry)
			{
				$chalanItemPaidIds[] = $paidEntry['id'];
			}
		}

		foreach ($data as $chalanItem)
		{
			// If empty chalan item found then delete the chalan item
			if (empty($chalanItem['sender_party']) && empty($chalanItem['receiver_party']) && empty($chalanItem['trade_mark']) && empty($chalanItem['units']) && empty($chalanItem['weight']) && empty($chalanItem['freight']) && empty($chalanItem['inam']) && empty($chalanItem['billt_paid']) && !empty($chalanItem['id']))
			{
				$chalanItemTable = Table::getInstance('ChalanItem', 'TmsTable', array());
				$chalanItemTable->load(array('id' => $chalanItem['id']));
				$chalanItemTable->delete();
			}

			// If sender or receiver or units or freight or weight is empty then don't save the record
			if (empty($chalanItem['sender_party']) || empty($chalanItem['receiver_party']) || empty($chalanItem['trade_mark']) || empty($chalanItem['units']) || empty($chalanItem['weight']) || empty($chalanItem['freight']) || empty($chalanItem['inam']))
			{
				continue;
			}

			$chalanItemTable = Table::getInstance('ChalanItem', 'TmsTable', array());

			$chalanItemTable->id = !empty($chalanItem['id']) ? $chalanItem['id'] : '';
			$chalanItemTable->chalan_id = (int) $chalanId;
			$chalanItemTable->sender_party = $chalanItem['sender_party'];
			$chalanItemTable->receiver_party = $chalanItem['receiver_party'];
			$chalanItemTable->trade_mark = $chalanItem['trade_mark'];
			$chalanItemTable->units = (double) $chalanItem['units'];
			$chalanItemTable->weight = (double) $chalanItem['weight'];
			$chalanItemTable->freight = (double) $chalanItem['freight'];
			$chalanItemTable->inam = (double) $chalanItem['inam'];

			// Add entry in chalan item table
			if (!$chalanItemTable->store())
			{
				return false;
			}

			// Add billt paid entry
			if (!empty($chalanItem['billt_paid']))
			{
				$paidData = new stdclass;
				$paidData->id = !empty($chalanItem['billt_paid_id']) ? $chalanItem['billt_paid_id'] : '';
				$paidData->account_id = $chalanItem['receiver_party'];
				$paidData->amount = $chalanItem['billt_paid'];
				$paidData->chalan_id = $chalanId;
				$paidData->chalan_itemid = $chalanItemTable->id;

				// Add bill-t paid entry
				$billtPaidId = $this->saveBilltPaid($paidData);

				// Update bill-t paid id in the chalan item table
				$chalanItemTable->billt_paid_id = $billtPaidId;
				$chalanItemTable->store();
			}

			// Do not delete the updated paid entries
			if (in_array($chalanItemTable->billt_paid_id, $chalanItemPaidIds) && !empty($chalanItemTable->billt_paid_id))
			{
				unset($chalanItemPaidIds[array_search($chalanItemTable->billt_paid_id, $chalanItemPaidIds)]);
			}
		}

		// Delete removed bill-t paid entires
		if (!empty($chalanItemPaidIds))
		{
			foreach ($chalanItemPaidIds as $chalanItemPaidId)
			{
				$this->deletePaidEntry($chalanItemPaidId);
			}
		}

		return true;
	}

	/**
	 * Function to add billt paid amount
	 *
	 * @param   ARRAY  $paidData  chalan item data
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function saveBilltPaid($paidData)
	{
		$billtPaidTable = Table::getInstance('BilltPaid', 'TmsTable', array());

		if (!empty($paidData->id))
		{
			$billtPaidTable->load(array('id' => $paidData->id));
		}

		// Add Update paid entry
		if ($billtPaidTable->save($paidData))
		{
			// Add transactions for the paid amount
			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tms/models');
			$transactionModel = JModelLegacy::getInstance('Transaction', 'TmsModel');

			$params = ComponentHelper::getParams('com_tms');
			$transactionCategory = $params->get('paid_transaction_category', '', 'INT');
			$billtPaidAccount = $params->get('billt_paid_account', '', 'INT');

			$transactionData = array();

			// If paid is negative then add credit entry in the party account else add debit entry
			if ($paidData->amount > 0)
			{
				$transactionData['debit_accounts'] = array("debit_accounts0" => array("debit_account_id" => $paidData->account_id, "debit_amount" => $paidData->amount));
				$transactionData['credit_accounts'] = array("credit_accounts0" => array("credit_account_id" => $billtPaidAccount, "credit_amount" => $paidData->amount));
			}
			else
			{
				$transactionData['debit_accounts'] = array("debit_accounts0" => array("debit_account_id" => $billtPaidAccount, "debit_amount" => $paidData->amount));
				$transactionData['credit_accounts'] = array("credit_accounts0" => array("credit_account_id" => $paidData->account_id, "credit_amount" => $paidData->amount));
			}

			$transactionData['category_id'] = $transactionCategory;
			$transactionData['description'] = Text::sprintf("COM_TMS_CHALAN_BILLT_PAID_DESC", $paidData->chalan_id);
			$transactionData['published']  = 1;
			$transactionData['date'] = Factory::getDate()->toSql();

			// If already transaction is added for the paid entry then update the transaction else add new
			if (!empty($billtPaidTable->transaction_id))
			{
				$transactionData['id'] = $billtPaidTable->transaction_id;
			}

			$transactionModel->save($transactionData);

			// Update transaction id in the bill-T paid entry
			if (empty($billtPaidTable->transaction_id))
			{
				$transactionId = (int) $transactionModel->getState($transactionModel->getName() . '.id');
				$billtPaidTable->transaction_id = $transactionId;

				$billtPaidTable->store();
			}

			return $billtPaidTable->id;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to get chalan items
	 *
	 * @param   INT  $chalanId  chalan id
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function getChalanItems($chalanId)
	{
		if (empty($chalanId))
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__transport_chalan_item');
		$query->where('chalan_id' . ' = ' . (int) $chalanId);
		$db->setQuery($query);
		$chalanItems = $db->loadAssocList();

		foreach ($chalanItems as $k => $chalanItem)
		{
			if (!empty($chalanItem['billt_paid_id']))
			{
				$billtpaidTable = JTable::getInstance('BilltPaid', 'TmsTable', array('dbo', $db));
				$billtpaidTable->load(array('id' => $chalanItem['billt_paid_id']));

				$chalanItems[$k]['billt_paid'] = $billtpaidTable->amount;
			}
		}

		return $chalanItems;
	}

	/**
	 * Function to validate chalan items
	 *
	 * @param   ARRAY  $items  Array of chalan items
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function validateChalanItems($data)
	{
		// Chalan items
		$items = $data['chalan_items'];

		// Third party bill-t paid
		$thirdPartyPaid = $data['third_party_paid'];

		// Count of valid entries
		$ValidEntries = 0;

		// Total freight for the items
		$totalItemsFreight = 0;

		// Total bill-t paid
		$totalBillTPaid = 0;

		foreach ($items as $item)
		{
			if (!empty($item['sender_party']) || !empty($item['receiver_party']) || !empty($item['trade_mark']) || !empty($item['units']) || !empty($item['weight']) || !empty($item['freight']) || !empty($item['inam']))
			{
				if (empty($item['sender_party']) || empty($item['receiver_party']) || empty($item['trade_mark']) || empty($item['units']) || empty($item['weight']) || empty($item['freight']) || empty($item['inam']))
				{
					$this->setError(Text::_('COM_TMS_CHALAN_CHALAN_ITEMS_SAVE_ERROR'));

					return false;
				}
				else
				{
					if (!empty($item['billt_paid']))
					{
						if ($item['billt_paid'] > ($item['freight'] * $item['units']))
						{
							$accountTable = Table::getInstance('Account', 'TmsTable', array());
							$accountTable->load(array('id' => $item['sender_party']));

							$this->setError(Text::sprintf('COM_TMS_CHALAN_SAVE_ERROR_GREATER_PAID_AMOUNT', $accountTable->title));

							return false;
						}

						$totalBillTPaid += !empty($item['billt_paid']) ? $item['billt_paid'] : 0;
					}

					$totalItemsFreight += $item['freight'] * $item['units'];
					$ValidEntries++;
				}
			}
		}

		if (!empty($thirdPartyPaid))
		{
			foreach ($thirdPartyPaid as $partyPaid)
			{
				if (!empty($partyPaid['chalan_billt_paid']))
				{
					$totalBillTPaid += $partyPaid['chalan_billt_paid'];
				}
			}
		}

		// Check if correct Bill-T paid is added or not
		if ($totalBillTPaid != ($totalItemsFreight - $data['total_freight'] + $data['advance']))
		{
			$diff = ($totalItemsFreight - $data['total_freight'] + $data['advance']) - ($totalBillTPaid);
			$this->setError(Text::sprintf('COM_TMS_CHALAN_SAVE_ERROR_BILL_T_PAID_INCORRECT', $diff));

			return false;
		}

		if (count($ValidEntries))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Delete chalan and chalan items
	 *
	 * @param   object  &$pks  The primary key related to the chalan that was deleted.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function delete(&$pks)
	{
		$return = parent::delete($pks);

		if ($return)
		{
			// Delete chalan items
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__transport_chalan_item'));
			$query->where($db->quoteName('chalan_id') . ' IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();

			// Delete chalan items
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__transport_billt_paid'));
			$query->where($db->qn('chalan_id') . ' IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$paidEntries = $db->loadAssocList();

			foreach ($paidEntries as $paidEntry)
			{
				$this->deletePaidEntry($paidEntry['id']);
			}
		}

		return $return;
	}

	/**
	 * Function to get third part paid amount
	 *
	 * @param   INT      $chalanId    chalan id
	 * @param   BOOLEAN  $thirdParty  third party paid
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function getBilltPaid($chalanId, $thirdParty = false)
	{
		if (empty($chalanId))
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__transport_billt_paid');
		$query->where($db->qn('chalan_id') . ' = ' . (int) $chalanId);

		if ($thirdParty)
		{
			$query->where($db->qn('chalan_itemid') . ' = 0');
		}
		else
		{
			$query->where($db->qn('chalan_itemid') . ' != 0');
		}

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	public function deletePaidEntry($id)
	{
		if (!empty($id))
		{
			$billtPaidTable = Table::getInstance('BilltPaid', 'TmsTable', array());
			$billtPaidTable->load(array('id' => $id));

			// Delete related transactions
			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tms/models');
			$transactionModel = JModelLegacy::getInstance('Transaction', 'TmsModel');
			$transactionModel->setState('forceDelete', 1);
			$transactionModel->delete($billtPaidTable->transaction_id);

			// If paid is against chalan item then remove the reference from the chalan item table
			if (!empty($billtPaidTable->chalan_itemid))
			{
				$chalanItemTable = Table::getInstance('ChalanItem', 'TmsTable', array());
				$chalanItemTable->load(array('id' => $billtPaidTable->chalan_itemid));
				$chalanItemTable->billt_paid_id = 0;
				$chalanItemTable->store();
			}

			// Delete paid entry
			$billtPaidTable->delete();

			return true;
		}
		else
		{
			return false;
		}
	}
}
