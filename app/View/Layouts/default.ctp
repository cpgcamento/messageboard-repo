<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version());
App::uses('Controller', 'Controller/Users');

?>
<!DOCTYPE html>
<html>

<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        MessageBoard
    </title>
    <?php
		echo $this->Html->meta('icon');

		echo $this->Html->css(array('app','bootstrap.min','toastr.min','jquery-ui','select2.min'));

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->Html->script(array('jquery','toastr.min','app','jquery-ui','bootstrap.min','select2.min'));
		echo $this->fetch('script');
	?>
</head>

<body>
    <div class="container-fluid">
        <?php if ($this->Session->check('Auth.User')): ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
            <div class="collapse navbar-collapse">
                <a class="navbar-brand"
                    href="<?php echo $this->Html->url(array('controller'=>'users','action' => 'index')) ?>">Message
                    Board</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="navbar-nav text-right">
                <a class="nav-link"
                    href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'index')) ?>">Home
                    <span class="sr-only">(current)</span></a>
                <a class="nav-link"
                    href="<?php echo $this->Html->url(array('controller' => 'messages', 'action' => 'index')) ?>">Messages</a>
                <a class="nav-link"
                    href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'logout')) ?>">Logout</a>
            </div>
        </nav>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <?php echo $this->Flash->render(); ?>

                <?php echo $this->fetch('content'); ?>
            </div>
        </div>
    </div>
</body>

</html>