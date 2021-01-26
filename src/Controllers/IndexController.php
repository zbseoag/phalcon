<?php
declare(strict_types=1);

namespace Invo\Controllers;

class IndexController extends Controller {

    public function initialize(){

        parent::initialize();
        $this->tag->setTitle('Welcome');
    }

    public function index(): void{

        $this->flash->notice('This is a sample application of the Phalcon Framework.');
        //$this->view->disable();

    }


}
