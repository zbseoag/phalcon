<?php
declare(strict_types=1);

namespace Invo\Plugins;

use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Component;
use Phalcon\Acl\Role;
use Phalcon\Acl\Enum;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;

class SecurityPlugin extends Injectable {

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

        $role = $this->session->get('auth')? 'Users' : 'Guests';

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        $acl = $this->getAcl();
        if (!$acl->isComponent($controller)) {
            $dispatcher->forward(['controller' => 'errors', 'action' => 'show404',]);
            return false;
        }

        $allowed = $acl->isAllowed($role, $controller, $action);
        if (!$allowed) {
            $dispatcher->forward(['controller' => 'errors', 'action' => 'show401',]);
            $this->session->destroy();

            return false;
        }

        return true;
    }

    /**
     * Returns an existing or new access control list
     *
     * @returns AclList
     */
    protected function getAcl(): AclList {


        if (isset($this->persistent->acl)) {
            return $this->persistent->acl;
        }

        $acl = new AclList();
        $acl->setDefaultAction(Enum::DENY);

        // Register roles
        $roles = [
            'users'     => new Role('Users', '特权'),
            'guests'    => new Role('Guests', '客人'
        )];

        //添加角色
        foreach ($roles as $role) $acl->addRole($role);

        $config = $this->di->getShared('config');

        $privateResources = $config->acl->private->toArray();

        foreach ($privateResources as $resource => $actions) {
            $acl->addComponent(new Component($resource), $actions);
        }

        $publicResources = $config->acl->public->toArray();

        foreach ($publicResources as $resource => $actions) {
            $acl->addComponent(new Component($resource), $actions);
        }

        //Grant access to public areas to both users and guests
        foreach ($roles as $role) {
            foreach ($publicResources as $resource => $actions) {
                foreach ($actions as $action) {
                    $acl->allow($role->getName(), $resource, $action);
                }
            }
        }

        //Grant access to private area to role Users
        foreach ($privateResources as $resource => $actions) {
            foreach ($actions as $action) {
                $acl->allow('Users', $resource, $action);
            }
        }

        $this->persistent->acl = $acl;

        return $acl;
    }

}
