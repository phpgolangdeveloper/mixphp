<?php

namespace App\Http\Service;

class TestService
{
    public static function index()
    {

        for ($i=0;$i<=10;$i++) {
            echo $i;
            sleep(1);
        }

    }
}
