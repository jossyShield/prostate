<?php

namespace Bookstore\Models;


use Bookstore\Exceptions\DbException;
use Bookstore\Exceptions\NotFoundException;
use PDO;

class MemberModel extends AbstractModel{

    public function addBiodataInfo(
        $surname, $firstname, $other_name, $gender, $dob,
        $nationality, $state_of_origin, $lga, $home_town, $res_address, $land_mark,
        $res_country, $res_state, $phone_num, $other_num, $whatsapp_num,
        $email, $kin_name, $kin_phone, $kin_relationship, $form_number, $form_state
    ){
        $this->db->beginTransaction();

        //Temporay password hash
        $password_hash = sha1($surname);
        $query = 'INSERT INTO _members (password_hash) VALUES (:password)';
		$sth = $this->db->prepare($query);
		if(!$sth->execute(['password'=> $password_hash])) {
			$this->db->rollBack();
			throw new DbException($sth->errorInfo()[2]);
		}

        $memberId = $this->db->lastInsertId();

        //Insert profile
        $query = 'INSERT INTO _profile (id, surname, first_name, other_name, gender, dob,';
        $query .= ' nationality, state_of_origin, lga, home_town, residential_address, land_mark,';
        $query .= ' residence_country, residence_state, phone_number, other_number, whatsapp_number,';
        $query .= ' email_address, nok_name, nok_phone, nok_relationship)';
        $query .= ' VALUES (:member_id, :sname, :fname, :oname, :gender, :dob,';
        $query .= ' :nation, :s_o_or, :lga, :h_town, :res_address, :l_mark,';
        $query .= ' :res_country, :res_state, :phone_num, :other_num, :whatsapp_num,';
        $query .= ' :email, :kin_name, :kin_phone, :kin_relationship)';

		$sth = $this->db->prepare($query);
        $sth->bindParam('member_id',$memberId);
		$sth->bindParam('sname',$surname);
		$sth->bindParam('fname', $firstname);
        $sth->bindParam('oname', $other_name);
        $sth->bindParam('gender', $gender);
        $sth->bindParam('dob', $dob);
        $sth->bindParam('nation', $nationality);
        $sth->bindParam('s_o_or', $state_of_origin);
        $sth->bindParam('lga', $lga);
        $sth->bindParam('h_town', $home_town);
        $sth->bindParam('res_address', $res_address);
        $sth->bindParam('l_mark', $land_mark);
        $sth->bindParam('res_country', $res_country);
        $sth->bindParam('res_state', $res_state);
        $sth->bindParam('phone_num', $phone_num);
        $sth->bindParam('other_num', $other_num);
        $sth->bindParam('whatsapp_num', $whatsapp_num);
        $sth->bindParam('email', $email);
        $sth->bindParam('kin_name', $kin_name);
        $sth->bindParam('kin_phone', $kin_phone);
        $sth->bindParam('kin_relationship', $kin_relationship);

        if(!$sth->execute()){
            $this->db->rollBack();
			throw new DbException($sth->errorInfo()[2]);
		}

        $query = 'INSERT INTO _form_tracker (id, form_number, form_state) VALUES (:member_id, :form_num, :form_state)';
		$sth = $this->db->prepare($query);
		if(!$sth->execute(['member_id'=> $memberId, 'form_num'=>strtolower($form_number), 'form_state'=>$form_state])) {
			$this->db->rollBack();
			throw new DbException($sth->errorInfo()[2]);
		}

        $this->db->commit();
    }

