<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\HTML\HTMLHelper;

// Set some global property
$document = Factory::getDocument();

// Access check: is this user allowed to access the backend of this component?
if (!Factory::getUser()->authorise('core.manage', 'com_tms'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Get an instance of the controller prefixed by HelloWorld
$controller = BaseController::getInstance('Tms');

// Load component CSS file
HTMLHelper::stylesheet(Juri::root() . 'media/com_tms/css/tms.css');

// Perform the Request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
