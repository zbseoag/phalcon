<?php
/**
 * User: admin
 * Date: 2021/1/26
 * Email: <zbseoag@163.com>
 */

namespace Invo\Models;
use Phalcon\Mvc\Collection;

class Robots extends Collection {

    public function initialize() {

        //表名
        $this->setSource('the_robots');
    }


    public static function test(){

        $robot = Robots::findById('5087358f2d42b8c3d15ec4e2');
        Robots::findFirst([
            ['name' => 'Astro Boy',]
        ]);


    }

}