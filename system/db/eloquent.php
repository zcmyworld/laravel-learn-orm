<?php

namespace System\DB;

abstract class Eloquent
{
    public $query;
    
    public static function find($id)
    {
        return Eloquent\Factory::make(get_called_class())->where('id', '=', $id)->get();
        return Eloquent\Factory::make(get_called_class())->query()->where('id', '=', $id)->get();
    }

    public function __call($method, $parameters)
    {
        call_user_func_array(array($this->query, $method), $parameters) ;

        return $this;
    }

}
