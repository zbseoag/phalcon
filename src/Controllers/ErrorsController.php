<?php
declare(strict_types=1);

namespace Invo\Controllers;

/**
 * ErrorsController
 *
 * Manage errors
 */
class ErrorsController extends Controller {

    public function initialize() {

        $this->tag->setTitle('Oops!');

        parent::initialize();
    }

    public function show404(): void {

        $this->response->setStatusCode(404);
    }

    public function show401(): void {

        $this->response->setStatusCode(401);
    }

    public function show500(): void {

        $this->response->setStatusCode(500);
    }

}
