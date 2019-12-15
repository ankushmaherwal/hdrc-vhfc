<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;

/**
 * Transactions Controller
 *
 * @since  1.0.0
 */
class TmsControllerTransactions extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Transaction', $prefix = 'TmsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to generate the account statement as pdf.
	 *
	 * @return  void.
	 *
	 * @since   1.0.0
	 */
	public function generateStatement()
	{
		Session::checkToken() or die( 'Invalid Token' );

		$jInput = Factory::getApplication()->input->post;
		$data = $jInput->get('filter', array(), 'ARRAY');

		if (empty($data['account_id']))
		{
			return false;
		}

		JLoader::import('components.com_tms.models.transactions', JPATH_ADMINISTRATOR);
		$transactionsModel = BaseDatabaseModel::getInstance('Transactions', 'TmsModel', array('ignore_request' => true));
		$accountModel      = $this->getModel('account');
		$transactionsModel->setState('filter.account_id', $data['account_id']);
		$transactionsModel->setState('filter.published', 1);

		if (!empty($data['category_id']))
		{
			$transactionsModel->setState('filter.category_id', $data['category_id']);
		}

		if (!empty($data['from_date']))
		{
			$transactionsModel->setState('filter.from_date', $data['from_date']);
		}

		if (!empty($data['to_date']))
		{
			$transactionsModel->setState('filter.to_date', $data['to_date']);
		}

		$accountTable = $accountModel->getTable('account');
		$accountTable->load($data['account_id']);

		$statementData = array();
		$statementData['transactions']   = $transactionsModel->getItems();
		$statementData['accountBalance'] = $accountModel->getBalance($data['account_id']);
		$statementData['statementFrom']  = ($data['from_date']) ? $data['from_date'] : '';
		$statementData['statementTo']    = ($data['to_date']) ? $data['to_date'] : '';
		$statementData['accountDetails'] = $accountTable;
		$statementHTML  = LayoutHelper::render('transactions.statement', $statementData, JPATH_ADMINISTRATOR . '/components/com_tms');
		$statementFileName = trim(str_replace(' ', '', $accountTable->title . Factory::getDate()->toSql())) . '.pdf';

		JLoader::import('components.com_tms.helpers.tms', JPATH_ADMINISTRATOR);
		TmsHelper::generatePdf($statementHTML, $statementFileName, 1);
		
		jexit();
	}
}
