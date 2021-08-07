<?php
/*
 * Config.php
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

use Bookstore\Exceptions\NotFoundException;

class Config{
	private $data;
	private static $instance;
	private $json;
	
	private function __construct(){
		$this->json = file_get_contents(__DIR__ . '/config/app.json');
		$this->data = json_decode($this->json, true);
	}

	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new Config();
		}
		return self::$instance;
	}

	public function get($key){

		if(!isset($this->data[$key])){
			throw new NotFoundException("Key $key not in config.");
		}
		
		return $this->data[$key];
	}
}
