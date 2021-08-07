<?php
/*
 * AuthenticationModelphp
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
namespace Bookstore\Models;

use Bookstore\Exceptions\NotFoundException;
use Bookstore\Exceptions\DbException;

class AuthenticationModel extends AbstractModel{
	public function login(string $username): User {
		//'SELECT b.* FROM borrowed_books bb LEFT JOIN book b ON bb.book_id = b.id WHERE bb.customer_id = :id';
		
		$query = 'SELECT p.*, u.* FROM users u LEFT JOIN profile p ON u.id = p.id';
		$query .= ' WHERE u.name = :uname AND deleted = 0';
		
		//$query = 'SELECT * FROM user u LEFT JOIN profile p ON u.id = b.id';
		//$query .= ' WHERE u.name = :uname';
		
		$sth = $this->db->prepare($query);
		if(!$sth->execute(['uname' => $username]))
			throw new DbException($sth->errorInfo()[2]);
		
		$row = $sth->fetch();
		//var_dump($row);
		//Confirm that username exists
		if(empty($row)){
			throw new NotFoundException($sth->errorInfo()[2]);
		}
		
		
		//Confirm that User is acive
		if($row['active'] == '0'){
			throw new InactiveUserException($sth->errorInfo()[2]);
		}
		
		
		
		
		return UserFactory::factory(
			$row['user_type'],
			$row['firstname'],
			$row['lastname'],
			$row['email'],
			$row['phone'],
			$row['birthday'],
			$row['address'],
			$row['measurment'],
			$row['photo'],
			$row['service_location'],
			$row['active'],
			$row['name'],
			$row['password_hash']
			
		);
	}

	public function getUserCredentials($username, $password){
		$query = 'SELECT * FROM users WHERE username = :uname AND password = :pass AND usertype = :utype';

		$sth = $this->db->prepare($query);

		$usertype = '0';
		$sth->bindParam('uname', $username);
		$sth->bindParam('pass', $password);
		$sth->bindParam('utype', $usertype);

		if(!$sth->execute()){
			throw new DbException($sth->errorInfo()[2]);
		}

		$rows = $sth->fetchAll();
		//print_r($rows);
		if(empty($rows)){
			throw new NotFoundException('User or Password incorrect');
		}
		return $rows;

	}
	
	public function addNewUser(
		$surname, $firstname, $address, $phone, $password, $username, $occupation
	){
		$query = 'INSERT INTO users (surname, firstname, address, phone, password, username, occupation)';
		$query .= ' VALUES (:sname, :fname, :addr, :phone, :pass, :uname, :occ)';

		$sth = $this->db->prepare($query);

		$sth->bindParam('sname',$surname);
		$sth->bindParam('fname',$firstname);
		$sth->bindParam('addr',$address);
		$sth->bindParam('phone',$phone);
		$sth->bindParam('pass',$password);
		$sth->bindParam('uname',$username);
		$sth->bindParam('occ',$occupation);

		if(!$sth->execute()){
			throw new DbException($sth->errorInfo()[2]);
		}
	}
	
	
}
