<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList();
		
		# AdminModule routes
    	$router[] = new Route('admin1896/<presenter>/<action>[/<id>]', array(
            'module'    => 'Admin',
            'presenter' => 'Menu',
            'action'    => 'menu',
            'id'        => null
    	));	
    	
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
