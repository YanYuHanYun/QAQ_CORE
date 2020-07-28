<?php

namespace App\Controller;

use QAQ\Kernel\Controller;

class Index extends Controller
{
    public function index($title = 'Hello QAQ_CORE！')
    {
        $info = [
            'title' => $title
        ];
        return view('index', $info);
    }
}