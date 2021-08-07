<?php
/*
 * HomeController.php
 * 
 * Copyright 2021 jossy <jossy@DESKTOP-7C418G4>
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

use Bookstore\Models\AuthenticationModel;
use Bookstore\Exceptions\DbException;
use Bookstore\Exceptions\NotFoundException;

class AuthController extends AbstractController{
	public function isUserActive(){
        //$request->cookies()->addCookie('gfcc','uid',['user'=>'joeman', 'pass'=>'joref']);

		//Find user cookie
        $user_cookie = (array) $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
        return (!empty($user_cookie) && array_key_exists('uid',$user_cookie))? true: false;
	}

    private function isUserValid(Object $user){
        //$user = (object) $user;
        if($user->login_as->value == 'patient'){
            $credential = $this->getUserCredentials($user->username->value, $user->password->value);
            
            if(!is_array($credential))
                return false;
            
            return true;
        }
            
        //This reads credentials fro file and is only applcable to doctors.
        $credentials = (object) $this->config->get('uid');

        if(
            $user->username->name == $credentials->username && 
            $user->password->name == $credentials->password
        ){ 
            return true;
        }
        return false;
    }

    private function getUserCredentials($username, $password){print_r($password);
        $this->model = new AuthenticationModel($this->db);
        try{
            $credential = $this->model->getUserCredentials($username, $password);
        }catch(DbException $e){
            return $this->errorMessage($e->getMessage(),$this->page,[]);
        }catch(NotFoundException $e){
            return $this->errorMessage($e->getMessage(),$this->page,[]);
        }

        return $credential;
    }

    public function loginForm():string{
        $this->page = 'access_page.twig';
        if($this->request->isPost()){
			$request = $this->request->getParams();

            $filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					$view_data_with_errors, ['formSection'=>'login_user_info']
				);
				
                return $this->rend($this->page, $this->properties);
            }

            //Verify user inputs against database
            //print_r($view_data_build);
            return $this->loginUser($view_data_build);
        }

        $this->properties = array_merge($this->properties, ['formSection'=>'login_user_info']);
        return $this->rend($this->page, $this->properties);
    }

    public function signupForm():string{
        $this->page = 'access_page.twig';
        if($this->request->isPost()){
			$request = $this->request->getParams();

            $filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);
            //print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					$view_data_with_errors, ['formSection'=>'signup_user_info']
				);
				
                return $this->rend($this->page, $this->properties);
            }

            //Verify user inputs against database
            //print_r($view_data_build);
            return $this->signupUser($view_data_build);
        }

        $this->properties = array_merge($this->properties, ['formSection'=>'signup_user_info']);
        return $this->rend($this->page, $this->properties);
    }

    private function loginUser($data){
        $data = (object) $data;
        if(!$this->isUserValid($data)){
            $this->properties = array_merge($this->properties, 
                ['error_message'=> "Username or Password is incorrect"],
                ['formSection'=> 'login_user_info']
            );
            $this->log->error("Could not verify login credentials.");

            return $this->rend($this->page, $this->properties);
        }
        
        //Store temporary data in cookie
        //Refactor to persist data to database
        $this->request->cookies()->addCookie( self::APP_COOKIE_NAME,'uid',
            ['username'=>$data->username->name, 'password'=>$data->password->name, 'usertype'=>$data->login_as->name]
        );

       // print_r($this->request->cookies()->get('gfcc'));
       $controller = new HomeController($this->request);
       return $controller->getPageContent($data->login_as->name);
       
    }

    private function signupUser($data){
        $data = (object) $data;
        //print_r($data);
        //print_r($this->page);
        //print_r();
        //Store temporary data in cookie
        if(!is_bool($user = $this->addNewUser($data)))
            return $user;

        //Refactor to persist data to database
        $this->request->cookies()->addCookie(
            self::APP_COOKIE_NAME, 'uid', 
            ['username'=>$data->username_profile->name, 'password'=>$data->password->name, 'usertype'=>'patient']
        );

       // print_r($this->request->cookies()->get('gfcc'));
       $controller = new HomeController($this->request);
       return $controller->getPageContent('patient');
       
    }

    private function addNewUser($validated_data){
        $this->model = new AuthenticationModel($this->db);
        $request = $validated_data;

        try{
           $addUser = $this->model->addNewUser(
               $request->surname->value, $request->firstname->value, $request->address->value,
               $request->phone_number->value, $request->password->value, $request->username_profile->value,
               $request->occupation->value
           ); 
        }catch(DbException $e){
            //print_r($e->getMessage());
            return $this->errorMessage($e->getMessage(),$this->page, [], true);
        }

        return true;
    }

    public function logoutForm(){
        $destroy = $this->request->cookies()->deleteCookie(self::APP_COOKIE_NAME, 'uid');

        return $this->loginForm();
    }

    private function is_inputs_filtered($request):array{
        /* 
            Ensures that required fields are validated. Check for errors within fields
        */
		
        $errors = [];
        $filtered = [true, $errors];

        //Validation for login page
        if($request->has('login_user_info')){
            $filtered =  $request->whenHas($field = 'username', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Username is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'password', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Password is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'login_as', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Login type is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });
        }
        

        //Validation for signup page
        if($request->has('signup_user_info')){ 
            $filtered =  $request->whenHas($field = 'surname', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Surname is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'firstname', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Firstname is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'address', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Address is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'phone_number', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Phone number is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'password', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Password is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'password_confirm', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Confirm password is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                if($request->get($field) !== $request->get('password')){
                    $message = "Confirm password is not the same as Password";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'username_profile', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Username is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'marital_status', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Marital status is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'dob', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Date of birth is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });

            $filtered =  $request->whenHas($field = 'occupation', function()use($request, $field, &$errors){
                //$field = "first_name";
                if(!$request->filled($field)){
                    $message = "Occupation is not filled";
                    array_push($errors, $errors[$field]=$message);
                    return [false, $errors];
                }

                return [true, $errors];
            });
        }

        return $filtered;
    }

}
