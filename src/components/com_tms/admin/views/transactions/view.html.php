<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * TMS - Transactions View
 *
 * @since  1.0.0
 */
class TmsViewTransactions extends HtmlView
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get application
		$app = Factory::getApplication();

		// Get data from the model
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = JHelperContent::getActions('com_tms');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the submenu
		TmsHelper::addSubmenu('transactions');

		// Set the toolbar and number of found items
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(Text::_('COM_TMS_MANAGE_TRANSACTIONS'), 'address tms');

		if ($this->canDo->get('core.create'))
		{
			JToolBarHelper::addNew('transaction.add', 'JTOOLBAR_NEW');
			JToolBarHelper::custom('transaction.addCreditNote', 'new', '', Text::_('COM_TMS_TRANSACTION_ADD_CREDIT_NOTE'), false);
			JToolBarHelper::custom('transaction.addDebitNote', 'new', '', Text::_('COM_TMS_TRANSACTION_ADD_DEBIT_NOTE'), false);
		}

		if ($this->canDo->get('core.edit'))
		{
			JToolBarHelper::editList('transaction.edit', 'JTOOLBAR_EDIT');
		}

		if ($this->canDo->get('core.edit.state'))
		{
			JToolBarHelper::publish('transactions.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('transactions.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($this->canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'transactions.delete', 'JTOOLBAR_DELETE');
		}

		JToolBarHelper::custom('getAccountStatement', 'print', '', Text::_('COM_TMS_TRANSACTION_PRINT'), false);

		if ($this->canDo->get('core.admin'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_tms');
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_TMS_MANAGE_TRANSACTIONS'));
	}
}
