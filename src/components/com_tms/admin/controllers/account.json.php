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
 * TMS Account Controller
 *
 * @since  1.0.0
 */
class TmsControllerAccount extends FormController
{
	/**
	 * Implement to allow edit or not
	 *
	 * @return bool(
	 */
	public function popupSave()
	{
		$return = array();
		$jInput = Factory::getApplication()->input->post;
		$model = $this->getModel();

		$data = $jInput->get('jform', array(), 'ARRAY');
		$status = $model->save($data);
		$accountId = (int) $model->getState($model->getName() . '.id');

		if (!empty($accountId))
		{
			$return['id'] = $accountId;
			$return['title'] = $data['title'];
		}

		echo new JResponseJson($return);
	}
}
