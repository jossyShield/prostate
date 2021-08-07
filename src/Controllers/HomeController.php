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

use Bookstore\Models\DiagnosisModel;
use Bookstore\Exceptions\DbException;
use Bookstore\Exceptions\NotFoundException;
use Bookstore\Exceptions\FileUploadException;

class HomeController extends AbstractController{
	public function getSignUpContent():string{
		//Form validation hash data
		$form_sections = [ 
			'login_user_info',
			'signup_user_info'
		];
		$form_sections_hash = [
			'login_user'=>'login_user_info',
			'signup_user'=>'signup_user_info'
		];

		$this->page = 'signup.twig';

		if($this->request->isPost()){
			$request = $this->request->getParams();
			
			//Ensure that form is a valid one and ready to be processed
			if(!$this->isFormValid($this->getActiveFormKey($request,$form_sections_hash))){
				$this->properties = array_merge($this->properties, 
					['formSection'=>'invalid_form']
				);
				return $this->rend($this->page,$this->properties);
			}
			
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			$current_section = $request->has('signup_user_info')? 'signup_user_info': 'login_user_info';
			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>$current_section],
					$view_data_with_errors
				);
				
                return $this->rend($this->page, $this->properties);
            }

		}

		$this->properties = array_merge($this->properties, 
			['formSection'=>'signup_user_info']
		);

		return $this->rend($this->page,$this->properties);
	}

	public function getLoginContent():string{
		//Form validation hash data
		$form_sections = [ 
			'login_user_info',
			'signup_user_info'
		];
		$form_sections_hash = [
			'login_user'=>'login_user_info',
			'signup_user'=>'signup_user_info'
		];

		$this->page = 'signup.twig';

		if($this->request->isPost()){
			$request = $this->request->getParams();
			
			//Ensure that form is a valid one and ready to be processed
			if(!$this->isFormValid($this->getActiveFormKey($request,$form_sections_hash))){
				$this->properties = array_merge($this->properties, 
					['formSection'=>'invalid_form']
				);
				return $this->rend($this->page,$this->properties);
			}
			
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			$current_section = $request->has('signup_user_info')? 'signup_user_info': 'login_user_info';
			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>$current_section],
					$view_data_with_errors
				);
				
                return $this->rend($this->page, $this->properties);
            }

		}

		$this->properties = array_merge($this->properties, 
			['formSection'=>'login_user_info']
		);
		return $this->rend('signup.twig',$this->properties);
	}

	public function getPageContent($userType=''){//manageMembers()
		//Form valaidation data
		$form_sections = [ 
			'place_diagnosis_info'
		];
		$form_sections_hash = [
			'place_diagnosis'=>'place_diagnosis_info'
		];
		$this->page = 'index.twig';
		$cookie = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
		$request = $this->request->getParams();
		
		$user_access = isset($cookie->uid)?$cookie->uid->usertype: $userType;
		
		$this->username = isset($cookie->uid)?$cookie->uid->username: null;
		$this->model = new DiagnosisModel($this->db);
		
		$this->diagnosis_report = $this->formatDiagnosisReport($this->getDiagnosisReport());
		//$this->diagnosis_report_unread = $this->diagnosis_report[1];
		//print_r($this->diagnosis_report);
		$this->diagnosis_report_doc = $this->formatDiagnosisReportDoc($this->getDiagnosisReportDoc());
		//print_r($this->diagnosis_report_doc);	

		$this->diagnosis_report_queue = $this->formatDiagnosisReport($this->getDiagnosisReportQueue());
		$this->diagnosis_report_doc_queue = $this->formatDiagnosisReportDoc($this->getDiagnosisReportDocQueue());

		if($this->request->isPost() && $request->has('place_diagnosis_info')){
			$request = $this->request->getParams();

			//Ensure that form is a valid one and ready to be processed
			if(!$this->isFormValid($this->getActiveFormKey($request,$form_sections_hash))){
				$this->properties = array_merge($this->properties, 
					['formSection'=>'invalid_form']
				);
				return $this->rend($this->page,$this->properties);
			}
			
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, $this->diagnosis_report, $this->diagnosis_report_doc
				);
				
                return $this->rend('index.twig', $this->properties);
            }
			
			$diagnosis = $this->submitDiagnosis($view_data_build);
			if(!is_bool($diagnosis)){
				return $diagnosis;
			}
			
			//Load next section of Form
			$this->properties = array_merge($this->properties, 
				['formSection'=>'place_diagnosis_info','userAccess'=>$user_access]
			);
			
			return $this->rend('index.twig',$this->properties);
		}

		if($this->request->isPost() && $request->has('diagnosis_report_info')){
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, $this->diagnosis_report, $this->diagnosis_report_doc
				);
				
                return $this->rend($this->page, $this->properties);
            }
			//print_r($view_data_build);

			$fuzzy_output = $this->getDiagnosisReportOutput($view_data_build['diagnosis_report']->value, 'diagnosis');
			//print_r($fuzzy_output);
			if(!is_array($fuzzy_output))
				return $fuzzy_output;

			$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$this->diagnosis_report, $this->diagnosis_report_doc,
					['diagnosisOutput'=>$fuzzy_output], 
					['diagnosisId'=>$view_data_build['diagnosis_report']->value]
				);

			return $this->returnMessage($this->page, $this->properties);
		}

		if($this->request->isPost() && $request->has('diagnosis_report_doc_info')){
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, $this->diagnosis_report, $this->diagnosis_report_doc
				);
				
                return $this->rend($this->page, $this->properties);
            }
			//print_r($view_data_build);

			$fuzzy_output = $this->getDiagnosisReportOutputDoc($view_data_build['diagnosis_report_doc']->value,'doctor');
			//print_r($fuzzy_output);
			if(!is_array($fuzzy_output))
				return $fuzzy_output;

			$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$this->diagnosis_report, $this->diagnosis_report_doc,
					['diagnosisOutputDoc'=>$fuzzy_output], 
					['diagnosisId'=>$view_data_build['diagnosis_report_doc']->value]
				);

			return $this->returnMessage($this->page, $this->properties);
		}

		if($this->request->isPost() && $request->has('medical_advice_info')){
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, $this->diagnosis_report, $this->diagnosis_report_doc
				);
				
                return $this->rend($this->page, $this->properties);
            }
			//print_r($view_data_build);
			$seek_advice = $this->seekMedicalAdvice($view_data_build['medical_advice_info']->value);
			if(!is_bool($seek_advice))
				return $seek_advice;

			$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$this->diagnosis_report, $this->diagnosis_report_doc
				);

			return $this->returnMessage($this->page, $this->properties);
		}

		if($this->request->isPost() && $request->has('diagnosis_report_queue_info')){
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, $this->diagnosis_report, $this->diagnosis_report_doc
				);
				
                return $this->rend($this->page, $this->properties);
            }
			//print_r($view_data_build);

			$fuzzy_output = $this->getDiagnosisReportOutputQueue($view_data_build['diagnosis_report']->value);
			//print_r($fuzzy_output);
			if(!is_array($fuzzy_output))
				return $fuzzy_output;

			$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$user_access =='doctor'? $this->diagnosis_report_queue : $this->diagnosis_report, 
					$user_access =='doctor'? $this->diagnosis_report_doc_queue : $this->diagnosis_report_doc,
					['diagnosisOutput'=>$fuzzy_output], 
					['diagnosisId'=>$view_data_build['diagnosis_report']->value]
				);

			return $this->returnMessage($this->page, $this->properties);
		}

		if($this->request->isPost() && $request->has('diagnosis_report_doc_queue_info')){
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, $this->diagnosis_report, $this->diagnosis_report_doc
				);
				
                return $this->rend($this->page, $this->properties);
            }
			//print_r($view_data_build);

			$fuzzy_output = $this->getDiagnosisReportOutputDocQueue($view_data_build['diagnosis_report_doc']->value);
			//print_r($fuzzy_output);
			if(!is_array($fuzzy_output))
				return $fuzzy_output;

			$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$user_access =='doctor'? $this->diagnosis_report_queue : $this->diagnosis_report, 
					$user_access =='doctor'? $this->diagnosis_report_doc_queue : $this->diagnosis_report_doc,
					['diagnosisOutputDoc'=>$fuzzy_output], 
					['diagnosisId'=>$view_data_build['diagnosis_report_doc']->value]
				);

			return $this->returnMessage($this->page, $this->properties);
		}

		if($this->request->isPost() && $request->has('doctors_response_info')){
			//Filter and validate user inputs
			$filtered = $this->is_inputs_filtered($request);
			$view_data_build = $this->buildViewData($request, $filtered[1]);

			//print_r($view_data_build);
            if(!empty($filtered[1]) || !$filtered[0]){
                //@var filtered contains errors, return error_fields
                //Build data for View
                $view_data_with_errors = $view_data_build;
				//print_r($view_data_with_errors);
				$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$view_data_with_errors, 
					$user_access =='doctor'? $this->diagnosis_report_queue : $this->diagnosis_report, 
					$user_access =='doctor'? $this->diagnosis_report_doc_queue : $this->diagnosis_report_doc
				);
				
                return $this->rend($this->page, $this->properties);
            }
			//print_r($view_data_build);
			$combine_advice = $this->combineMedicalAdvice(
				$view_data_build['advice1']->value, $view_data_build['advice2']->value,
				$view_data_build['advice3']->value, $view_data_build['advice4']->value
			);
			$profer_advice = $this->proferMedicalAdvice(
				$view_data_build['doctors_response_info']->value, $combine_advice
			);
			if(!is_bool($profer_advice))
				return $profer_advice;

			$this->properties = array_merge($this->properties, 
					['formSection'=>'place_diagnosis_info', 'userAccess'=>$user_access],
					$user_access =='doctor'? $this->diagnosis_report_queue : $this->diagnosis_report, 
					$user_access =='doctor'? $this->diagnosis_report_doc_queue : $this->diagnosis_report_doc
				);

			return $this->returnMessage($this->page, $this->properties);
		}

		//print_r($this->diagnosis_report);
		$this->properties = array_merge(
			$this->properties, ['formSection'=>'place_diagnosis_info','userAccess'=>$user_access],
			$user_access =='doctor'? $this->diagnosis_report_queue : $this->diagnosis_report, 
			$user_access =='doctor'? $this->diagnosis_report_doc_queue : $this->diagnosis_report_doc
		);
		
		return $this->rend($this->page, $this->properties);
		//return $this->rend('member.twig', $this->properties);
	}

	private function combineMedicalAdvice(string $advice1, string $advice2, string $advice3, string $advice4):string{
		$delimeter = "{{_}}";
		$advice = [$advice1, $advice2, $advice3, $advice4];

		return implode($delimeter, $advice);
	}

	private function seekMedicalAdvice($diagnosis_id){
		try{
			$advice = $this->model->seekMedicalAdvice($diagnosis_id);
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(), $this->page, [], true);
		}

		return true;
	}

	private function proferMedicalAdvice($diagnosis_id, $advice){
		try{
			$advice = $this->model->proferMedicalAdvice($diagnosis_id, $advice);
			$this->diagnosis_report_queue = $this->formatDiagnosisReport($this->getDiagnosisReportQueue());
			$this->diagnosis_report_doc_queue = $this->formatDiagnosisReportDoc($this->getDiagnosisReportDocQueue());
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(), $this->page, [], true);
		}

		return true;
	}

	private function getDiagnosisFuzzySets($diagnosis_id, $report_type){
		try{
			$fuzzy_sets = $this->model->getDiagnosisFuzzySets($diagnosis_id, $report_type);
			if($report_type == 'diagnosis')
				$this->diagnosis_report = $this->formatDiagnosisReport($this->getDiagnosisReport());
			else
				$this->diagnosis_report_doc = $this->formatDiagnosisReportDoc($this->getDiagnosisReportDoc());

		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(), $this->page, [], true);
		}catch(NotFoundException $e){
			return $this->errorMessage($e->getMessage(), $this->page, []);
		}

		return $fuzzy_sets;
	}


	private function getDiagnosisFuzzySetsDoc($diagnosis_id){
		try{
			$fuzzy_sets = $this->model->getDiagnosisFuzzySetsDoc($diagnosis_id);
			
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(), $this->page, [], true);
		}catch(NotFoundException $e){
			return $this->errorMessage($e->getMessage(), $this->page, []);
		}

		return $fuzzy_sets;
	}

	private function getDiagnosisReportOutput($diagnosis_id, $report_type){
		$fuzzy_sets = (object) $this->getDiagnosisFuzzySets($diagnosis_id, $report_type)[0];

		if(!is_object($fuzzy_sets))
			return $fuzzy_sets;

		$tnm = $fuzzy_sets->TNM;// 't1a';
		$gs = $fuzzy_sets->GS;//'5';
		$psa = $fuzzy_sets->PSA;//'13.21';
		$ipss = $fuzzy_sets->IPSS;//'23';
		$age = date('Y', time()) - date('Y',strtotime($fuzzy_sets->DOB));//'45';
		$pbv = $fuzzy_sets->PBV;//'7.50';
		$act = $fuzzy_sets->ACT;//'2.00';
		$fpsa = $fuzzy_sets->FPSA;//'31.00';
		
		$this->diag_1 = $this->diagnosisOutputOne($tnm);
		$this->diag_2 = $this->diagnosisOutputTwo($gs, $psa, $ipss, $age, $pbv, $act);
		$this->diag_3 = $this->diagnosisOutputThree($fpsa, $psa);
		$this->diag_4 = $this->diagnosisOutputAll($tnm, $gs, $psa, $ipss, $age, $pbv, $act, $fpsa);
		return [
			['outputCount'=>'1', 'diagnosisDeduction'=> $this->diag_1[0]],
			['outputCount'=>'2', 'diagnosisDeduction'=> $this->diag_2[0]],
			['outputCount'=>'3', 'diagnosisDeduction'=> $this->diag_3[0]],
			['outputCount'=>'4', 'diagnosisDeduction'=> $this->diag_4[0]]
		];
	}
	private function getDiagnosisReportOutputQueue($diagnosis_id){
		$fuzzy_sets = (object) $this->getDiagnosisFuzzySetsDoc($diagnosis_id)[0];

		if(!is_object($fuzzy_sets))
			return $fuzzy_sets;

		$tnm = $fuzzy_sets->TNM;// 't1a';
		$gs = $fuzzy_sets->GS;//'5';
		$psa = $fuzzy_sets->PSA;//'13.21';
		$ipss = $fuzzy_sets->IPSS;//'23';
		$age = date('Y', time()) - date('Y',strtotime($fuzzy_sets->DOB));//'45';
		$pbv = $fuzzy_sets->PBV;//'7.50';
		$act = $fuzzy_sets->ACT;//'2.00';
		$fpsa = $fuzzy_sets->FPSA;//'31.00';
		
		$this->diag_1 = $this->diagnosisOutputOne($tnm);
		$this->diag_2 = $this->diagnosisOutputTwo($gs, $psa, $ipss, $age, $pbv, $act);
		$this->diag_3 = $this->diagnosisOutputThree($fpsa, $psa);
		$this->diag_4 = $this->diagnosisOutputAll($tnm, $gs, $psa, $ipss, $age, $pbv, $act, $fpsa);
		return [
			['outputCount'=>'1', 'diagnosisDeduction'=> $this->diag_1[0]],
			['outputCount'=>'2', 'diagnosisDeduction'=> $this->diag_2[0]],
			['outputCount'=>'3', 'diagnosisDeduction'=> $this->diag_3[0]],
			['outputCount'=>'4', 'diagnosisDeduction'=> $this->diag_4[0]]
		];
	}

	private function getDiagnosisReportOutputDoc($diagnosis_id, $report_type){
		$fuzzy_sets = (object) $this->getDiagnosisFuzzySets($diagnosis_id, $report_type)[0];

		if(!is_object($fuzzy_sets))
			return $fuzzy_sets;

		$tnm = $fuzzy_sets->TNM;// 't1a';
		$gs = $fuzzy_sets->GS;//'5';
		$psa = $fuzzy_sets->PSA;//'13.21';
		$ipss = $fuzzy_sets->IPSS;//'23';
		$age = date('Y', time()) - date('Y',strtotime($fuzzy_sets->DOB));//'45';
		$pbv = $fuzzy_sets->PBV;//'7.50';
		$act = $fuzzy_sets->ACT;//'2.00';
		$fpsa = $fuzzy_sets->FPSA;//'31.00';
		$docs_advice = (!empty($fuzzy_sets->DOCS_ADVICE) && count(explode('{{_}}',$fuzzy_sets->DOCS_ADVICE)) === 4 )? 
			explode('{{_}}',$fuzzy_sets->DOCS_ADVICE): ['','','',''];
		
		$this->diag_1 = $this->diagnosisOutputOne($tnm);
		$this->diag_2 = $this->diagnosisOutputTwo($gs, $psa, $ipss, $age, $pbv, $act);
		$this->diag_3 = $this->diagnosisOutputThree($fpsa, $psa);
		$this->diag_4 = $this->diagnosisOutputAll($tnm, $gs, $psa, $ipss, $age, $pbv, $act, $fpsa);
		return [
			['outputCount'=>'1', 'diagnosisDeduction'=> $this->diag_1[0], 'diagnosisRemark'=>$docs_advice[0]],
			['outputCount'=>'2', 'diagnosisDeduction'=> $this->diag_2[0], 'diagnosisRemark'=>$docs_advice[1]],
			['outputCount'=>'3', 'diagnosisDeduction'=> $this->diag_3[0], 'diagnosisRemark'=>$docs_advice[2]],
			['outputCount'=>'4', 'diagnosisDeduction'=> $this->diag_4[0], 'diagnosisRemark'=>$docs_advice[3]]
		];
	}
	private function getDiagnosisReportOutputDocQueue($diagnosis_id){
		$fuzzy_sets = (object) $this->getDiagnosisFuzzySetsDoc($diagnosis_id)[0];

		if(!is_object($fuzzy_sets))
			return $fuzzy_sets;

		$tnm = $fuzzy_sets->TNM;// 't1a';
		$gs = $fuzzy_sets->GS;//'5';
		$psa = $fuzzy_sets->PSA;//'13.21';
		$ipss = $fuzzy_sets->IPSS;//'23';
		$age = date('Y', time()) - date('Y',strtotime($fuzzy_sets->DOB));//'45';
		$pbv = $fuzzy_sets->PBV;//'7.50';
		$act = $fuzzy_sets->ACT;//'2.00';
		$fpsa = $fuzzy_sets->FPSA;//'31.00';
		$docs_advice = (!empty($fuzzy_sets->DOCS_ADVICE) && count(explode('{{_}}',$fuzzy_sets->DOCS_ADVICE)) === 4 )? 
			explode('{{_}}',$fuzzy_sets->DOCS_ADVICE): ['','','',''];
		
		$this->diag_1 = $this->diagnosisOutputOne($tnm);
		$this->diag_2 = $this->diagnosisOutputTwo($gs, $psa, $ipss, $age, $pbv, $act);
		$this->diag_3 = $this->diagnosisOutputThree($fpsa, $psa);
		$this->diag_4 = $this->diagnosisOutputAll($tnm, $gs, $psa, $ipss, $age, $pbv, $act, $fpsa);
		return [
			['outputCount'=>'1', 'diagnosisDeduction'=> $this->diag_1[0], 'diagnosisRemark'=>$docs_advice[0]],
			['outputCount'=>'2', 'diagnosisDeduction'=> $this->diag_2[0], 'diagnosisRemark'=>$docs_advice[1]],
			['outputCount'=>'3', 'diagnosisDeduction'=> $this->diag_3[0], 'diagnosisRemark'=>$docs_advice[2]],
			['outputCount'=>'4', 'diagnosisDeduction'=> $this->diag_4[0], 'diagnosisRemark'=>$docs_advice[3]]
		];
	}

	private function diagnosisWeigthFunction(float $weight, int $output_number, string $tnm  = null){
		$z = '';
		switch($output_number){
			case '1':
				if($tnm == 'nil')
					$z = ['non existent', 0];
				if($tnm == 't1a' || $tnm == 't1b')
					$z = ['very low', 1];
				if($tnm == 't2a')
					$z = ['low', 2];
				if($tnm == 't2b')
					$z = ['moderate', 3];
				if($tnm == 't3a')
					$z = ['slightly moderate', 4];
				if($tnm == 't3b')
					$z = ['high', 5];
				if($tnm == 't3c')
					$z = ['slightly high', 6];
				if($tnm == 't4a')
					$z = ['very high', 7];
				break;
			case '2':
				if(0.00 <= $weight && $weight <= 0.069)
					$z = 'non existent';
				if(0.07 <= $weight && $weight <= 0.199)
					$z = 'very low';
				if(0.20 <= $weight && $weight <= 0.249)
					$z = 'low';
				if(0.25 <= $weight && $weight <= 0.399)
					$z = 'moderate';
				if(0.40 <= $weight && $weight <= 0.599)
					$z = 'slightly moderate';
				if(0.60 <= $weight && $weight <= 0.749)
					$z = 'high';
				if(0.75 <= $weight && $weight <= 0.899)
					$z = 'slightly high';
				if(0.90 <= $weight )//&& $weight <= 1
					$z = 'very high';

				break;
			case '3':
				if(0.9001 <= $weight )//&& $weight <= 0.1
					$z = 'non existent';
				if(0.7501 <= $weight && $weight <= 0.90)
					$z = 'very low';
				if(0.5001 <= $weight && $weight <= 0.75)
					$z = 'low';
				if(0.4001 <= $weight && $weight <= 0.50)
					$z = 'moderate';
				if(0.2601 <= $weight && $weight <= 0.40)
					$z = 'slightly moderate';
				if(0.1501 <= $weight && $weight <= 0.26)
					$z = 'high';
				if(0.0801 <= $weight && $weight <= 0.15)
					$z = 'slightly high';
				if(0.0000 <= $weight && $weight <= 0.08)
					$z = 'very high';

				break;
			default:
				if(0 <= $weight && $weight <= 0.0214)
					$z = 'non existent';
				if(0.0215 <= $weight && $weight <= 0.15)
					$z = 'very low';
				if(0.1501 <= $weight && $weight <= 0.30)
					$z = 'low';
				if(0.3001 <= $weight && $weight <= 0.45)
					$z = 'moderate';
				if(0.4501 <= $weight && $weight <= 0.60)
					$z = 'slightly moderate';
				if(0.6001 <= $weight && $weight <= 0.75)
					$z = 'high';
				if(0.7501 <= $weight && $weight <= 0.90)
					$z = 'slightly high';
				if(0.9001 <= $weight && $weight <= 1)
					$z = 'very high';
		}

		return $z;
	}

	private function diagnosisOutputOne($tnm){
		$output_number = 1;
		$output = $this->diagnosisWeigthFunction('0.00', $output_number, $tnm);
		return [$output[0], $output[1]];
	}
	private function diagnosisOutputTwo($gs, $psa, $ipss, $age, $pbv, $act){
		$output_number = 2;
		$output = (0.4 * $gs) + (0.26 * $psa) + (0.14 * $ipss) + (0.08 * $age) + (0.07 * $pbv) + (0.05 * $act);
		
		return [$this->diagnosisWeigthFunction($output, $output_number), $output];
	}
	private function diagnosisOutputThree($fpsa, $psa){
		$output_number = 3;
		$output = ($fpsa / $psa);
		/* print_r('testing');
		print_r($output);
		print_r($this->diagnosisWeigthFunction($output, $output_number)); */
		return [$this->diagnosisWeigthFunction($output, $output_number), $output];
	}
	private function diagnosisOutputAll($tnm, $gs, $psa, $ipss, $age, $pbv, $act, $fpsa){
		$output_number = 4;
		$output = ( (0.45 * $this->diag_1[1]) + 
			(0.4 * $this->diag_2[1]) + 
			(0.15 * $this->diag_3[1]) )/ 7;

		return [$this->diagnosisWeigthFunction($output, $output_number), $output];
	}

	private function formatDiagnosisReport($report){
		if(!is_array($report)){
			return ['diagnosisReports'=>'', 'diagnosisUnread'=>''];
		}
		$diagnosis_unread = (end($report)['diagnosis_id'])?:'';
		$diagnosis_reports = array_slice($report,0, (count($report) - 1));
		//print_r(end($report)['diagnosis_id']);
		return ['diagnosisReports'=>$diagnosis_reports, 'diagnosisUnread'=>$diagnosis_unread];
	}

	private function formatDiagnosisReportDoc($report){
		if(!is_array($report)){
			return ['diagnosisReportsDoc'=>'', 'diagnosisUnreadDoc'=>''];
		}
		$diagnosis_unread = (end($report)['diagnosis_id'])?:'';
		$diagnosis_reports = array_slice($report,0, (count($report) - 1));
		//print_r(end($report)['diagnosis_id']);
		return ['diagnosisReportsDoc'=>$diagnosis_reports, 'diagnosisUnreadDoc'=>$diagnosis_unread];
	}
	private function getDiagnosisReport(){
		
		try{
			$report = $this->model->getDiagnosisReport($this->username);
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}catch(NotFoundException $e){
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}

		return $report;
	}

	private function getDiagnosisReportDoc(){
		
		try{
			$report = $this->model->getDiagnosisReportDoc($this->username);
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}catch(NotFoundException $e){
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}

		return $report;
	}

	private function getDiagnosisReportQueue(){
		
		try{
			$report = $this->model->getDiagnosisReportQueue();
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}catch(NotFoundException $e){
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}

		return $report;
	}
	private function getDiagnosisReportDocQueue(){
		
		try{
			$report = $this->model->getDiagnosisReportDocQueue();
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}catch(NotFoundException $e){
			return $this->errorMessage($e->getMessage(),$this->page, []);
		}

		return $report;
	}

	private function submitDiagnosis($data){
		$request = (object) $data;
		//$user_cookie = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
        //(!empty($user_cookie) && array_key_exists('uid',$user_cookie));
		//print_r($user_cookie);
		//$username = $user_cookie->uid->username;

		$username = $this->username;
		try{
			$diagnosis = $this->model->addNewDiagnosis(
				$request->age->value, $request->gleason_score->value, $request->psa->value,
				$request->ipss->value, $request->activity->value, $request->fpsa->value, 
				$request->pbv->value, $request->tnms->value, $request->ethnicity->value,
				$request->heredity->value, $username
			);
		}catch(DbException $e){
			//print_r($e->getMessage());
			return $this->errorMessage($e->getMessage(),$this->page, [],true);
		}

		return true;
	}

	private function is_inputs_filtered($request):array{
        /* 
        Ensures that required fields are validated. Check for errors within fields
         */
		
        $errors = [];
        $filtered = [true, $errors];

		if($request->has('place_diagnosis_info')){
			$filtered =  $request->whenHas($field = 'age', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Age has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'gleason_score', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Gleason score is not filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'psa', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "PSA is not filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'ipss', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "I-PSS has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'activity', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Activity has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'fpsa', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "FPSA has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'pbv', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "PBV has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'tnms', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "TNMS has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'ethnicity', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Ethnicity has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'heredity', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Heredity has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});
		}

		if($request->has('diagnosis_report_info')){
			$filtered =  $request->whenHas($field = 'diagnosis_report', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Diagnosis Report has been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});
		}

		if($request->has('doctors_response_info')){
			$filtered =  $request->whenHas($field = 'advice1', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Advice for Level 1 has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'advice2', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Advice for Level 2 has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'advice3', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Advice for Level 3 has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});

			$filtered =  $request->whenHas($field = 'advice4', function()use($request, $field, &$errors){
				//$field = "first_name";
				if(!$request->filled($field)){
					$message = "Advice for Level 4 has not been filled";
					array_push($errors, $errors[$field]=$message);
					return [false, $errors];
				}

				return [true, $errors];
			});
		}

        return $filtered;
    }

	private function getActiveFormKey($request, $form_sections_hash){
		$post_keys = $request->getAll();

        foreach($post_keys as $key => $value){
			if(in_array($key, $form_sections_hash)){
				$hash_key = array_search($key, $form_sections_hash);
				//returns server hashkey, form validation value & active form key
				return [$hash_key,$value,$key];
			}
		}

		return false;
	}

	private function isFormValid(array $form_hash_keys){
		if(!is_bool($form_hash_keys) && $form_hash_keys[0] === $form_hash_keys[1]){
			return true;
		}
		return false;
	}

	private function getNextFormSection(array $form_sections, string $current_section){
		if(in_array($current_section, $form_sections)){
			$key = array_search($current_section, $form_sections);
			
			if(array_key_exists(++$key, $form_sections)){
				return $form_sections[$key];
			}else
				return "completed_form";
		}

	}

	private function addFormSectionData(string $current_section, array $validated_posts, array $form_sections){
		//return true;
		$request = $validated_posts;
		$model = new MemberModel($this->db);

		//Get gfcc cookie
		//$gfcc = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
		//$form_number = $gfcc->membership->form_number;
		//$form_number = "";
		if($current_section === 'login_user_info'){
			//Load the next form state that has not been saved
			$form_state = $current_section;
			$next_form_section = $this->getNextFormSection($form_sections, $form_state);
			$this->properties = array_merge($this->properties, 
				['formSection'=> $next_form_section]
			);
			//print_r($this->properties);
			return $this->rend('index.twig', $this->properties);

		}
		elseif($current_section === 'form_number_info'){
			try{
				/* 
				@var form_state contains the last section of the form
				that was last saved
				 */
				$form_number = $request['form_number']->value;
				$form_state = $model->getStateByFormNumber($form_number);

				//Set cookie for the form number
				$this->request->cookies()->setCookie("form_number", $form_number, "membership", self::APP_COOKIE_NAME);

				
				
				//Load the next form state that has not been saved
				$next_form_section = $this->getNextFormSection($form_sections, $form_state);
				$this->properties = array_merge($this->properties, 
					['formSection'=> $next_form_section]
				);

				//Check if no more form state available and output corresponding message to the user
				if($next_form_section == 'completed_form')
					$this->properties = array_merge(
						$this->properties, 
						['form_completed_message' => "This form with No.:{$form_number} has correctly been verified."]
					);
				return $this->rend('member.twig', $this->properties);

			}catch(NotFoundException $e){
				//Set cookie for the form number
				$this->request->cookies()->setCookie("form_number", $request['form_number']->value, "membership", self::APP_COOKIE_NAME);
				
				//Load Fresh member form
				return true;
			}
			
		}
		elseif($current_section === 'biodata_info'){
			try{
				//print_r($request);
				$gfcc = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
				$form_number = $gfcc->membership->form_number;

				$model->addBiodataInfo(
					$request['surname']->value, $request['first_name']->value, $request['other_name']->value, 
					$request['gender']->value, ("{$request['dob_year']->value}-{$request['dob_month']->value}-{$request['dob_day']->value}"), $request['nationality']->value, 
					$request['state_of_origin']->value, $request['lga']->value, $request['home_town']->value, 
					$request['residential_address']->value, $request['land_mark']->value, $request['residence_country']->value, 
					$request['residence_state']->value, $request['phone_number']->value, $request['other_number']->value, 
					$request['whatsapp_number']->value, $request['email_address']->value, $request['nok_name']->value, 
					$request['nok_phone']->value, $request['nok_relationship']->value, $form_number, $current_section
				);
			}catch(DbException $e){
				$this->properties = array_merge(
					$this->properties, 
					['formSection'=>$current_section],
					['error_message' => '... Database error encountered - ' . $e->getMessage()]
				);
		
				return $this->rend('member.twig', $this->properties);
			}
		}
		elseif($current_section === 'family_info'){
			try{
				//print_r($request);
				$gfcc = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
				$form_number = $gfcc->membership->form_number;
				
				$model->addFamilyInfo(
					$request['marital_status']->value,$request['spouse_name']->value,$request['maiden_name']->value,
					$request['spouse_phone_number']->value, $request['spouse_email']->value,("{$request['m_a_day']->value}-{$request['m_a_month']->value}"),
					("{$request['spouse_birth_day']->value}-{$request['spouse_birth_month']->value}"), $request['spouse_nationality']->value,
					$request['spouse_state_of_origin']->value, $request['spouse_lga']->value, $request['spouse_home_town']->value,
					$request['spouse_occupation']->value, $request['spouse_business']->value, $request['spouse_position_held']->value,
					$request['spouse_business_address']->value, $request['spouse_relevant_info']->value, $request['marriage_length']->value,
					$request['marriage_no_of_children']->value, $request['children_in_gfcc']->value, $request['single_parent']->value,
					$request['spouse_in_gfcc']->value, $request['spouse_attend_church']->value, $request['spouse_denomination']->value,
					$request['engaged_living_together_not_married']->value, $request['married_traditionally_only']->value,
					$request['married_in_court']->value, $request['married_in_church']->value, $request['marriage_in_church_denomination']->value,
					$request['marriage_in_church_when']->value, $request['family_other_info']->value,
					$form_number, $current_section
				);
			}catch(DbException $e){
				$this->properties = array_merge(
					$this->properties, 
					['formSection'=>$current_section],
					['error_message' => '... Database error encountered - ' . $e->getMessage()]
				);
		
				return $this->rend('member.twig', $this->properties);
			}
		}
		elseif($current_section === 'education_occupation_info'){
			try{
				//print_r($request);
				$gfcc = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
				$form_number = $gfcc->membership->form_number;

				$model->addEducationOccupationInfo(
					$request['highest_academic_qualification']->value, $request['academic_qualification_others']->value,
					$request['academic_discipline']->value, $request['personal_occupation']->value, $request['personal_business']->value,
					$request['personal_position_held']->value, $request['personal_business_address']->value,
					$current_section, $form_number
				);
			}catch(DbException $e){
				$this->properties = array_merge(
					$this->properties, 
					['formSection'=>$current_section],
					['error_message' => '... Database error encountered - ' . $e->getMessage()]
				);
		
				return $this->rend('member.twig', $this->properties);
			}
		}
		elseif($current_section === 'christian_experience_info'){
			try{
				$gfcc = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
				$form_number = $gfcc->membership->form_number;

				$model->addChristianExperienceInfo(
					$request['denomination1_name']->value, $request['denomination1_from']->value,$request['denomination1_to']->value,
					$request['denomination1_position']->value, $request['denomination1_ministerial_position']->value,
					$request['denomination2_name']->value, $request['denomination2_from']->value, $request['denomination2_to']->value,
					$request['denomination2_position']->value, $request['denomination2_ministerial_position']->value,
					$request['denomination3_name']->value, $request['denomination3_from']->value, $request['denomination3_to']->value,
					$request['denomination3_position']->value, $request['denomination3_ministerial_position']->value,
					$request['family_church_which']->value, $request['baptized_in_water']->value, $request['baptized_in_water_when']->value,
					$request['baptized_in_water_where']->value, $request['baptized_in_holy_spirit']->value, $request['baptized_in_holy_spirit_when']->value,
					$request['baptized_in_holy_spirit_where']->value, $request['passion']->value, $request['special_skills']->value,
					$request['church_family_expectation']->value,
					$current_section, $form_number
				);
			}catch(DbException $e){
				//Refactor
				$this->properties = array_merge(
					$this->properties, 
					['formSection'=>$current_section],
					['error_message' => '... Database error encountered - ' . $e->getMessage()]
				);
		
				return $this->rend('member.twig', $this->properties);
			}
		}
		elseif($current_section === 'official_affirmation_info'){
			//print_r($request);
			
			try{
				$gfcc = $this->request->cookies()->getCookie(self::APP_COOKIE_NAME);
				$form_number = $gfcc->membership->form_number;
				$photo = $gfcc->photo->name;

				$model->addOfficialAffirmationInfo(
					$request['registration_date']->value, $request['official_remark']->value,
					$request['user_affirmation']->value, $photo,
					$current_section, $form_number
				);
			}catch(DbException $e){
				//Refactor
				$this->properties = array_merge(
					$this->properties, 
					['formSection'=>$current_section],
					['error_message' => '... Database error encountered - ' . $e->getMessage()]
				);
		
				return $this->rend('member.twig', $this->properties);
			}
		}

		return true;
	}

}
