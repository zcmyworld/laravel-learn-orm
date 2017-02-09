<?php

namespace System\DB\Eloquent;

class Factory
{
    public static function make($class)
    {
        $model = new $class;
        
        $model->query = \System\DB\Query::table(Meta::table($class));
        
        return $model;
        
    }
}