    public function addFamilyInfo(
        $marital_status, $spouse_name, $maiden_name, $spouse_phone, $spouse_email, $marr_ann, $spouse_bday,
        $spouse_nation, $spouse_state, $spouse_lga, $spouse_h_town, $spouse_occ, $spouse_bus, $spouse_pos,
        $spouse_bus_addr, $spouse_rel_info, $marriage_lgt, $marriage_children, $children_gfcc, $single_parent,
        $spouse_gfcc, $spouse_attend_ch, $spouse_den, $eltnm, $mto,  $mic, $mich, $micd, $micw, $foi,
        $form_number, $form_state
        ){
        $this->db->beginTransaction();
        
        $query = 'SELECT id FROM _form_tracker WHERE form_number = :form_num';
		$sth = $this->db->prepare($query);
		if(!$sth->execute(['form_num'=> $form_number])) {
			$this->db->rollBack();
			throw new DbException($sth->errorInfo()[2]);
		}
        
        $data = $sth->fetchAll();
        $form_id = (object) $data[0];

        //Update _profile table
        $query = 'UPDATE _profile SET marital_status = :marital_status, spouse_name = :sp_name,';
        $query .= ' maiden_name = :maiden_name, spouse_phone_number = :sp_phone, spouse_email = :sp_email,';
        $query .= ' marriage_anniversary_date = :marriage_ann, spouse_birthday = :sp_bday,';
        $query .= ' spouse_nationality = :sp_nation, spouse_state_of_origin = :sp_s_o_or, spouse_lga = :sp_lga,';
        $query .= ' spouse_home_town = :sp_h_twn, spouse_occupation = :sp_occ, spouse_business = :sp_bus,';
        $query .= ' spouse_position_held = :sp_pos_h, spouse_business_address = :sp_bus_addr,';
        $query .= ' spouse_relevant_info = :sp_rel_info, marriage_length = :marriage_lgt, marriage_no_of_children = :mrg_ch,';
        $query .= ' children_in_gfcc = :ch_gfcc, single_parent = :single_pr, spouse_in_gfcc = :sp_gfcc,';
        $query .= ' spouse_attend_church = :sp_church, spouse_denomination = :sp_den, engaged_living_together_not_married = :eltnm,';
        $query .= ' married_traditionally_only = :mto,  married_in_court = :mic, married_in_church = :mich, marriage_in_church_denomination = :micd,';
        $query .= ' marriage_in_church_when = :micw, family_other_info = :foi';
        $query .= ' WHERE id = :member_id';
    
        
		$sth = $this->db->prepare($query);
        $sth->bindParam('marital_status',$marital_status);
        $sth->bindParam('sp_name',$spouse_name);
        $sth->bindParam('maiden_name',$maiden_name);
        $sth->bindParam('sp_phone',$spouse_phone);
        $sth->bindParam('sp_email',$spouse_email);
        $sth->bindParam('marriage_ann',$marr_ann);
        $sth->bindParam('sp_bday',$spouse_bday);
        $sth->bindParam('sp_nation',$spouse_nation);
        $sth->bindParam('sp_s_o_or',$spouse_state);
        $sth->bindParam('sp_lga',$spouse_lga);
        $sth->bindParam('sp_h_twn',$spouse_h_town);
        $sth->bindParam('sp_occ',$spouse_occ);
        $sth->bindParam('sp_bus',$spouse_bus);
        $sth->bindParam('sp_pos_h',$spouse_pos);
        $sth->bindParam('sp_bus_addr',$spouse_bus_addr);
        $sth->bindParam('sp_rel_info',$spouse_rel_info);
        $sth->bindParam('marriage_lgt',$marriage_lgt);
        $sth->bindParam('mrg_ch',$marriage_children);
        $sth->bindParam('ch_gfcc',$children_gfcc);
        $sth->bindParam('single_pr',$single_parent);
        $sth->bindParam('sp_gfcc',$spouse_gfcc);
        $sth->bindParam('sp_church',$spouse_attend_ch);
        $sth->bindParam('sp_den',$spouse_den);
        $sth->bindParam('eltnm',$eltnm);
        $sth->bindParam('mto',$mto);
        $sth->bindParam('mic',$mic);
        $sth->bindParam('mich',$mich);
        $sth->bindParam('micd',$micd);
        $sth->bindParam('micw',$micw);
        $sth->bindParam('foi',$foi);
        $sth->bindParam('member_id',$form_id->id);

		if(!$sth->execute()) {
			$this->db->rollBack();
			throw new DbException($sth->errorInfo()[2]);
		}

        //Update _form_tracker form state
        $query = 'UPDATE _form_tracker SET form_state = :form_st WHERE id = :member_id';

		$sth = $this->db->prepare($query);
        $sth->bindParam('member_id',$form_id->id);
        $sth->bindParam('form_st',$form_state);

        if(!$sth->execute()) {
			$this->db->rollBack();
			throw new DbException($sth->errorInfo()[2]);
		}

        $this->db->commit();
    }

