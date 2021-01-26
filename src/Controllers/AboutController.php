<?php
declare(strict_types=1);

namespace Invo\Controllers;

class AboutController extends Controller
{
    public function initialize()
    {
        parent::initialize();

        $this->tag->setTitle('About us');
    }

    public function index(): void
    {
    }
}
