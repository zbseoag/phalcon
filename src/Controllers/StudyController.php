<?php
declare(strict_types=1);

namespace Invo\Controllers;
use Invo\Forms\RegisterForm;

class StudyController extends ControllerBase {

    public function initialize(){

        parent::initialize();
        $this->tag->setTitle('查看对象');
    }

    public function index(): void{

        $this->dispatcher->forward('index/index');


    }

    public function register() {

        $user = new Users();
        $success = $user->save($this->request->getPost(), ["name", "email",]);

        if ($success) {
            echo "Thanks for registering!";
        } else {
            echo "Sorry, the following problems were generated: ";
            $messages = $user->getMessages();

            foreach ($messages as $message) {
                echo $message->getMessage(), "<br/>";
            }
        }
        $this->view->disable();
    }

    /**
     * 通过Phalcon运行的兼容PSR-4的文件加载器
     */
    public function loader(): void{

        define('BASE_PATH', dirname(__DIR__));
        define('APP_PATH', BASE_PATH . '/app');

        $loader = new \Phalcon\Loader();

        $loader->registerDirs([
            APP_PATH . '/controllers/',
            APP_PATH . '/models/',
        ]);
        $loader->register();

    }


    public function component(): void{
        try {

            $di = new \Phalcon\Di\FactoryDefault();
            $di->set('view2', function () {
                $view = new \Phalcon\Mvc\View();
                $view->setViewsDir(APP_PATH . '/views/');
                return $view;
            });

            $di->set('url2', function () {
                $url = new \Phalcon\Mvc\Url();
                $url->setBaseUri('/');
                return $url;
            });

            $application = new \Phalcon\Mvc\Application($di);
            $response = $application->handle($_SERVER['REQUEST_URI']);
            $response->send();

        } catch (\Exception $e) {
            echo $e->getMessage() . '<br>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }

    }

    public function sql(): void{

        $profiles = $this->di->getProfiler()->getProfiles();

        if($profiles) foreach ($profiles as $profile) {
            echo "SQL语句: ", $profile->getSQLStatement(), "\n";
            echo "开始时间: ", $profile->getInitialTime(), "\n";
            echo "结束时间: ", $profile->getFinalTime(), "\n";
            echo "消耗时间: ", $profile->getTotalElapsedSeconds(), "\n";
        }

        if($profiles) echo $this->di->getProfiler->getLastProfile()->getSQLStatement();
        $this->view->disable();

    }


}
