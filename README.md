# 从0开始写laravel-ORM

## 需求设计 - 1

1. 不需要手写sql语句，通过对象操作数据库


## 用法

设有一个开发者定义了User类，通过User::find(１)可以找到id为１的行


## 实现思路

根据持久化对象中的属性，利用　Query　类构造出对应的sql语句


在 system/db 目录下新建抽象类eloquent.php

	<?php

	namespace System\DB;

	abstract class Eloquent
	{
	    public $query;

	    public static function find($id)
	    {

	    }
	}


在 application 目录下创建目录　models ，再在　models　下新建文件　user.php

	<?php

	class User extends \System\DB\Eloquent
	{
	    public static $table = 'user';
	}


User 类继承　Eloquent, 　并加入静态属性　$table ,表示这个model对应的数据库表是user

在 system/loaderphp 里加入 models 的自动加载

	elseif (file_exists($path = APP_PATH.'models/'.$file.EXT))
    {
        require $path;
    }

在 system/db 下创建　eloquent 目录，再在　eloquent 目录下新建　factory.php, 用于实例化　User 类并构建其查询属性

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

在 system/db/eloquent 目录下创建 meta.php ,用于根据　User 中的属性，获取其对应的数据库表格

	<?php

	namespace System\DB\Eloquent;

	class Meta
	{
	    public static function table($class)
	    {
	        return $class::$table;
	    }
	}

在　system/db/eloquent 类中通过　get_called_class()　方法获取其调用的类名，再通过　Factory 来获取　Query 属性


	public $query;

    public static function find($id)
    {
        return Eloquent\Factory::make(get_called_class());
    }

根据上一篇学到的知识，可以直接利用　

	return Eloquent\Factory::make(get_called_class())->query()->where('id', '=', $id)->get();

来进行查询

为了简化写法，重写　__call()

	public function __call($method, $parameters)
    {
        call_user_func_array(array($this->query, $method), $parameters) ;
        return $this;
    }

所以最终，Eloquent 类变成：

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




整个框架项目结构形如：

* application
	* |- config
		* |-applicationi.php
		* |-db.php
		* |-error.php
		* |-session.php
	* |- views
		* |-home
			* index.php
	* |- routes.php
* public
	* |- index.php
* system
	* |-db
		* |- eloquent
			* |- factory.php
			* |- meta.php
		* |- query
			* |-compiler.php
		* |- connector.php
		* |- eloquent.php
		* |- query.php
	* |- session
		* |- driver
			* |- file.php
		* |- driver.php
		* |- factory.php
	* |- config.php
	* |- cookie.php
	* |- db.php
	* |- error.php
	* |- loader.php
	* |- request.php
	* |- response.php
	* |- route.php
	* |- session.php
	* |- str.php
	* |- router.php
	* |- view.php