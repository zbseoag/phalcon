<?php
declare(strict_types=1);

namespace Invo\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class Study extends Model {

    public $id;
    public $name;
    public $price;

    const DELETED     = 1;
    const NOT_DELETED = 0;

    public function initialize_demo() {

        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\Timestampable([
            'beforeCreate' => [
                'field'  => 'created_at',
                'format' => 'Y-m-d',
            ]
        ]));

        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\Timestampable([
            'beforeCreate' => [
                'field'  => 'created_at',
                'format' => function(){

                    $datetime = new Datetime(
                        new DateTimeZone('Europe/Stockholm')
                    );
                    return $datetime->format('Y-m-d H:i:sP');
                },
            ]
        ]));

        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete([
            'field' => 'status',
            'value' => Users::DELETED,
        ]));


    }

    public function onConstruct() {

    }



    public function beforeCreate_demo() {

        if ($this->age > 1000) {
            $this->appendMessage(new Message('年龄不能超过 1000'));
            return false;
        }
        $manager = $this->getDI()->get('db');

        #在action 中
        $phql = "INSERT INTO Users VALUES (NULL, 'Nissan Versa', 1000, 'Sedan')";
        $result = $manager->executeQuery($phql);
        if ($result->success() === false) {
            foreach ($result->getMessages() as $message) {
                echo $message->getMessage();
            }
        }

    }

    public static function findByCreateInterval() {

        // 原始SQL语句
        $sql = 'SELECT * FROM robots WHERE id > 0';

        // 基础模型
        $robot = new Robots();

        // 执行查询
        return new \Phalcon\Mvc\Model\Resultset\Simple(
            null,
            $robot,
            $robot->getReadConnection()->query($sql)
        );
    }

    public static function findByRawSql($conditions, $params = null) {
        // 原始SQL语句
        $sql = 'SELECT * FROM robots WHERE $conditions';

        // 基础模型
        $robot = new Robots();

        // 执行查询
        return new \Phalcon\Mvc\Model\Resultset\Simple(
            null,
            $robot,
            $robot->getReadConnection()->query($sql, $params)
        );

        //查询
        //$robots = Robots::findByRawSql('id > ?', [10]);
        //$robots = Robots::query()
        //    ->where('type = :type:')
        //    ->andWhere('year < 2000')
        //    ->bind(['type' => 'mechanical'])
        //    ->order('name')
        //    ->execute();
    }


}
