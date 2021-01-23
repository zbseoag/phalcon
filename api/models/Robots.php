<?php
/**
 * User: admin
 * Date: 2021/1/23
 * Email: <zbseoag@163.com>
 */

namespace Store\Toys;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class Robots extends Model {

    public function validation() {

        $validator = new Validation();

        $validator->add(
            'type',
            new InclusionIn(
                [
                    'domain' => [
                        'Mechanical',
                        'Virtual',
                        'Droid',
                    ]
                ]
            )
        );

        $validator->add(
            'name',
            new Uniqueness(
                [
                    'message' => 'The robot name must be unique',
                ]
            )
        );

        return $this->validate($validator);
    }

}