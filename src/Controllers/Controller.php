<?php
declare(strict_types=1);

namespace Invo\Controllers;


class Controller extends \Phalcon\Mvc\Controller {

    protected function initialize() {

        $this->tag->prependTitle('INVO | ');
        $this->view->setTemplateAfter('main');
    }

}
