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

class DiagnosisModel extends AbstractModel{
    
	public function addNewDiagnosis(
		$age, $gleason_score, $psa, $ipss, $activity, $fpsa, $pbv, $tnms, $ethnicity,
		$heredity, $username
	){
		$query = 'INSERT INTO diagnose (userid, diagnosis_hash, gleason_score, psa, ipss, activity, fpsa, pbv, tnms, ethnicity, heredity)';
		//$query .= ' VALUES (u.id, :age, :g_score, :psa, :ipss, :activity, :fpsa, :pbv, :tnms, :ethnicity, :heredity)';
        $query .= ' SELECT u.id, SHA1(CONCAT(d.id,NOW())), :g_score, :psa, :ipss, :activity, :fpsa, :pbv, :tnms, :ethnicity, :heredity';
        $query .= ' FROM diagnose d';
		$query .= ' LEFT JOIN users u ON u.username = :username';
        $query .= ' LIMIT 1';

        
		$sth = $this->db->prepare($query);

		//$sth->bindParam('diag_hash', $diag);
		$sth->bindParam('g_score', $gleason_score);
		$sth->bindParam('psa', $psa);
		$sth->bindParam('ipss', $ipss);
		$sth->bindParam('activity', $activity);
		$sth->bindParam('fpsa', $fpsa);
		$sth->bindParam('pbv', $pbv);
		$sth->bindParam('tnms', $tnms);
		$sth->bindParam('ethnicity', $ethnicity);
		$sth->bindParam('heredity', $heredity);
		$sth->bindParam('username', $username);

        if(!$sth->execute()){
            throw new DbException($sth->errorInfo()[2]);
        }
	}

    public function getDiagnosisReportQueue(){
        $query = 'SELECT diagnosis_hash AS diagnosis_id FROM diagnose d';
        //$query .= ' LEFT JOIN users u ON u.id = d.userid';
        $query .= ' WHERE user_request = :req AND user_request_reply = :reply';
        $query .= ' UNION';
        $query .= ' SELECT COUNT(*) AS diagnosis_unread FROM diagnose d';
        $query .= ' WHERE d.user_request = :req AND d.user_request_reply = :reply';

        $sth = $this->db->prepare($query);
        $request = '1';
        $reply = '0';

        if(!$sth->execute(['req'=>$request, 'reply'=> $reply])){
            throw new DbException($sth->errorInfo()[2]);
        }

        $rows = $sth->fetchAll();
        if(empty($rows)){
            throw new NotFoundException('Sorry! No diagnosis have been recorded for you');
        }

        //print_r($rows);

        return $rows;
    }

    public function getDiagnosisReportDocQueue(){
        $query = 'SELECT diagnosis_hash AS diagnosis_id FROM diagnose d';
        //$query .= ' LEFT JOIN users u ON u.id = d.userid';
        $query .= ' WHERE user_request = :req AND user_request_reply = :reply';
        $query .= ' UNION';
        $query .= ' SELECT COUNT(*) AS diagnosis_unread FROM diagnose d';
        $query .= ' WHERE d.user_request = :req AND d.user_request_reply = :reply';

        $sth = $this->db->prepare($query);
        $request = '1';
        $reply = '1';

        if(!$sth->execute(['req'=>$request, 'reply'=> $reply])){
            throw new DbException($sth->errorInfo()[2]);
        }

        $rows = $sth->fetchAll();
        if(empty($rows)){
            throw new NotFoundException('Sorry! No diagnosis have been recorded for you');
        }

       // print_r($rows);

        return $rows;
    }

