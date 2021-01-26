<?php
declare(strict_types=1);

namespace Invo\Controllers;

use Invo\Models\Users;

/**
 * InvoicesController
 *
 * Manage operations for invoices
 */
class InvoicesController extends Controller {

    public function initialize() {

        $this->tag->setTitle('Manage your Invoices');

        parent::initialize();
    }

    public function index(): void {

    }


    public function profile(): void {

        $auth = $this->session->get('auth');

        $user = Users::findFirst($auth['id']);
        if (!$user) {
            $this->dispatcher->forward(['controller' => 'index', 'action' => 'index']);
            return;
        }

        if (!$this->request->isPost()) {
            $this->tag->setDefault('name', $user->name);
            $this->tag->setDefault('email', $user->email);
        } else {
            $user->name = $this->request->getPost('name', ['string', 'striptags']);
            $user->email = $this->request->getPost('email', 'email');

            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $this->flash->success('Your profile information was updated successfully');
            }
        }
    }

}
