<?php
/*
 * Router.php
 * 
 * Copyright 2020 jossy <jossy@DESKTOP-7C418G4>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

namespace Bookstore\Core;

use Bookstore\Controllers\ErrorController;
use Bookstore\Controllers\AuthController;
use Bookstore\Core\Db;

class Router{
	private $routeMap;
	private static $regexPatterns = [
			'number' => '\d+',
			'string' => '\w'
	];
	private $getpath;
	
	public function __construct(){
		$json = file_get_contents(__DIR__ . '/config/routes.json');
		$this->routeMap = json_decode($json, true);
	}
	
	public function route(Request $request):string{
		$path = $request->getPath();
		$this->getpath = $request->getPath();;
		foreach ($this->routeMap as $route => $info){
			$regexRoute = $this->getRegexRoute($route, $info);
			if (preg_match("@^/$regexRoute$@", $path)) {
				return $this->executeController(
					$route, $path, $info, $request
				);
			}
		}
		
		$errorController = new ErrorController($request);
		return $errorController->notFound();
	}
	
	private function getRegexRoute(string $route, array $info): string{
		if(isset($info['params'])){
			foreach ($info['params'] as $name => $type){
				$route = str_replace(
					':'.$name, self::$regexPatterns[$type], $route
				);
			}
		}
		return $route;
	}
	
	private function extractParams(string $route, string $path):array{
		$params = [];
		
		$pathParts = explode('/', $path);
		$routeParts = explode('/', $route);
		
		foreach ($routeParts as $key => $routePart){
			if(strpos($routePart, ':') === 0) {
				$name = substr($routePart,1);
				$params['name'] = $pathParts[$key + 1];
			}
		}
		return $params;
	}
	
	private function executeController(
		string $route,
		string $path,
		array $info,
		Request $request
	):string{
		//Confirm that database connection is properly connected
		$db = Db::getInstance();
		if(!$db){
			$errorController = new ErrorController($request);
			return $errorController->dbConnectError();
		}

		//Load the requested route
		$controllerName = '\Bookstore\Controllers\\'
			. $info['controller'] . 'Controller';
		$controller = new $controllerName($request);
		
		
		//Check if route is protected and requires login
		if(isset($info['login']) && $info['login']){
			$auth = new AuthController($request);
			if(!$auth->isUserActive()){
				return $auth->loginForm();
			}					
		}
		
		
		$params = $this->extractParams($route, $path);
		
		return call_user_func_array(
			[$controller, $info['method']], $params
		);	
	}
	
	
}
