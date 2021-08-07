<?php
/*
 * Db.php
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

use PDO;
use PDOException;
use Bookstore\Exceptions\NotFoundException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Db{
	private static $instance;
	private static $log_instance;
	
	private static function connect(){
		
		try{
			$dbConfig = Config::getInstance()->get('db');
		}catch(NotFoundException $e){
			$error_instance = self::logErrorInstance();
			$error_instance->critical("Database connection failure: {$e->getMessage()}");

			return false;
		}
		
		try{
			$pdo = new PDO(
				'mysql:host=localhost;dbname=prostate_cancer',
				$dbConfig['user'],
				$dbConfig['password']
			);

			return $pdo;

		}catch(PDOException $e){
			$error_instance = self::logErrorInstance();
			$error_instance->critical("Database connection failure PDO: {$e->getMessage()}");

			return false;
		}
	}

	private static function logErrorInstance(){
		//$log = new Logger('GFCC - '.get_called_class());
		$logFile = Config::getInstance()->get('log');
		self::$log_instance = new Logger('GFCC - '.get_called_class());	
		self::$log_instance->pushHandler(
			new StreamHandler($logFile, Logger::DEBUG)
		);
		return self::$log_instance;	
	}

	public static function getInstance(){
		if (self::$instance == null){
			self::$instance = self::connect();
		}
		return self::$instance;
	}
}
