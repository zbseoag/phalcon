<?php
declare(strict_types=1);

namespace Invo\Controllers;

use Invo\Forms\CompaniesForm;
use Invo\Models\Companies;
use Phalcon\Mvc\Model\Criteria;


class CompaniesController extends Controller {

    public function initialize() {

        parent::initialize();

        $this->tag->setTitle('Manage your companies');
    }


    public function index(): void {

        $this->view->form = new CompaniesForm();
    }


    public function search(): void {

//        $paginator = new \Phalcon\Paginator\Adapter\Model(
//            [
//                'model'  => Companies::class,
//                "parameters" => [
//                    "id = :id:",
//                    "bind" => [
//                        "id" => 1
//                    ],
//                    "order" => "id DESC"
//                ],
//                'limit' => 20,
//                'page'  => $this->request->getQuery('page', 'int', 1),
//            ]
//        );


        $query =  $this->request->get();
        $builder = $this->modelsManager->createBuilder()->columns('*')->from(Companies::class)->where('');

        if($query['id'])  $builder->andWhere('id = :id:', [ 'id' => $query['id'] ]);
        if($query['name']) $builder->andWhere('name LIKE :name:', ['name' => '%' . $query['name'] . '%']);

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            "builder" => $builder,
            "limit"   => 2,
            "page"    => $this->request->getQuery('page', 'int', 1),
        ]);

        $paginate = $paginator->paginate();

        //sql($this->di);
        //$this->view->disable();
        $this->view->page = $paginate;

    }


    public function listall(){

        //stop(Companies::find([ 'hydration' => \Phalcon\Mvc\Model\Resultset::HYDRATE_ARRAYS])->toArray());
        $paginator = new \Phalcon\Paginator\Adapter\NativeArray([
                'data'  => Companies::find()->toArray(),
                'limit' => 2,
                'page'  => 1,
        ]);



        $paginate = $paginator->paginate();

        print_r($paginate->getItems());exit;
        $this->view->disable();
    }

    /**
     * Shows the form to create a new company
     */
    public function new(): void {

        $this->view->form = new CompaniesForm(null, ['edit' => true]);
    }

    /**
     * Edits a company based on its id
     *
     * @param int $id
     */
    public function edit($id): void {

        $company = Companies::findFirstById($id);
        if (!$company) {
            $this->flash->error('Company was not found');

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'index',
            ]);

            return;
        }

        $this->view->form = new CompaniesForm($company, ['edit' => true]);
    }

    /**
     * Creates a new company
     */
    public function create(): void {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'index',
            ]);

            return;
        }

        $form = new CompaniesForm();
        $company = new Companies();

        $data = $this->request->getPost();
        if (!$form->isValid($data, $company)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'new',
            ]);

            return;
        }

        if (!$company->save()) {
            foreach ($company->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'new',
            ]);

            return;
        }

        $form->clear();
        $this->flash->success('Company was created successfully');

        $this->dispatcher->forward([
            'controller' => 'companies',
            'action' => 'index',
        ]);
    }

    /**
     * Saves current company in screen
     */
    public function save(): void {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'index',
            ]);

            return;
        }

        $id = $this->request->getPost('id', 'int');
        $company = Companies::findFirstById($id);
        if (!$company) {
            $this->flash->error('Company does not exist');

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'index',
            ]);

            return;
        }

        $data = $this->request->getPost();
        $form = new CompaniesForm();
        if (!$form->isValid($data, $company)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'new',
            ]);

            return;
        }

        if (!$company->save()) {
            foreach ($company->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'new',
            ]);

            return;
        }

        $form->clear();
        $this->flash->success('Company was updated successfully');

        $this->dispatcher->forward([
            'controller' => 'companies',
            'action' => 'index',
        ]);
    }

    /**
     * Deletes a company
     *
     * @param string $id
     */
    public function delete($id) {

        $companies = Companies::findFirstById($id);
        if (!$companies) {
            $this->flash->error('Company was not found');

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'index',
            ]);

            return;
        }

        if (!$companies->delete()) {
            foreach ($companies->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            $this->dispatcher->forward([
                'controller' => 'companies',
                'action' => 'search',
            ]);

            return;
        }

        $this->flash->success('Company was deleted');

        $this->dispatcher->forward([
            'controller' => 'companies',
            'action' => 'index',
        ]);
    }

}