    public function addEducationOccupationInfo(
        $high_aca_qual, $aca_qual_others, $aca_discipline, $personal_occupation, $personal_business,
        $personal_pos_h, $personal_bus_addr,
        $form_state, $form_number
    ){
        $this->db->beginTransaction();

        $query = 'UPDATE _form_tracker SET form_state = :form_st, id = LAST_INSERT_ID(id)';
        $query .= ' WHERE form_number = :form_num';

        $sth = $this->db->prepare($query);
        $sth->bindParam('form_st', $form_state);
        $sth->bindParam('form_num', $form_number);

        if(!$sth->execute()){
            $this->db-rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }
        
        $form_id = $this->db->lastInsertId();

        //Update profile
        $query = 'UPDATE _profile SET highest_academic_qualification = :high_aca_qual,';
        $query .= ' academic_qualification_others = :aca_qual_others, academic_discipline = :aca_disc,';
        $query .= ' personal_occupation = :pers_occ, personal_business = :pers_bus, personal_position_held = :pers_pos_h,';
        $query .= ' personal_business_address = :pers_bus_addr';
        $query .= ' WHERE id = :member_id';

        $sth = $this->db->prepare($query);
        $sth->bindParam('high_aca_qual', $high_aca_qual);
        $sth->bindParam('aca_qual_others', $aca_qual_others);
        $sth->bindParam('aca_disc', $aca_discipline);
        $sth->bindParam('pers_occ', $personal_occupation);
        $sth->bindParam('pers_bus', $personal_business);
        $sth->bindParam('pers_pos_h', $personal_pos_h);
        $sth->bindParam('pers_bus_addr', $personal_bus_addr);
        $sth->bindParam('member_id', $form_id);

        if(!$sth->execute()){
            $this->db->rollback();
            throw new DbException($sth->errorInfo()[2]);
        }

        $this->db->commit();
    }

