<?php
declare(strict_types=1);

namespace Invo\Controllers;

use Invo\Models\Users;
use Invo\Forms\RegisterForm;
use Phalcon\Db\RawValue;

class UserController extends Controller {

    public function initialize() {

        parent::initialize();
        $this->tag->setTitle('注册/登录');
    }

    public function register(): void {

        $form = new RegisterForm();

        if ($this->request->isPost()) {
            $password = $this->request->getPost('password');
            $repeatPassword = $this->request->getPost('repeatPassword');

            if ($password !== $repeatPassword) {
                $this->flash->error('Passwords are different');
                return;
            }

            $user = new Users();
            $user->username = $this->request->getPost('username', 'alphanum');
            $user->password = sha1($password);
            $user->name = $this->request->getPost('name', ['string', 'striptags']);
            $user->email = $this->request->getPost('email', 'email');
            $user->created_at = new RawValue('now()');
            $user->active = 'Y';

            if (!$user->save()) {

                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }

            } else {

                $this->tag->setDefault('email', '');
                $this->tag->setDefault('password', '');

                $this->flash->success('Thanks for sign-up, please log-in to start generating invoices');
                $this->dispatcher->forward('user/login');
                return;
            }
        }

        $this->view->form = $form;
    }


    public function login(): void {

        if ($this->request->isPost()) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            /** @var Users $user */
            $user = Users::findFirst([
                "(email = :email: OR username = :email:) AND password = :password: AND active = 'Y'",
                'bind' => ['email' => $email, 'password' => sha1($password),],
            ]);

            if ($user) {
                $this->registerSession($user);
                $this->flash->success('Welcome ' . $user->name);

                $this->dispatcher->forward(['controller' => 'invoices', 'action' => 'index',]);
                return;
            }

            $this->flash->error('Wrong email/password');

        } else {

            $this->tag->setDefault('email', '');
            $this->tag->setDefault('password', '');
        }


    }


    public function logout(): void {

        $this->session->remove('auth');
        $this->flash->success('Goodbye!');
        $this->dispatcher->forward(['controller' => 'index', 'action' => 'index',]);
    }

    /**
     * Register an authenticated user into session data
     * @param Users $user
     */
    private function registerSession(Users $user): void {

        $this->session->set('auth', ['id' => $user->id, 'name' => $user->name,]);
    }

}
