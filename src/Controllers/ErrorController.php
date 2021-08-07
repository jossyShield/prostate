<?php
/*
 * ErrorController.php
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
class ErrorController extends AbstractController{
	
	public function notFound(): string{
		$properties = ['errorMessage' => 'Page not found!'];
		return $this->rend('error.twig', $properties);
		//return $this->rend('error-test.twig', $properties);
	}
	
	public function testPage(): string{
		$properties = ['privilegeStatus' => $this->request->getCookies()->get('privilege')];
		return $this->rend('test.twig', $this->properties);
		//return $this->rend('error-test.twig', $properties);
	}
	public function noAccess(): string{
		$this->properties = array_merge($this->properties, ['errorMessage' => 'Sorry, you are not allowed to access this page!']);
		return json_encode([
			"notif" => "Login Successful",
			"page_content" => $this->rend('error.twig', $this->properties)
		]);
		return $this->rend('error.twig', $this->properties);
		//return $this->rend('error-test.twig', $properties);
	}
}

