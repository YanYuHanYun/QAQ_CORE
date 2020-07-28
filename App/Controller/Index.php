<?php

namespace App\Controller;

use QAQ\Kernel\App;
use QAQ\Kernel\Db;

class Index extends App
{
    public function index($title = 'Hello QAQ_CORE！')
    {
        $info = [
            'title' => $title
        ];
        return view('index', $info);
    }
}