<?php
/*
 * AbstractController.php
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

namespace Bookstore\Controllers;

use Bookstore\Core\Request;
use Bookstore\Core\Db;
use Bookstore\Core\Config;
use Monolog\Logger;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Monolog\Handler\StreamHandler;


abstract class AbstractController{
	protected $request;
	protected $db;
	protected $config;
	protected $view;
	protected $log;

	protected $properties;
	const APP_COOKIE_NAME = 'prostate';
	
	public function __construct(Request $request){
		//define("APP_COOKIE_NAME", 'gfcc');
		
		$this->request = $request;
		$this->db = Db::getInstance();
		$this->config = Config::getInstance();
		
		$loader = new \Twig\Loader\FilesystemLoader(
			__DIR__ . '/../../views'
		);
		$this->view  = new \Twig\Environment($loader);
		
		//Instantiate Logger for logging errors to file
		$this->log = new Logger('GFCC - '.get_called_class());
		$logFile = $this->config->get('log');
		$this->log->pushHandler(
			new StreamHandler($logFile, Logger::DEBUG)
		);

		//More View data can be added to this property
		$this->properties = ['privileges' => '1'];
	}
	
	
	//Used when using Twig_Loader_Filesystem instance
	protected function render(string $template, array $params): string{
		return $this->view->loadTemplate($template)->render($params);
	}
	
	//Used when using \Twig\Loader\FilesystemLoader instance
	protected function rend(string $template, array $params): string{
		return $this->view->render($template, $params);
	}

	protected function buildViewData($request, Array $errors){
        $post_keys = $request->getAll();
        $view_data = array();
        
        foreach($post_keys as $key => $value){
            if($key !== "_token" && !is_int($key)){
                $field_name = $key; $field_value=[];
                
				/* Posted data are returned back as is in case of form errors

					@var index -name contains the value as is from the user_error
					@var index - error contains messages retrieved from validation
					@var index - value contains the value after it has been fitered
					& valaidated. This is data that could be persistent.
				 */
                if(array_key_exists($key, $errors)){
                   	$field_value['name'] =  $value;
                   	$field_value['error']= $errors[$key];
                }else{
					$field_value['name'] =  $value;

					//Ensure that only filtered value is stored
                   	$field_value['value'] =  $request->getFilteredString($key);
                }
                array_push($view_data, $view_data[$field_name] = (object) $field_value);
            }
            
        }
		
		//Push accumulated errors into views if any
		$build_errors = $this->buildErrorMessage($errors, 3);
		if($build_errors && gettype($build_errors) == 'string'){
			array_push($view_data, $view_data['error_message']= $build_errors);
		}
        
        return $view_data;
    }

	protected function formatErrorKeys(array $errors):array{
		$error_keys = [];
		$errors = array_keys($errors);

		foreach($errors as $key){
			if(!is_int($key)){
				$key = ucwords(str_replace('_',' ', $key));
				array_push($error_keys, $key);
			}	
		}

		return $error_keys;
	}

	protected function buildErrorMessage(array $errors, int $displayed_error_fields_count){
		if(empty($errors))
			return false;

		//$error_keys = array_keys($errors);
		$error_keys = $this->formatErrorKeys($errors);
		$error_count = count($error_keys);
		$remaining_error_fields = $error_count - $displayed_error_fields_count;

		if( $error_count > $displayed_error_fields_count){
			$displayed_error_fields = implode(', ',array_slice($error_keys, 0, $displayed_error_fields_count)) ;
			$displayed_error_message = "$displayed_error_fields and $remaining_error_fields other field(s) needs to be verified.";
		}else{
			$displayed_error_fields = implode(', ',array_slice($error_keys,0, $error_count));
			$displayed_error_message = "$displayed_error_fields needs to be verified.";
		}
			
		return $displayed_error_message;
	}

	protected function errorMessage($message, $error_page, array $props=[], bool $db=null){
		if($db){
			$message = '... Database error encountered - ' . $message;
		}
		$this->properties = array_merge(
                $this->properties, 
                ['error_message' => $message], $props
            );
			
            return $this->rend($error_page, $this->properties);
	}

	protected function successMessage($message, $success_page, array $props=[]){
		
		$this->properties = array_merge(
                $this->properties, 
                ['success_message' => $message], $props
            );
			//print_r($this->properties);
            return $this->rend($success_page, $this->properties);
	}

	protected function returnMessage($return_page, array $props=[]){
		
		$this->properties = array_merge(
                $this->properties,  $props
            );
			//print_r($this->properties);
            return $this->rend($return_page, $this->properties);
	}          
}

