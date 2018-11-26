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
use Joomla\CMS\Language\Text;

/**
 * TMS Freight Controller
 *
 * @since  1.0.0
 */
class TmsControllerFreight extends FormController
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

	public function save($key = null, $urlVar = null)
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$data  = $input->get('freightdata', array(), "ARRAY");

		$result = $this->validate($data);

		if ($result === true)
		{
			$model = $this->getModel();
			$model->save($data);
		}
		else
		{
			$app->enqueueMessage(Text::_("COM_TMS_MANAGE_FREIGHT_INVALID_DATA"), 'error');
		}

		// Redirect back to the edit screen.
		$this->setRedirect('index.php?option=com_tms&view=freight');
	}

	public function validate($data)
	{
		$app = Factory::getApplication();

		foreach ($data as $destination => $freight)
		{
			foreach($freight as $freightItem)
			{
				// Check if empty entry for box weight
				if (empty($freightItem['box_weight']))
				{
					$app->enqueueMessage(Text::sprintf("COM_TMS_MANAGE_FREIGHT_DUPLICATE_BOX_WEIGHT_ERROR", $freightItem['box_weight'], $destination), 'error');

					return false;
				}

				// Check if empty entry for freight
				if (empty($freightItem['freight']))
				{
					$app->enqueueMessage(Text::sprintf("COM_TMS_MANAGE_FREIGHT_EMPTY_FREIGHT_ERROR", $destination), 'error');

					return false;
				}

				// Check if empty entry for inam
				if (empty($freightItem['inam']))
				{
					$app->enqueueMessage(Text::sprintf("COM_TMS_MANAGE_FREIGHT_EMPTY_INAM_ERROR", $destination), 'error');

					return false;
				}
			}

			// Check if duplicate entry for box weight for given destination
			for ($i = 0; $i < count($freight); $i++)
			{
				for ($j = $i+1; $j < count($freight); $j++)
				{
					if ($freight[$i]['box_weight'] == $freight[$j]['box_weight'])
					{
						$app->enqueueMessage(Text::sprintf("COM_TMS_MANAGE_FREIGHT_DUPLICATE_BOX_WEIGHT_ERROR", $freight[$i]['box_weight'], $destination), 'error');

						return false;
					}
				}
			}
		}

		return true;
	}

	public function cancel($key = null)
	{
		// Redirect back to the edit screen.
		$this->setRedirect('index.php?option=com_tms');
	}
}
