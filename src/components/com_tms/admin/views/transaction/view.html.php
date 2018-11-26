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

/**
 * TMS - Transaction View
 *
 * @since  1.0.0
 */
class TmsViewTransaction extends HtmlView
{
	protected $form;

	protected $item;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		$this->user            = Factory::getUser();

		$this->canCreate       = $this->user->authorise('core.create', 'com_tms');
		$this->canEdit         = $this->user->authorise('core.edit', 'com_tms');
		$this->canChangeStatus = $this->user->authorise('core.edit.state', 'com_tms');
		$this->canDelete       = $this->user->authorise('core.delete', 'com_tms');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Set the toolbar
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
		$isNew = (isset($this->item->id) && !empty($this->item->id)) ? 0 : 1;

		JToolBarHelper::title($isNew ? JText::_('COM_TMS_TRANSACTION_CREATE') : JText::_('COM_TMS_TRANSACTION_EDIT'), 'apply');

		if ($this->canEdit || $this->canCreate)
		{
			JToolBarHelper::apply('transaction.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('transaction.save', 'JTOOLBAR_SAVE');
			JToolbarHelper::save2new('account.save2new');
		}

		JToolBarHelper::cancel('transaction.cancel', 'JTOOLBAR_CANCEL');
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = (isset($this->item->id) && !empty($this->item->id)) ? 0 : 1;
		$document = Factory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_TMS_TRANSACTION_CREATE') : JText::_('COM_TMS_TRANSACTION_EDIT'));
	}
}
