<?php
/*
 * Request.php
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
session_start();
use Bookstore\Core\FilteredMap;
class Request{
	const GET = 'GET';
	const POST = 'POST';
	
	private $domain;
	private $path;
	private $method;
	
	private $params;
	private $cookies;
	private $session;
	
	public function __construct(){
		$this->domain = $_SERVER['HTTP_HOST'];
		$this->path = $_SERVER['REQUEST_URI'];
		$this->method = $_SERVER['REQUEST_METHOD'];
		
		$this->params = new FilteredMap(
					array_merge($_POST, $_GET)
		);
		$this->cookies = new FilteredMap($_COOKIE);
		$this->sessions = new FilteredMap($_SESSION);
	}
	
	public function getUrl(): string{
		return $this->domain. $this->path;
	}
	
	public function getDomain(): string{
		return $this->domain;
	}
	
	public function getPath(): string{
		return $this->path;
	}
	
	public function getMethod(): string{
		return $this->method;
	}
	
	public function isPost(): bool{
		return $this->method === self::POST;
	}
	
	public function isGet(): bool{
		return $this->method === self::GET;
	}
	
	public function getParams(): FilteredMap{
		return $this->params;
	}
	
	public function cookies(): FilteredMap{
		return $this->cookies;
	}
	
	public function sessions(){
		return $this->sessions;
	}
}