    public function getDiagnosisReport($username){
        $query = 'SELECT diagnosis_hash AS diagnosis_id FROM diagnose d';
        $query .= ' LEFT JOIN users u ON u.id = d.userid';
        $query .= ' WHERE u.username = :username';
        $query .= ' UNION';
        $query .= ' SELECT COUNT(*) AS diagnosis_unread FROM diagnose d';
        $query .= ' WHERE d.user_request = 0 AND d.viewed_user_request = 0';

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['username'=>$username])){
            throw new DbException($sth->errorInfo()[2]);
        }

        $rows = $sth->fetchAll();
        if(empty($rows)){
            throw new NotFoundException('Sorry! No diagnosis have been recorded for you');
        }

        //print_r($rows);

        return $rows;
    }

    public function getDiagnosisReportDoc($username){
        $query = 'SELECT diagnosis_hash AS diagnosis_id FROM diagnose d';
        $query .= ' LEFT JOIN users u ON u.id = d.userid';
        $query .= ' WHERE u.username = :username AND d.user_request = 1';
        $query .= ' UNION';
        $query .= ' SELECT COUNT(*) AS diagnosis_unread FROM diagnose d';
        $query .= ' WHERE d.user_request = 1 AND d.user_request_reply = 1 AND d.viewed_report = 0';

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['username'=>$username])){
            throw new DbException($sth->errorInfo()[2]);
        }

        $rows = $sth->fetchAll();
        if(empty($rows)){
            throw new NotFoundException('Sorry! No diagnosis have been recorded for you');
        }

        //print_r($rows);

        return $rows;
    }

    public function getDiagnosisFuzzySets($diagnosis_hash, $report_type){
        $this->db->beginTransaction();
        if($report_type == 'diagnosis'){
            $query = 'UPDATE diagnose SET viewed_user_request = :req';
            $query .= ' WHERE diagnosis_hash = :hash';
        }else{
            $query = 'UPDATE diagnose SET viewed_report = :req';
            $query .= ' WHERE diagnosis_hash = :hash'; 
        }
        

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['hash'=>$diagnosis_hash, 'req'=>'1'])){
            $this->db->rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }

        $query = 'SELECT tnms AS TNM, gleason_score AS GS, psa AS PSA, ipss AS IPSS, pbv AS PBV,';
        $query .= ' activity AS ACT, fpsa AS FPSA, l123ftxt AS DOCS_ADVICE, u.dob AS DOB FROM diagnose d';
        $query .= ' LEFT JOIN users u ON u.id = d.userid';
        $query .= ' WHERE d.diagnosis_hash = :hash';

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['hash'=>$diagnosis_hash])){
            $this->db->rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }

        $rows = $sth->fetchAll();
        if(empty($rows)){
            throw new NotFoundException('Sorry! No record found for this diagnosis');
        }

        //print_r($rows);
        $this->db->commit();
        return $rows;
    }

    public function getDiagnosisFuzzySetsDoc($diagnosis_hash){
        $query = 'SELECT tnms AS TNM, gleason_score AS GS, psa AS PSA, ipss AS IPSS, pbv AS PBV,';
        $query .= ' activity AS ACT, fpsa AS FPSA, l123ftxt AS DOCS_ADVICE, u.dob AS DOB FROM diagnose d';
        $query .= ' LEFT JOIN users u ON u.id = d.userid';
        $query .= ' WHERE d.diagnosis_hash = :hash';

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['hash'=>$diagnosis_hash])){
            throw new DbException($sth->errorInfo()[2]);
        }

        $rows = $sth->fetchAll();
        if(empty($rows)){
            throw new NotFoundException('Sorry! No record found for this diagnosis');
        }

        //print_r($rows);
        return $rows;
    }

    public function seekMedicalAdvice($diagnosis_hash){
        $query = 'UPDATE diagnose SET user_request = :req';
        $query .= ' WHERE diagnosis_hash = :hash';

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['hash'=>$diagnosis_hash, 'req'=>'1'])){
            throw new DbException($sth->errorInfo()[2]);
        }
    }

    public function proferMedicalAdvice($diagnosis_hash, $advice){
        $query = 'UPDATE diagnose SET user_request_reply = :reply, l123ftxt = :advice';
        $query .= ' WHERE diagnosis_hash = :hash';

        $sth = $this->db->prepare($query);

        if(!$sth->execute(['hash'=>$diagnosis_hash, 'reply'=>'1', 'advice'=>$advice])){
            throw new DbException($sth->errorInfo()[2]);
        }
    }
}
