<?php
declare(strict_types=1);

namespace Invo\Controllers;

use Invo\Forms\ProductsForm;
use Invo\Models\Products;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;

class ProductsController extends Controller {

    public function initialize() {

        parent::initialize();
        $this->tag->setTitle('Manage your products');
    }

    /**
     * Shows the index action
     */
    public function index(): void {

        $this->view->form = new ProductsForm;
    }

    /**
     * Search products based on current criteria
     */
    public function search(): void {

        $query = Criteria::fromInput($this->di, Products::class, $this->request->get());
        $products = Products::find($query->getParams());

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $products,
            'limit' => 10,
            'page' => $this->request->getQuery('page', 'int', 1),
        ]);
        $this->view->page = $paginator->paginate();
    }

    /**
     * Shows the form to create a new product
     */
    public function new(): void {

        $this->view->form = new ProductsForm(null, ['edit' => true]);
    }

    /**
     * Edits a product based on its id
     *
     * @param $id
     */
    public function edit($id): void {

        $product = Products::findFirstById($id);
        if (!$product) {
            $this->flash->error('Product was not found');
            $this->dispatcher->forward(['controller' => 'products', 'action' => 'index',]);
            return;
        }

        $this->view->form = new ProductsForm($product, ['edit' => true]);
    }

    /**
     * Creates a new product
     */
    public function create(): void {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward(['controller' => 'products', 'action' => 'index',]);
            return;
        }

        $form = new ProductsForm();
        $product = new Products();

        if (!$form->isValid($this->request->getPost(), $product)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward(['controller' => 'products', 'action' => 'new',]);

            return;
        }

        if (!$product->save()) {
            foreach ($product->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            $this->dispatcher->forward(['controller' => 'products', 'action' => 'new',]);

            return;
        }

        $form->clear();
        $this->flash->success('Product was created successfully');

        $this->dispatcher->forward(['controller' => 'products', 'action' => 'index',]);
    }

    /**
     * Saves current product in screen
     */
    public function save(): void {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward(['controller' => 'products', 'action' => 'index']);
            return;
        }

        $id = $this->request->getPost('id', 'int');
        $product = Products::findFirstById($id);
        if (!$product) {
            $this->flash->error('Product does not exist');

            $this->dispatcher->forward(['controller' => 'products', 'action' => 'index']);

            return;
        }

        $form = new ProductsForm();
        $this->view->form = $form;
        $data = $this->request->getPost();

        if (!$form->isValid($data, $product)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => 'products',
                'action' => 'edit',
                'params' => [$id],
            ]);

            return;
        }

        if (!$product->save()) {
            foreach ($product->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => 'products',
                'action' => 'edit',
                'params' => [$id],
            ]);

            return;
        }

        $form->clear();
        $this->flash->success('Product was updated successfully');

        $this->dispatcher->forward([
            'controller' => 'products',
            'action' => 'index',
        ]);
    }

    /**
     * Deletes a product
     *
     * @param string $id
     */
    public function delete($id): void {

        $products = Products::findFirstById($id);
        if (!$products) {
            $this->flash->error('Product was not found');

            $this->dispatcher->forward([
                'controller' => 'products',
                'action' => 'index',
            ]);

            return;
        }

        if (!$products->delete()) {
            foreach ($products->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward(['controller' => 'products', 'action' => 'search']);
            return;
        }

        $this->flash->success('Product was deleted');
        $this->dispatcher->forward(['controller' => 'products', 'action' => 'index']);
    }

}