    public function addChristianExperienceInfo(
        $denom1_name, $denom1_from, $denom1_to, $denom1_pos, $denom1_min, $denom2_name, $denim2_from, $denom2_to,
        $denom2_pos, $denom2_min, $denom3_name, $denom3_from, $denom3_to, $denom3_pos, $denom3_min,
        $fam_ch_which, $bap_water, $bap_water_when, $bap_water_where, $bap_spirit, $bap_spirit_when,
        $bap_spirit_where, $passion, $church_exp, $sp_skills,
        $form_state, $form_number
    ){

        $this->db->beginTransaction();

        $query = 'UPDATE _form_tracker SET form_state = :form_st, id = LAST_INSERT_ID(id)';
        $query .= ' WHERE form_number = :form_num';

        $sth = $this->db->prepare($query);
        $sth->bindParam('form_st', $form_state);
        $sth->bindParam('form_num', $form_number);

        if(!$sth->execute()){
            $this->db-rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }
        
        $form_id = $this->db->lastInsertId();

        $query = 'UPDATE _profile SET denomination1_name = :denom1_name, denomination1_from = :denom1_from,';
        $query .= ' denomination1_to = :denom1_to, denomination1_position = :denom1_pos, denomination1_ministerial_position = :denom1_min,';
        $query .= ' denomination2_name = :denom2_name, denomination2_from = :denom2_from, denomination2_to = :denom2_to,';
        $query .= ' denomination2_position = :denom2_pos, denomination2_ministerial_position = :denom2_min,';
        $query .= ' denomination3_name = :denom3_name, denomination3_from = :denom3_from, denomination3_to = :denom3_to,';
        $query .= ' denomination3_position = :denom3_pos, denomination3_ministerial_position = :denom3_min,';
        $query .= ' family_church_which = :fam_ch_which, baptized_in_water = :bap_water, baptized_in_water_when = :bap_water_when,';
        $query .= ' baptized_in_water_where = :bap_water_where, baptized_in_holy_spirit = :bap_spirit,';
        $query .= ' baptized_in_holy_spirit_when = :bap_spirit_when, baptized_in_holy_spirit_where = :bap_spirit_where,';
        $query .= ' passion = :passion,special_skills = :sp_skills, church_family_expectation = :church_exp';
        $query .= ' WHERE id = :member_id';

        $sth = $this->db->prepare($query);
        $sth->bindParam('denom1_name', $denom1_name);
        $sth->bindParam('denom1_from', $denom1_from);
        $sth->bindParam('denom1_to', $denom1_to);
        $sth->bindParam('denom1_pos', $denom1_pos);
        $sth->bindParam('denom1_min', $denom1_min);
        $sth->bindParam('denom2_name', $denom2_name);
        $sth->bindParam('denom2_from', $denom2_from);
        $sth->bindParam('denom2_to', $denom2_to);
        $sth->bindParam('denom2_pos', $denom2_pos);
        $sth->bindParam('denom2_min', $denom2_min);
        $sth->bindParam('denom3_name', $denom3_name);
        $sth->bindParam('denom3_from', $denom3_from);
        $sth->bindParam('denom3_to', $denom3_to);
        $sth->bindParam('denom3_pos', $denom3_pos);
        $sth->bindParam('denom3_min', $denom3_min);
        $sth->bindParam('fam_ch_which', $fam_ch_which);
        $sth->bindParam('bap_water', $bap_water);
        $sth->bindParam('bap_water_when', $bap_water_when);
        $sth->bindParam('bap_water_where', $bap_water_where);
        $sth->bindParam('bap_spirit', $bap_spirit);
        $sth->bindParam('bap_spirit_when', $bap_spirit_when);
        $sth->bindParam('bap_spirit_where', $bap_spirit_where);
        $sth->bindParam('passion', $passion);
        $sth->bindParam('sp_skills', $sp_skills);
        $sth->bindParam('church_exp', $church_exp);
        $sth->bindParam('member_id', $form_id);
        

        if(!$sth->execute()){
            $this->db->rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }

        $this->db->commit();
    }

    public function addOfficialAffirmationInfo(
        $user_reg_date, $official_rem, $user_affirm, $user_photo,
        $form_state, $form_number
    ){
        $this->db->beginTransaction();

        $query = 'UPDATE _form_tracker SET form_state = :form_st, id = LAST_INSERT_ID(id)';
        $query .= ' WHERE form_number = :form_num';

        $sth = $this->db->prepare($query);
        $sth->bindParam('form_st', $form_state);
        $sth->bindParam('form_num', $form_number);

        if(!$sth->execute()){
            $this->db->rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }
        
        $form_id = $this->db->lastInsertId();

        $query = 'UPDATE _profile SET user_registration_date = :user_reg_date, official_remark = :official_rem,';
        $query .= ' user_affirmation = :user_affirm, user_photo = :user_photo';
        $query .= ' WHERE id = :member_id';

        $sth = $this->db->prepare($query);
        $sth->bindParam('user_reg_date', $user_reg_date);
        $sth->bindParam('official_rem', $official_rem);
        $sth->bindParam('user_affirm', $user_affirm);
        $sth->bindParam('user_photo', $user_photo);
        $sth->bindParam('member_id', $form_id);

        if(!$sth->execute()){
            $this->db->rollBack();
            throw new DbException($sth->errorInfo()[2]);
        }

        $this->db->commit();
    }

