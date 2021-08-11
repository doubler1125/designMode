<?php

use Gaia\Helpers\Error\LastError;
use Helpers\SingletonHelper;

abstract class BaseService
{
    use SingletonHelper;
    use LastError;

    public function __construct()
    {
    }

}