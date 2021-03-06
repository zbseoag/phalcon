<?php
declare(strict_types=1);

namespace Invo\Models;

use Phalcon\Mvc\Model;

class Products extends Model {

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $product_types_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $price;

    /**
     * @var string
     */
    public $active;

    /**
     * Products initializer
     */
    public function initialize() {

        $this->belongsTo(
            'product_types_id',
            ProductTypes::class,
            'id',
            [
                'reusable' => true,
            ]
        );
    }

    /**
     * Returns a human representation of 'active'
     *
     * @return string
     */
    public function getActiveDetail(): string {

        return $this->active == 'Y' ? 'Yes' : 'No';
    }

}