    public function getStateByFormNumber(string $form_number): string{
        $query = 'SELECT form_state FROM _form_tracker WHERE form_number = :member_id';
		$sth = $this->db->prepare($query);
		if(!$sth->execute(['member_id' => strtolower($form_number)])){
            throw new DbException($sth->errorInfo()[2]);
        }
        
        $row = $sth->fetchAll();
		if(empty($row)){
			throw new NotFoundException();
		}
        $data = (object) $row[0];
        return $data->form_state;
    }

    public function getMembers(){
        $query = 'SELECT *, DATE_FORMAT(dob, :date_format) AS birthday,';
        $query .= ' CONCAT(UCASE(LEFT(surname,1)), SUBSTRING(surname, 2)) AS surname,';
        $query .= ' CONCAT(UCASE(LEFT(first_name,1)), SUBSTRING(first_name, 2)) AS firstname,';
        $query .= ' CONCAT(UCASE(LEFT(other_name,1)), SUBSTRING(other_name, 2)) AS othername,';
        $query .= ' CONCAT(UCASE(LEFT(gender,1)), SUBSTRING(gender, 2)) AS gender,';
        $query .= ' CONCAT(UCASE(LEFT(marital_status,1)), SUBSTRING(marital_status, 2)) AS married,';
        $query .= ' CONCAT(UCASE(LEFT(highest_academic_qualification,1)), SUBSTRING(highest_academic_qualification, 2)) AS academics,';
        $query .= ' user_photo AS photo,';
        $query .= ' CONCAT(UCASE(LEFT(ft.form_number ,1)), SUBSTRING(ft.form_number, 2)) AS form_number';
        $query .= ' FROM _profile p';
        $query .= ' LEFT JOIN _form_tracker ft ON ft.id = p.id';

        $sth = $this->db->prepare($query);
        if(!$sth->execute(['date_format'=>'%b %D'])){
            throw new DbException($sth->errorInfo()[2]);
        }
         $rows = $sth->fetchAll();

         if(empty($rows)){
            throw new NotFoundException();
         }

         return $rows;
    }

    //Dashboard Monitoring count
    public function get(){
        $query = 'SELECT COUNT(*) AS members FROM _members';
        $query .= ' UNION';
        $query .= ' SELECT COUNT(*) AS incorporated FROM _form_tracker';
        $query .= ' UNION';
        $query .= ' SELECT COUNT(*) AS unverified FROM _form_tracker WHERE form_state <> :state';

        $sth = $this->db->prepare($query);
        if(!$sth->execute(['state'=>'official_affirmation_info'])){
            throw new DbException($sth->errorInfo()[2]);
        }

        $row = $sth->fetchAll();
        //print_r($row);
        return $row;
    }

    public function getMembersInIncorporation(){
        $query = 'SELECT p.id,';
        $query .= ' CONCAT(UCASE(LEFT(surname,1)), SUBSTRING(surname, 2)) AS surname,';
        $query .= ' CONCAT(UCASE(LEFT(first_name,1)), SUBSTRING(first_name, 2)) AS firstname,';
        $query .= ' CONCAT(UCASE(LEFT(other_name,1)), SUBSTRING(other_name, 2)) AS othername,';
        $query .= ' CONCAT(UCASE(LEFT(gender,1)), SUBSTRING(gender, 2)) AS gender,';
        $query .= ' user_photo AS photo';
        $query .= ' FROM _form_tracker ft';
        $query .= ' LEFT JOIN _profile p ON p.id = ft.id';

        $sth = $this->db->prepare($query);

        if(!$sth->execute()){
            throw new DbException($sth->errorInfo()[2]);
        }
        $rows = $sth->fetchAll();
        
         if(empty($rows)){
            throw new NotFoundException();
         }

         return $rows;
    }
}