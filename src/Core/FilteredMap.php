<?php
/*
 * FilteredMap.php
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

class FilteredMap {
	private $map;
	
	public function __construct(array $baseMap){
		$this->map = $baseMap;
	}
	
	public function getAll():array{
		return $this->map;
	}

	public function has(string $name):bool{
		return isset($this->map[$name]);
	}

	public function whenHas(string $name, $function){
		return isset($this->map[$name])? $function() : null;
	}
	
	public function get(string $name){
		return $this->map[$name]?? null;
	}
	
	public function getInt(string $name){
		return (int) $this->get($name);
	}
	
	public function getNumber(string $name){
		return (float) $this->get($name);
	}
	
	public function getFilteredString(string $name, bool $filter = true){
		//@refactor Add more strict filter
		$value = (string) trim($this->get($name));
		return $filter ? addslashes($value) : $value;
	}

	public function setCookie(String $key, String $value, string $cookie_name, string $app_cookie_name): bool{
		$cookie_structure = array(
			$app_cookie_name => [$cookie_name =>[$key=>$value]]//$cookie_name[$key] = $value
		);
		
		$key_ = array_keys($cookie_structure)[0];
		$value_ = $cookie_structure[array_keys($cookie_structure)[0]];

		setcookie($key_, json_encode($value_));
		
		return true;
	}
	public function deleteCookie($app_cookie_name, $key){
		$gfcc_cookie = (array) $this->getCookie($app_cookie_name);
		if(array_key_exists($key, $gfcc_cookie)){
			unset($gfcc_cookie[$key]);
		}
		setcookie($app_cookie_name, json_encode($gfcc_cookie));
	}
	public function addCookie($app_cookie_name, $key, $value){
		$gfcc_cookie = (array) $this->getCookie($app_cookie_name);
		//$gfcc_cookie['user_id'] = ['username'=>'joe', 'password'=>'joe'];
		
		$key_ = $app_cookie_name;

		if(!$gfcc_cookie){
			
			$value_ = [$key => $value];

			setcookie($key_, json_encode($value_));
			return;
		}
		
		$new_key = array(
			$key=> $value
		);
		$new_cookie_structure = array_merge($gfcc_cookie, $new_key);

		
		$value_ = $new_cookie_structure;

		setcookie($key_, json_encode($value_));
	}

	public function getCookie($name){
		return $this->has($name)?$this->getEncodedString($name):false;
	}
	private function getEncodedString(string $name){
		return json_decode($this->get($name));
	}

	public function filled(string $name){
		return !empty($this->get($name))? true: false;
	}

	public function setSession(string $name, array $form_post){
		$this->map[$name] = $form_post;
	}

	public function getSession(string $session_name){
		return isset($_SESSION[$session_name])? $_SESSION[$session_name]:null;
	}

	public function setBaseSession(string $session_name, array $session_data){
		$this->map[$name] = $session_data;

		if(!isset($_SESSION[$session_name])){
			$_SESSION[$session_name] = $session_data;
			//array_merge($_SESSION,$_SESSION[$session_name] );
		}
	}


	public function sessionDestroy(){
		session_destroy();
	}
}