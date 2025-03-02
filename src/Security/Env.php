<?php

namespace App\Security;

class Env
{
    /**
    *
    * look for the .env file and store data in memory
    *
    */	
	public static function loadEnv($path = __DIR__.'/../../.env') {
	    if (!file_exists($path)) {
	        throw new \InvalidArgumentException("Environment file not found");
	    }
	    $env = parse_ini_file($path);
	    foreach ($env as $key => $value) {
	        putenv("$key=$value");
	    }
	}
}

