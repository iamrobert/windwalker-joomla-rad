<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use {{extension.name.cap}}\Router\Route;

defined('_JEXEC') or die;

JHtmlBootstrap::tooltip();

/**
 * Prepare data for this template.
 *
 * @var $container \Windwalker\DI\Container
 * @var $data      \Windwalker\Data\Data
 * @var $state     \Joomla\Registry\Registry
 * @var $user      \JUser
 * @var $this      \Windwalker\View\Engine\PhpEngine
 */
$container = $this->getContainer();
$data      = $this->data;
$state     = $data->state;
$user      = $container->get('user');
?>
<form action="<?php echo Route::view('{{controller.list.name.lower}}'); ?>" method="post" name="adminForm" id="adminForm">

	<div id="{{extension.name.lower}}-wrap" class="windwalker list container-fluid {{controller.list.name.lower}}">
		<div id="{{extension.name.lower}}-wrap-inner">

            <!-- {{controller.list.name.cap}} List -->
            <div id="{{controller.list.name.lower}}-wrap">

                <!--Columns-->
                <?php if (!empty($data->items)): ?>

                    <?php foreach ((array) $data->items as $key => &$item): ?>
                        <div class="item">
                            <?php echo $this->loadTemplate('item', array('item' => $item)); ?>
                        </div>

                        <span class="row-separator"></span>
                        <!-- LINE END -->
                    <?php endforeach; ?>

                <?php endif; ?>
                <!--Columns End-->

                <!--Pagination-->
                <div class="pagination">
                    <?php echo $data->pagination->getPagesLinks(); ?>
                </div>
                <!--Pagination End-->
            </div>
        </div>


        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="return" id="return_url" value="<?php echo base64_encode(JUri::getInstance()->toString()); ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>

</form>
