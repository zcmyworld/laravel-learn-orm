<?php

namespace System\DB\Eloquent;

class Meta
{
    public static function table($class)
    {
        return $class::$table;
    }
}
