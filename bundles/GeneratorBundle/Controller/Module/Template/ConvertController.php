<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Controller\Module\Template;

use GeneratorBundle\Action;
use GeneratorBundle\Controller\Module\AbstractModuleController;

/**
 * Class ConvertController
 *
 * @since 1.0
 */
class ConvertController extends AbstractModuleController
{
	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @since   1.0
	 * @throws  \LogicException
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		// Flip src and dest because we want to convert template.
		$dest = $this->config->get('dir.dest');
		$src  = $this->config->get('dir.src');

		$this->config->set('dir.dest', $src);
		$this->config->set('dir.src',  $dest);

		$this->doAction(new Action\ConvertTemplateAction);
		$this->doAction(new Action\Module\ReplaceXmlClientAction);

		return true;
	}
}
