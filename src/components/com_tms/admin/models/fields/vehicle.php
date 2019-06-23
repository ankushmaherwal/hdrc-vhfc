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
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of accounts
 *
 * @since  1.0.0
 */
class JFormFieldVehicle extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0.0
	 */
	protected $type = 'Vehicle';

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
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($db->quoteName(array('v.id', 'v.registration_number')));
		$query->from($db->quoteName('#__transport_vehicle', 'v'));
		$query->where($db->quoteName('v.published') . '=1');
		$query->order($db->quoteName('v.registration_number') . ' ASC');
		$db->setQuery($query);

		$vehicles = $db->loadObjectList();

		$options = array();

		foreach ($vehicles as $vehicle)
		{
			$options[] = HTMLHelper::_('select.option', $vehicle->id, $vehicle->registration_number);
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

	/**
	 * Method to get a field input
	 *
	 * @return	HTML
	 *
	 * @since   1.0.0
	 */
	public function getInput()
	{
		// Add class to the element to distinguish the element
		$this->class = "tms-vehicle";

		$html = parent::getInput();

		// Add link to add account 
		$html .= "<a href='#' onclick='tms.manageVehicle.openVehicleForm();'>" . Text::_("JGLOBAL_FIELD_ADD") . "</a>";

		return $html;
	}
}
