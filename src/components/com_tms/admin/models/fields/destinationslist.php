<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of accounts
 *
 * @since  1.0.0
 */
class JFormFieldDestinationsList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0.0
	 */
	protected $type = 'destinationsList';

	/**
	 * Field to decide if options are being loaded externally and from xml
	 *
	 * @var		integer
	 * @since	1.0.0
	 */
	protected $loadExternally = 1;

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of HTMLHelper options.
	 *
	 * @since   1.0.0
	 */
	protected function getOptions()
	{
		$params = ComponentHelper::getParams('com_tms');
		$destinationsList = $params->get('destinations_list', '', 'STRING');

		$destinationsList = explode(',', $destinationsList);

		foreach ($destinationsList as $destination)
		{
			$destination = trim($destination);
			$options[] = HTMLHelper::_('select.option', $destination, $destination);
		}

		if ($this->loadExternally)
		{
			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);
		}

		return $options;
	}

	/**
	 * Method to get a list of options for a list input externally and not from xml.
	 *
	 * @return	array		An array of HTMLHelper options.
	 *
	 * @since   1.0.0
	 */
	public function getOptionsExternally()
	{
		$this->loadExternally = 1;

		return $this->getOptions();
	}
}
