<?php
class Realestate_model extends CI_Model{


	public function __construct(){
		parent::__construct();
	}

	public function validateCustomer(){
		$userId = $this->input->post('userid');
		$passwd = $this->input->post('password');

		if ( !isset($userId) || !isset($passwd) ){
			return false;
		}

		$whereClause = array( 'CUSTOMER_ID'=>$userId, 'PASSWORD'=>$passwd );
		$userQuery   = $this->db->get_where(TBL_PREFIX.TBL_CUSTOMER_DETAILS, $whereClause);
		log_message('debug', 'Jv_model: validateCustomer: ['.$this->db->last_query().']');
		if ( isset( $userQuery ) && $userQuery->num_rows() > 0 ){
			$row = $userQuery->row();
			$this->session->set_userdata('is_logged_in', true);
			$this->session->set_userdata('userid', $userId);
			return true;
		}else{
                        $this->session->set_userdata('is_logged_in', false);
			return false;
		}
	}

	public function setNewForm(){

		$this->session->set_userdata('last_form_id', 'NEW');
		
			return true;
		
	}

	public function setFormSession($formId){

		$this->session->set_userdata('last_form_id', $formId);
		
			return true;
		
	}

	public function getBasicProfile(){
		$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'));
		$userDataQuery = $this->db->get_where(TBL_PREFIX.TBL_CUSTOMER_DETAILS, $whereClause);
		log_message('debug', 'Jv_model: getBasicProfile: ['.$this->db->last_query().']');
		if( isset( $userDataQuery ) && $userDataQuery->num_rows() > 0 ){
			$row = $userDataQuery->row();
                        $data = array(
                                        'customer_id' => $row->customer_id,
                                        'password' => $row->password,
                                        'name' => $row->name,
					'company_name' => $row->company_name,
					'address' => $row->address,
					'country' => $row->country,
					'state' => $row->state,
					'city' => $row->city,
					'postal_code' => $row->postal_code,
					'contact_number' => $row->contact_number,
					'alternate_email' => $row->alternate_email,
					'website' => $row->website,
					'register_date' => $row->register_date,
					'last_update_date' => $row->last_update_date,
					'expiry_date' => $row->expiry_date
                                     );
			return $data;
		}else{
			return false;
		}

	}

	public function getAssignedEmployee(){
		//select emp.name, emp.contact_no, emp.email from bd_employee emp, bd_customer_responsibility cr, bd_customer_details cd where cd.customer_id = 'gaurav1@gmail.com' AND cr.customer_id = cd.id and emp.id = cr.assigned_to;
		$resultData = $this->db->query("select emp.name, emp.contact_no, emp.email from ".TBL_PREFIX.TBL_EMPLOYEE." emp, ".TBL_PREFIX.TBL_CUSTOMER_RESPONSIBILITY." cr, ".TBL_PREFIX.TBL_CUSTOMER_DETAILS." cd where cd.customer_id = '".$this->session->userdata('userid')."' AND cr.customer_id = cd.id and emp.id = cr.assigned_to");
		log_message('debug', 'Jv_model: getAssignedEmployee: ['.$this->db->last_query().']');
		if ( $resultData->num_rows() > 0 ){
			return $resultData->result();
		}else{
			return null;
		}
	}
	public function currentPackage( $formId = NULL ){
		log_message('debug', 'Form id ['.$formId.']');
		$userPackage = null;
		$whereClause = null;
		$i = 0;
		if( $formId == NULL ){
			//$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'),'form_id'=>$formId);
			$whereClause = array('form_id'=>$formId);
		}else{
			//$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'), 'form_id'=>$formId);
			$whereClause = array('form_id'=>$formId);
		}
		$packQuery = $this->db->get_where(TBL_PREFIX.TBL_CUSTOMER_PACKAGE, $whereClause);
		log_message('debug', 'Jv_model: currentPackage:  ['.$this->db->last_query().']');
		if( isset( $packQuery ) && $packQuery->num_rows() > 0 ){
			foreach($packQuery->result() as $row){
				$userPackage[$i]['package_id'] = $row->package_id;
				$userPackage[$i]['form_id']    = $row->form_id;
				$i++;
			}
			return $userPackage;
		}else{
			return NULL;
		}
	}


	


	public function currentAdditionalServices( $formId = NULL ){
		log_message('debug', 'Form id ['.$formId.']');
		$additionalServices = null;
		$whereClause = null;
		$i = 0;
		if( $formId == NULL ){
			//$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'),'form_id'=>$formId);
			$whereClause = array('form_id'=>$formId);
		}else{
			//$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'), 'form_id'=>$formId);
			$whereClause = array('form_id'=>$formId);
		}
		$additionalSerQuery = $this->db->get_where(TBL_PREFIX.TBL_CUSTOMER_ADDITIONAL_SERVICES, $whereClause);
		log_message('debug', 'Jv_model: currentAdditionalServices: ['.$this->db->last_query().']');
		if( isset( $additionalSerQuery ) && $additionalSerQuery->num_rows() > 0 ){
			foreach($additionalSerQuery->result() as $row){
				$additionalServices[$i]['feature_listing'] = $row->feature_listing;
				$additionalServices[$i]['memorandum']      = $row->memorandum;
				$additionalServices[$i]['plan']            = $row->plan;
				$additionalServices[$i]['valuation']       = $row->valuation;
				$additionalServices[$i]['mandate']         = $row->mandate;
				$additionalServices[$i]['advisory']        = $row->advisory;
				$additionalServices[$i]['pre_sales']       = $row->pre_sales;

				$additionalServices[$i]['form_id']         = $row->form_id;
				$i++;
			}
			return $additionalServices;
		}else{
			return NULL;
		}
	}



	public function registerUser(){
		$email    = $this->input->post('email');
		$passwd   = $this->input->post('passwd');
		$uname    = $this->input->post('name');
		$compname = $this->input->post('compname');
		$address  = $this->input->post('address');
		$country  = $this->input->post('country');
		$state    = $this->input->post('state');
		$city     = $this->input->post('city');
		$pcode    = $this->input->post('pcode');
		$contact  = $this->input->post('contact');
		$aemail   = $this->input->post('aemail');
		$web      = $this->input->post('web');

		$userData = array('customer_id'=>$email, 'password'=>$passwd, 'name'=>$uname, 'company_name'=>$compname, 'address'=>$address, 'country'=>$country, 'state'=>$state, 'city'=>$city, 'postal_code'=>$pcode, 'contact_number'=>$contact, 'alternate_email'=>$aemail, 'website'=>$web);

		$result = $this->db->insert(TBL_PREFIX.TBL_CUSTOMER_DETAILS, $userData);
		log_message('debug', 'Jv_model: registerUser: ['.$this->db->last_query().']');
		if ( !$result && $this->db->error()['code'] == 1062 ){
			return "duplicate";
		}else{
			if ( $this->db->affected_rows() > 0 ){
				return "true";
			}else{
				return "false";
			}
		}
	}
	public function editUserProfile(){
		$email    = $this->input->post('email');
		$passwd   = $this->input->post('passwd');
		$uname    = $this->input->post('name');
		$compname = $this->input->post('compname');
		$address  = $this->input->post('address');
		$country  = $this->input->post('country');
		$state    = $this->input->post('state');
		$city     = $this->input->post('city');
		$pcode    = $this->input->post('pcode');
		$contact  = $this->input->post('contact');
		$aemail   = $this->input->post('aemail');
		$web      = $this->input->post('web');
		$userData = array('password'=>$passwd, 'name'=>$uname, 'company_name'=>$compname, 'address'=>$address, 'country'=>$country, 'state'=>$state, 'city'=>$city, 'postal_code'=>$pcode, 'contact_number'=>$contact, 'alternate_email'=>$aemail, 'website'=>$web);
		//$result = $this->db->insert(TBL_PREFIX.TBL_CUSTOMER_DETAILS, $userData);
		$this->db->where('customer_id',$this->session->userdata('userid', $userId));
		//$this->db->update(TBL_PREFIX.TBL_CUSTOMER_DETAILS, $userData);
		if($this->db->update(TBL_PREFIX.TBL_CUSTOMER_DETAILS, $userData)){
			log_message('debug', 'User_model: registerUser: ['.$this->db->last_query().']');
			return "true";
		}else{
				return "false";
		}
	}

	public function registerBusinessUser( $actType="NEW" ){
		/*$business_type    = $this->input->post('business_type');
		$uname            = $this->input->post('lb_uname');
		$email            = $this->input->post('lb_email');
		$contact          = $this->input->post('lb_mobile');
		$address          = $this->input->post('lb_address');
		$location         = $this->input->post('lb_location');
		$compname         = $this->input->post('lb_company');
		$designation      = $this->input->post('lb_designation');
		$web              = $this->input->post('lb_web');
		$isOwner          = $this->input->post('lb_owner');
		$isBroker         = $this->input->post('lb_broker');
		$isRepresentative = $this->input->post('lb_representative');
		$aemail           = $this->input->post('lb_aemail');
		$acontact         = $this->input->post('lb_aphone');

		$who = $isOwner;
		if( isset($isOwner) ){
			$who = $isOwner;
		}else if( isset($isBroker) ){
			$who = $isBroker;
		}else if( isset( $isRepresentative ) ){
			$who = $isRepresentative;
		}*/
		$keyHeadline            = $this->input->post('keyHeadline');
		$businessDesc          = $this->input->post('bus_desc');
		$businessCountry       = $this->input->post('country');
		$businessRegion        = $this->input->post('region');
		$businessState         = $this->input->post('state');
		$businessCity          = $this->input->post('city');
		$businessCategory      = $this->input->post('category');
		$businessSubCategory   = $this->input->post('subcategory');
		$legalEntity           = $this->input->post('legal');
		$establishmentYear     = $this->input->post('eyear');
		$currentBusinessStatus = $this->input->post('bstatus');
		$other_state           = $this->input->post('other_state');
		if($this->input->post('other_city')!=''){
		
	    $other_city     = $this->input->post('other_city');
		
		
		}
		if($this->input->post('other_city_2')!=''){
			
		$other_city     = $this->input->post('other_city_2');

		
		}
		$property_type         = $this->input->post('property_type');
		$saleable_area         = $this->input->post('saleable_area');
		$age_construction         = $this->input->post('age_construction');
		$authority_approval         = $this->input->post('authority_approval');
		
		//select customer details
			$this->db->select('*');
			$this->db->where(array('customer_id'=>$this->session->userdata('userid')));
			$q = $this->db->get(TBL_PREFIX.TBL_CUSTOMER_DETAILS);
			$data = $q->result_array();
			
			
			
			$name     = $data[0]['name'];
			$contact_number     = $data[0]['contact_number'];
			$address     = $data[0]['address'];
			$location     = $data[0]['location'];
			$company_name     = $data[0]['company_name'];

		$userData = array('customer_id'=>$this->session->userdata('userid'),'business_type'=>'re_business','name'=>$name,'email'=>$this->session->userdata('userid'),'contact'=>$contact_number,'address'=>$address,'location'=>$location,'company_name'=>$company_name,'key_headline'=>$keyHeadline,'description'=>$businessDesc,'location_country'=>$businessCountry,'location_region'=>$businessRegion,'location_state'=>$businessState,'location_city'=>$businessCity,'property_type_old'=>$property_type,'saleable_area'=>$saleable_area,'age_construction'=>$age_construction,'authority_approval'=>$authority_approval,'other_state'=>$other_state,'other_city'=>$other_city);

		//$userData = array('business_type'=>$business_type,'name'=>$uname, 'email'=>$email, 'contact'=>$contact, 'address'=>$address, 'location'=>$location, 'company_name'=>$compname, 'designation'=>$designation, 'website'=>$web, 'who'=>$who, 'alternative_email'=>$aemail, 'alternative_contact'=>$acontact);
		
		if( strcasecmp( $actType, "NEW" ) == 0 ){
			//$userData['customer_id'] = $this->session->userdata('userid');
			$result = $this->db->insert(TBL_PREFIX.TBL_BUSINESS_DETAILS, $userData);
			log_message('debug', 'RE_model: registerBusinessUser: ['.$this->db->last_query().']');
			if ( !$result && $this->db->error()['code'] == 1062 ){
				return array("duplicate", null);
			}else{
				$lastInsertedId = $this->db->insert_id();
				if ( $this->db->affected_rows() > 0 ){
					$this->session->set_userdata('last_form_id', $lastInsertedId);
					$userData2 = array('form_id'=>$this->session->userdata('last_form_id'),'added_date'=>date('Y-m-d'));
					$result = $this->db->insert(TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS, $userData2);
					log_message('debug', 'RE_model: registerBusinessUser: ['.$this->db->last_query().']');
					return array(true, $this->session->userdata('last_form_id'));
				}else{
					return array(false, null);
				}
			}
		}else{
			$lastInsertedId = $this->input->post('frm_id');
			//$this->db->where('customer_id', $this->session->userdata('userid'));
			$userData = array('form_id'=>$this->session->userdata('last_form_id'));
			$this->db->where('form_id',$lastInsertedId);
			if($this->db->update(TBL_PREFIX.TBL_BUSINESS_DETAILS, $userData)){
				log_message('debug', 'Jv_model: registerBusinessUser: ['.$this->db->last_query().']');
				$this->session->set_userdata('last_form_id', $lastInsertedId);
				//$userData = array('form_id'=>$this->session->userdata('last_form_id'));
				//$result = $this->db->insert(TBL_PREFIX.TBL_SELL_BUSINESS_DETAILS, $userData);
				//log_message('debug', 'User_model: registerBusinessUser: ['.$this->db->last_query().']');
				return array(true, $lastInsertedId);
			}else{
				return array(true, $lastInsertedId);
			}
		}

	}

	public function getBusinessUserProfile( $businessType ){
		$whereClause = null;
		$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'));
		$this->db->select(TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS.'.form_id,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.key_headline,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.description,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.description,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.property_type_old,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.location_country,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.location_state,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.location_city,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.saleable_area,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.age_construction,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.authority_approval,'.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.location_region');
		//,,,,age_construction,authority_approval
		if( strcasecmp( $businessType, 'real_estate' ) == 0 ){
			$this->db->from(TBL_PREFIX.TBL_BUSINESS_DETAILS);
			$this->db->join(TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS, TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id = '.TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS.'.form_id','left');
		}	
		//$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.CUSTOMER_ID', $this->session->userdata('userid'));
		
		$this->db->where('CUSTOMER_ID',$this->session->userdata('userid'));
		$this->db->where('BUSINESS_TYPE','re_business');
		$this->db->order_by('form_id',"DESC");
		$this->db->limit(1);

		$userBusinessData = $this->db->get();
		log_message('debug', 'RE_model: getBusinessUserProfile: ['.$this->db->last_query().']');
		if( !$userBusinessData ){
			return null;
		}else{
			$dataToReturn = $userBusinessData->result_array();
			$this->session->set_userdata('last_form_id', $dataToReturn[0]['form_id']);
			return $dataToReturn;
		}

	}

	public function registerBusinessBasicInformation(){
		$keyHeadline            = $this->input->post('keyHeadline');
		$businessDesc          = $this->input->post('bus_desc');
		$businessCountry       = $this->input->post('country');
		$businessRegion        = $this->input->post('region');
		$businessState         = $this->input->post('state');
		$businessCity          = $this->input->post('city');
		$property_type      = $this->input->post('property_type');
		$saleable_area   = $this->input->post('saleable_area');
		$age_construction           = $this->input->post('age_construction');
		$authority_approval     = $this->input->post('authority_approval');
		$other_state           = $this->input->post('other_state');
		if($this->input->post('other_city')!=''){
		
	    $other_city     = $this->input->post('other_city');
		
		
		}
		if($this->input->post('other_city_2')!=''){
			
		$other_city     = $this->input->post('other_city_2');

		
		}
		
		//select customer details
			$this->db->select('*');
			$this->db->where(array('customer_id'=>$this->session->userdata('userid')));
			$q = $this->db->get(TBL_PREFIX.TBL_CUSTOMER_DETAILS);
			$data = $q->result_array();
			
			
			
			$name     = $data[0]['name'];
			$contact_number     = $data[0]['contact_number'];
			$address     = $data[0]['address'];
			$location     = $data[0]['location'];
			$company_name     = $data[0]['company_name'];

		$userData = array('business_type'=>'re_business','name'=>$name,'email'=>$this->session->userdata('userid'),'contact'=>$contact_number,'address'=>$address,'location'=>$location,'company_name'=>$company_name,'key_headline'=>$keyHeadline,'description'=>$businessDesc,'location_country'=>$businessCountry,'location_region'=>$businessRegion,'location_state'=>$businessState,'location_city'=>$businessCity,'property_type_old'=>$property_type,'saleable_area'=>$saleable_area,'age_construction'=>$age_construction,'authority_approval'=>$authority_approval,'other_state'=>$other_state,'other_city'=>$other_city);

		$result = $this->db->update( TBL_PREFIX.TBL_BUSINESS_DETAILS, $userData, array( 'customer_id' => $this->session->userdata('userid'),'form_id' => $this->session->userdata('last_form_id') ) );
		log_message('debug', 'RE_model: registerBusinessBasicInformation: ['.$this->db->last_query().']');
		if( !$result ){
			return "error";
		}else{
			if( $this->db->affected_rows() > 0 ){
				return true;
			}else{
				return true;
			}
		}
	}

	public function registerSellerBusinessDescription( $actType = "NEW" ){
		$tenancy_details         = $this->input->post('tenancy_details');
		$lease_period       = $this->input->post('lease_period');
		$lease_start             = $this->input->post('lease_start');
		$lease_end = $this->input->post('lease_end');
		$lock_period      = $this->input->post('lock_period');
		$security_received       = $this->input->post('security_received');
		$monthly_rental             = $this->input->post('monthly_rental');
		$annual_rental             = $this->input->post('annual_rental');
		$annual_maintenance        = $this->input->post('annual_maintenance');
		$escalation_after            = $this->input->post('escalation_after');
		$escalation_percent     = $this->input->post('escalation_percent');
		

		$userData = array('tenancy_details'=>$tenancy_details, 'lease_period'=>$lease_period, 'lease_start'=>$lease_start,
		'lease_end'=>$lease_end, 'lock_period'=>$lock_period, 'security_received'=>$security_received, 'monthly_rental'=>$monthly_rental,
		'annual_rental'=>$annual_rental,'annual_maintenance'=>$annual_maintenance, 'escalation_after'=>$escalation_after, 
		'escalation_percent'=>$escalation_percent);

		$result = $this->db->update( TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id') ) );
		log_message('debug', 'RE_model: registerSellerBusinessDescription  ['.$this->db->last_query().']');
		if( !$result ){
			return "error";
		}else{
			if( $this->db->affected_rows() > 0 ){
				return "true";
			}else{
				return "true";
			}
		}
	}

	
	public function registerSellerBusinessDetails( $actType = "NEW" ){
		$features         = $this->input->post('features');
		$location_advantages       = $this->input->post('location_advantages');
		$reason_sale             = $this->input->post('reason_sale');
		$roi_present = $this->input->post('roi_present');
		$roi_escalation      = $this->input->post('roi_escalation');
		$other_income       = $this->input->post('other_income');
		$price_currency             = $this->input->post('price_currency');
		$price_value             = $this->input->post('price_value');
		$price_unit        = $this->input->post('price_unit');
		
		

		$userData = array('features'=>$features, 'location_advantages'=>$location_advantages, 'reason_sale'=>$reason_sale,
		'roi_present'=>$roi_present, 'roi_escalation'=>$roi_escalation, 'other_income'=>$other_income, 'price_currency'=>$price_currency,
		'price_value'=>$price_value,'price_unit'=>$price_unit);
		
		$result = $this->db->update( TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id') ) );
		log_message('debug', 'RE_model: registerSellerBusinessDetails  ['.$this->db->last_query().']');
		if( !$result ){
			return "error";
		}else{
			if( $this->db->affected_rows() > 0 ){
				return "true";
			}else{
				return "true";
			}
		}
		
	}
	
	
	
	
	public function getSellerBusinessDescription(){
		$whereClause = null;
		$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'));
		$this->db->select('*');
		$this->db->from(TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS);
		$this->db->join(TBL_PREFIX.TBL_BUSINESS_DETAILS, TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id = '.TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS.'.form_id');
		$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.CUSTOMER_ID', $this->session->userdata('userid'));
		$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.FORM_ID', $this->session->userdata('last_form_id'));
		$userJvBusinessData = $this->db->get();
		log_message('debug', 'RE_model: getSellerBusinessDescription: ['.$this->db->last_query().']');
		if( !$userJvBusinessData ){
			return null;
		}else{
			return $userJvBusinessData->result_array();
		}
	}
	
	public function getSellerAdditionalDetails(){
		$whereClause = null;
		$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'));
		$this->db->select('*');
		$this->db->from(TBL_PREFIX.TBL_ADDITIONAL_DETAILS);
		$this->db->join(TBL_PREFIX.TBL_BUSINESS_DETAILS, TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id = '.TBL_PREFIX.TBL_ADDITIONAL_DETAILS.'.form_id');
		//$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.CUSTOMER_ID', $this->session->userdata('userid'));
		$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.FORM_ID', $this->session->userdata('last_form_id'));
		$userSellBusinessData = $this->db->get();
		log_message('debug', 'JV_model: getSellerAdditionalDetails: ['.$this->db->last_query().']');
		
		if( !$userSellBusinessData ){
			return null;
		}else{
			return $userSellBusinessData->result_array();
		}
	}


	public function getSellerAdditionalFinancialDetails(){
		$whereClause = null;
		$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'));
		$this->db->select('*');
		$this->db->from(TBL_PREFIX.TBL_ADDITIONAL_FINANCIAL_DETAILS);
		$this->db->join(TBL_PREFIX.TBL_BUSINESS_DETAILS, TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id = '.TBL_PREFIX.TBL_ADDITIONAL_FINANCIAL_DETAILS.'.form_id');
		//$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.CUSTOMER_ID', $this->session->userdata('userid'));
		$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.FORM_ID', $this->session->userdata('last_form_id'));
		$userSellBusinessData = $this->db->get();
		log_message('debug', 'JV_model: getSellerAdditionalFinancialDetails: ['.$this->db->last_query().']');
		
		if( !$userSellBusinessData ){
			return null;
		}else{
			return $userSellBusinessData->result_array();
		}
	}
	

	public function getSellerJvDetails(){
		$whereClause = null;
		$whereClause = array('CUSTOMER_ID'=>$this->session->userdata('userid'));
		$this->db->select('*');
		$this->db->from(TBL_PREFIX.TBL_JV_BUSINESS_DETAILS);
		$this->db->join(TBL_PREFIX.TBL_BUSINESS_DETAILS, TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id = '.TBL_PREFIX.TBL_JV_BUSINESS_DETAILS.'.form_id');
		//$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.CUSTOMER_ID', $this->session->userdata('userid'));
		$this->db->where(TBL_PREFIX.TBL_BUSINESS_DETAILS.'.FORM_ID', $this->session->userdata('last_form_id'));
		$userSellBusinessData = $this->db->get();
		log_message('debug', 'Jv_model: getSellerJvDetails ['.$this->db->last_query().']');
		if( !$userSellBusinessData ){
			return null;
		}else{
			return $userSellBusinessData->result_array();
		}
	}

	public function registerSellerJvDetails(){
		$purpose_jv         = $this->input->post('purposeJv');
		$nature_jv          = $this->input->post('natureJv');
		$prev_investment    = $this->input->post('previous_investment');
		$reqd_investment    = $this->input->post('investment_required');
		$invest_range       = $this->input->post('investment_range');
		$role_new_investo   = $this->input->post('role');
		$proposed_deal      = $this->input->post('purpose_deal');
		

		$userData = array('purpose_jv'=>$purpose_jv,'nature_jv'=>$nature_jv, 'prev_investment'=>$prev_investment, 'reqd_investment'=>$reqd_investment, 'invest_range'=>$invest_range, 'role_new_investo'=>$role_new_investo, 'proposed_deal'=>$proposed_deal);

		$result = $this->db->update( TBL_PREFIX.TBL_JV_BUSINESS_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id') ) );
		log_message('debug', 'Jv_model: registerSellerJvDetails  ['.$this->db->last_query().']');
		if( !$result ){
			return "error";
		}else{
			if( $this->db->affected_rows() > 0 ){
				return "true";
			}else{
				return "false";
			}
		}
	}


	public function registerSellerAdditionalInformation(){
		log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::Yahoo');
		$response = "true";
		$dataArr = array();
		$count = $this->input->post('count');
		for( $i=0; $i < $count; $i++){
			$val = $i+1;
			log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::In Count loop'.$val);
			$dataArr[$i]['property_id']         = $this->input->post('property_id_'.$val); 

			$dataArr[$i]['property_type']         = $this->input->post('property_type_'.$val);
			log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::In Count loop'.$this->input->post('property_type_'.$val));
			$dataArr[$i]['real_state_value_unit']      = $this->input->post('real_state_value_unit_'.$val);
			$dataArr[$i]['real_state_value']      = $this->input->post('real_state_value_'.$val);
			//$dataArr[$i]['real_state_value_type']      = $this->input->post('real_state_value_type_'.$val);
			$dataArr[$i]['facility_desc']         = $this->input->post('facility_desc_'.$val);
			$dataArr[$i]['industrial_commercial'] = $this->input->post('industrial_commercial_'.$val);
			$dataArr[$i]['total_land_area_type']  = $this->input->post('total_land_area_type_'.$val);
			$dataArr[$i]['total_land_area']       = $this->input->post('total_land_area_'.$val);
			$dataArr[$i]['built_up_area_type']    = $this->input->post('built_up_area_type_'.$val);
			$dataArr[$i]['built_up_area']         = $this->input->post('built_up_area_'.$val);
			$dataArr[$i]['open_area_type']        = $this->input->post('open_area_type_'.$val);
			$dataArr[$i]['open_area']             = $this->input->post('open_area_'.$val);
			$dataArr[$i]['other_area']            = $this->input->post('other_area_'.$val);
			/*dataArr[$i]['fin_year']              = $this->input->post('fin_year');
			$fin_revenue_turnover  = $this->input->post('fin_revenue_turnover');
			$fin_EDITDA            = $this->input->post('fin_EDITDA');
			$fin_PAT               = $this->input->post('fin_PAT');
			$fin_margin            = $this->input->post('fin_margin');
			$fin_yearly            = $this->input->post('fin_yearly');*/
		}

		$val = 0;
		$dataArrFin = array();
		$count_fin = $this->input->post('count-fin');
		for( $ii=0; $ii < $count_fin; $ii++){
			$val = $ii+1;
			log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::In Count loop'.$val);
			$dataArrFin[$ii]['finance_id']           = $this->input->post('finance_id_'.$val);
			$dataArrFin[$ii]['fin_year']             = $this->input->post('fin_year_'.$val);
			$dataArrFin[$ii]['fin_revenue_turnover'] = $this->input->post('fin_revenue_turnover_'.$val);
			$dataArrFin[$ii]['fin_EDITDA']           = $this->input->post('fin_EDITDA_'.$val);
			$dataArrFin[$ii]['fin_PAT']              = $this->input->post('fin_PAT_'.$val);
			$dataArrFin[$ii]['fin_margin']           = $this->input->post('fin_margin_'.$val);
			//$dataArrFin[$ii]['fin_yearly']           = $this->input->post('fin_yearly_'.$val);



		}




		log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::Yahoo:::: after loop');
		for($j=0; $j < $count; $j++){
			log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::Yahoo::::: in loop again');
			if(($dataArr[$j]['property_id']==NULL)){
				log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::Yahoo:::::: property not set');
				$userData = array('form_id'=>$this->session->userdata('last_form_id'),'property_type'=>$dataArr[$j]['property_type'],'real_state_value_unit'=>$dataArr[$j]['real_state_value_unit'],'real_state_value'=>$dataArr[$j]['real_state_value'], 'facility_desc'=>$dataArr[$j]['facility_desc'], 'industrial_commercial'=>$dataArr[$j]['industrial_commercial'], 'total_land_area_type'=>$dataArr[$j]['total_land_area_type'], 'total_land_area'=>$dataArr[$j]['total_land_area'], 'built_up_area_type'=>$dataArr[$j]['built_up_area_type'], 'built_up_area'=>$dataArr[$j]['built_up_area'], 'open_area_type'=>$dataArr[$j]['open_area_type'], 'open_area'=>$dataArr[$j]['open_area'], 'other_area'=>$dataArr[$j]['other_area']);


				
				$result = $this->db->insert( TBL_PREFIX.TBL_ADDITIONAL_DETAILS, $userData );
				log_message('debug', 'JV_model: registerSellerAdditionalInformation: ['.$this->db->last_query().']');
				
				if ( !$result && $this->db->error()['code'] == 1062 ){
					$response = "false";
					log_message('debug', 'JV_model: registerSellerAdditionalInformation: [FALSE:::: Duplicate Data:]');
				}else{
					if ( $this->db->affected_rows() > 0 ){
						$response = "true";
						log_message('debug', 'JV_model: registerSellerAdditionalInformation: [TRUE:::: Data Insertd]');
					}else{
						$response = "false";
						log_message('debug', 'JV_model: registerSellerAdditionalInformation: [FALSE:::: Data not Inserted]');
					}
				}

			}else{
				log_message('debug','In registerSellerAdditionalInformation:::::::::::::::::::::::Yahoo::::: In else');
				$userData = array('property_type'=>$dataArr[$j]['property_type'],'real_state_value_unit'=>$dataArr[$j]['real_state_value_unit'],'real_state_value'=>$dataArr[$j]['real_state_value'], 'facility_desc'=>$dataArr[$j]['facility_desc'], 'industrial_commercial'=>$dataArr[$j]['industrial_commercial'], 'total_land_area_type'=>$dataArr[$j]['total_land_area_type'], 'total_land_area'=>$dataArr[$j]['total_land_area'], 'built_up_area_type'=>$dataArr[$j]['built_up_area_type'], 'built_up_area'=>$dataArr[$j]['built_up_area'], 'open_area_type'=>$dataArr[$j]['open_area_type'], 'open_area'=>$dataArr[$j]['open_area'], 'other_area'=>$dataArr[$j]['other_area']);
				
					
				$result = $this->db->update( TBL_PREFIX.TBL_ADDITIONAL_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id'),'id'=>$dataArr[$j]['property_id'] ) );
				log_message('debug', 'JV_model: registerSellerAdditionalInformation: ['.$this->db->last_query().']');

				if( !$result ){
					$response = "false";
					log_message('debug', 'JV_model: registerSellerAdditionalInformation: [FALSE:::: Error]');
				}else{
					if( $this->db->affected_rows() > 0 ){
						$response = "true";
						log_message('debug', 'JV_model: registerSellerAdditionalInformation: [TRUE:::: Data Updated]');
					}else{
						$response = "false";
						log_message('debug', 'JV_model: registerSellerAdditionalInformation: [TRUE:::: Data Not Updated]');
					}
				}

			}	
		}

		
		$response = "true";

		log_message('debug','In registerSellerAdditionalFinancialInformation:::::::::::::::::::::::Yahoo:::: after loop');
		for($j=0; $j < $count_fin; $j++){
			log_message('debug','In registerSellerAdditionalFinancialInformation:::::::::::::::::::::::Yahoo::::: in loop again');
			if(($dataArrFin[$j]['finance_id']==NULL)){
				log_message('debug','In registerSellerAdditionalFinancialInformation:::::::::::::::::::::::Yahoo:::::: finance not set');
				$userData = array('form_id'=>$this->session->userdata('last_form_id'),'fin_year'=>$dataArrFin[$j]['fin_year'],'fin_revenue_turnover'=>$dataArrFin[$j]['fin_revenue_turnover'],'fin_EDITDA'=>$dataArrFin[$j]['fin_EDITDA'], 'fin_PAT'=>$dataArrFin[$j]['fin_PAT'], 'fin_margin'=>$dataArrFin[$j]['fin_margin']);


				
				$result = $this->db->insert( TBL_PREFIX.TBL_ADDITIONAL_FINANCIAL_DETAILS, $userData );
				log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: ['.$this->db->last_query().']');
				
				if ( !$result && $this->db->error()['code'] == 1062 ){
					$response = "false";
					log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: [FALSE:::: Duplicate Data:]');
				}else{
					if ( $this->db->affected_rows() > 0 ){
						$response = "true";
						log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: [TRUE:::: Data Insertd]');
					}else{
						$response = "false";
						log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: [FALSE:::: Data not Inserted]');
					}
				}

			}else{
				log_message('debug','In registerSellerAdditionalFinancialInformation:::::::::::::::::::::::Yahoo::::: In else');
				$userData = array('form_id'=>$this->session->userdata('last_form_id'),'fin_year'=>$dataArrFin[$j]['fin_year'],'fin_revenue_turnover'=>$dataArrFin[$j]['fin_revenue_turnover'],'fin_EDITDA'=>$dataArrFin[$j]['fin_EDITDA'], 'fin_PAT'=>$dataArrFin[$j]['fin_PAT'], 'fin_margin'=>$dataArrFin[$j]['fin_margin']);
				
					
				$result = $this->db->update( TBL_PREFIX.TBL_ADDITIONAL_FINANCIAL_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id'),'id'=>$dataArrFin[$j]['finance_id'] ) );
				log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: ['.$this->db->last_query().']');

				if( !$result ){
					$response = "false";
					log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: [FALSE:::: Error]');
				}else{
					if( $this->db->affected_rows() > 0 ){
						$response = "true";
						log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: [TRUE:::: Data Updated]');
					}else{
						$response = "false";
						log_message('debug', 'JV_model: registerSellerAdditionalFinancialInformation: [TRUE:::: Data Not Updated]');
					}
				}

			}	
		}




		if($response=="true"){
			return true;
		}else{
			return false;
		}
		
	}

	public function saveAllDetails( $uploadedFileNames ){
		$i = 0;
		$form_id= $this->input->post('frm_id');
		foreach($uploadedFileNames as $uploadedFile){
			log_message('debug', 'uploadedFile ['.print_r($uploadedFile, true).']');
			$userData = array('form_id'=>$form_id, 'customer_id'=>$this->session->userdata('userid'), 'document_type'=> $uploadedFile['doc_type'], 'path'=>$uploadedFile['path']);
			$result   = $this->db->insert( TBL_PREFIX.TBL_BUSINESS_DOCUMENTS, $userData );
			log_message('debug', 'Jv_model: saveAllDetails: ['.$this->db->last_query().']');
			if( $result ){
				if( $this->db->affected_rows() > 0 ){
					$i++;
				}
			}
			$userData = null;
		}
		if( $i == count($uploadedFileNames) ){
			return true;
		}else{
			return false;
		}
	}
	public function getBusinessMedia($form_id){
		//$mediaQuery = $this->db->get_where(TBL_PREFIX.TBL_BUSINESS_DOCUMENTS, array('customer_id'=>$this->session->userdata('userid'), 'form_id'=>$form_id));
		$mediaQuery = $this->db->get_where(TBL_PREFIX.TBL_BUSINESS_DOCUMENTS, array('form_id'=>$form_id));
		log_message('debug', 'JV_model: getBusinessMedia: ['.$this->db->last_query().']');
		if( isset( $mediaQuery ) && $mediaQuery->num_rows() > 0 ){
			return $mediaQuery->result_array();
		}else{
			return NULL;
		}
	}

	public function registerSellerPackage(){
		$selectedPackage = $this->input->post('spackage');
		$form_id         = $this->input->post('form_id');
		log_message('debug', 'In register Seller package and formid is ['.$form_id.'], selected package is ['.$selectedPackage.']');
		$userData = array('customer_id'=>$this->session->userdata('userid'), 'form_id'=>$form_id, 'package_id'=>$selectedPackage );
		$whereClause = array( 'form_id'=>$form_id, 'customer_id' => $this->session->userdata('userid') );

		$packageDetails = $this->db->get_where(TBL_PREFIX.TBL_CUSTOMER_PACKAGE, $whereClause);
		log_message('debug', 'Jv_model: registerSellerPackage: ['.$this->db->last_query().']');
		if( isset( $packageDetails ) && $packageDetails->num_rows() > 0 ){
			log_message('debug', ' -------------Package found---------- ');
			$result = $this->db->update( TBL_PREFIX.TBL_CUSTOMER_PACKAGE, $userData, $whereClause );
		}else{
			log_message('debug', ' -------------Package not found---------- ');
			$result = $this->db->insert( TBL_PREFIX.TBL_CUSTOMER_PACKAGE, $userData );
		}

		log_message('debug', 'Last query to insert-update customer package ['.$this->db->last_query().']');
		if( $this->db->affected_rows() > 0 ){
			return true;
		}else{
			return false;
		}
	}


	public function registerBusinessPayment(){
		$property_type = $this->input->post('');

		$userData = array('property_type'=>$property_type,'real_state_value'=>$real_state_value, 'facility_desc'=>$facility_desc, 'industrial_commercial'=>$industrial_commercial, 'total_land_area_type'=>$total_land_area_type, 'total_land_area'=>$total_land_area, 'built_up_area_type'=>$built_up_area_type, 'built_up_area'=>$built_up_area, 'open_area_type'=>$open_area_type, 'open_area'=>$open_area, 'other_area'=>$other_area, 'fin_year'=>$fin_year, 'fin_revenue_turnover'=>$fin_revenue_turnover, 'fin_EDITDA'=>$fin_EDITDA, 'fin_PAT'=>$fin_PAT, 'fin_margin'=>$fin_margin, 'fin_yearly'=>$fin_yearly);

		$result = $this->db->update( TBL_PREFIX.TBL_JV_BUSINESS_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id') ) );
		log_message('debug', 'Jv_model: registerBusinessPayment: ['.$this->db->last_query().']');
		if( !$result ){
			return "error";
		}else{
			if( $this->db->affected_rows() > 0 ){
				return true;
			}else{
				return false;
			}
		}

	}

	public function registerAdditionService(){
		$form_id         = $this->input->post('frm_id');

		

		//Need to check in which table data will be inserted for additional service
		$feature_listing                  = $this->input->post('ads_featured_listing');
		$information_memorandum           = $this->input->post('ads_information_memorandum');
		$business_plan                    = $this->input->post('business_plan');
		$business_valuation               = $this->input->post('business_valuation');
		$business_mandate                 = $this->input->post('business_mandate');
		$business_advisory                = $this->input->post('business_advisory');
		$per_sale_business_structuring    = $this->input->post('per_sale_business_structuring');
	

		if(isset($feature_listing) ){
			$feature_listing ='YES';
		}else{
			$feature_listing='NO';
		}
		
		if(isset($information_memorandum) ){
			$information_memorandum ='YES';
		}else{
			$information_memorandum='NO';
		}

		if(isset($business_plan) ){
			$business_plan ='YES';
		}else{
			$business_plan='NO';
		}

		if(isset($business_valuation) ){
			$business_valuation ='YES';
		}else{
			$business_valuation='NO';
		}

		if(isset($business_mandate) ){
			$business_mandate ='YES';
		}else{
			$business_mandate='NO';
		}

		if(isset($business_advisory) ){
			$business_advisory ='YES';
		}else{
			$business_advisory='NO';
		}

		if(isset($per_sale_business_structuring) ){
			$per_sale_business_structuring ='YES';
		}else{
			$per_sale_business_structuring='NO';
		}

		
		//$form_id         = $this->input->post('form_id');

		$userData = array('customer_id'=>$this->session->userdata('userid'), 'form_id'=>$form_id, 'feature_listing'=>$feature_listing, 'memorandum'=>$information_memorandum, 'plan'=>$business_plan, 'valuation'=>$business_valuation, 'mandate'=>$business_mandate, 'advisory'=>$business_advisory, 'pre_sales'=>$per_sale_business_structuring );
		$whereClause = array( 'form_id'=>$form_id, 'customer_id' => $this->session->userdata('userid') );

		$AdditionalServiceDetails = $this->db->get_where(TBL_PREFIX.TBL_CUSTOMER_ADDITIONAL_SERVICES, $whereClause);
		log_message('debug', 'Jv_model: registerAdditionService: Query to check user current package ['.$this->db->last_query().']');
		if( isset( $AdditionalServiceDetails ) && $AdditionalServiceDetails->num_rows() > 0 ){
			$result = $this->db->update( TBL_PREFIX.TBL_CUSTOMER_ADDITIONAL_SERVICES, $userData, $whereClause );
		}else{
			$result = $this->db->insert( TBL_PREFIX.TBL_CUSTOMER_ADDITIONAL_SERVICES, $userData );
		}

		log_message('debug', 'User_model: registerAdditionService:: Last query to insert customer package ['.$this->db->last_query().']');
		if( $this->db->affected_rows() > 0 ){
			return true;
		}else{
			return false;
		}
		
		/*$userData = array('property_type'=>$property_type,'real_state_value'=>$real_state_value, 'facility_desc'=>$facility_desc, 'industrial_commercial'=>$industrial_commercial, 'total_land_area_type'=>$total_land_area_type, 'total_land_area'=>$total_land_area, 'built_up_area_type'=>$built_up_area_type, 'built_up_area'=>$built_up_area, 'open_area_type'=>$open_area_type, 'open_area'=>$open_area, 'other_area'=>$other_area, 'fin_year'=>$fin_year, 'fin_revenue_turnover'=>$fin_revenue_turnover, 'fin_EDITDA'=>$fin_EDITDA, 'fin_PAT'=>$fin_PAT, 'fin_margin'=>$fin_margin, 'fin_yearly'=>$fin_yearly);

		$result = $this->db->update( TBL_PREFIX.TBL_SELL_BUSINESS_DETAILS, $userData, array( 'form_id' => $this->session->userdata('last_form_id') ) );
		if( !$result ){
			return "error";
		}else{
			if( $this->db->affected_rows() > 0 ){
				return "true";
			}else{
				return "false";
			}
		}
		return true;*/
	}


	public function setQueryInfo(){
		$name    = $this->input->post('name');
		$email   = $this->input->post('email');
		$city    = $this->input->post('city');
		$contact = $this->input->post('contact');
		$msg     = $this->input->post('msg');

		$queryData = array('name'=>$name, 'email'=>$email, 'city'=>$city, 'contact_number'=>$contact, 'message'=>$msg);
		$result = $this->db->insert(TBL_PREFIX.TBL_USER_QUERY, $queryData);
		if( !$result ){
			return false;
		}else{
			if( $this->db->affected_rows() > 0 ){
				return true;
			}else{
				return false;
			}
		}
	}
	public function getUserQueries(){
		return null;
	}
	public function getUserContacts($infoType='basic', $qid){
		if ( strcasecmp($infoType, 'basic') == 0 ){
			$this->db->select(TBL_PREFIX.TBL_LISTING_CONTACTS.".id,".TBL_PREFIX.TBL_LISTING_CONTACTS.".name, key_headline, mobile, country, state, city");
		}else{
			$this->db->select(TBL_PREFIX.TBL_LISTING_CONTACTS.".id,".TBL_PREFIX.TBL_LISTING_CONTACTS.".name, key_headline, mobile, country, state, city, message, date");
		}
		$this->db->from(TBL_PREFIX.TBL_LISTING_CONTACTS);
		$this->db->join(TBL_PREFIX.TBL_BUSINESS_DETAILS, TBL_PREFIX.TBL_LISTING_CONTACTS.'.form_id = '.TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id');
		if ( $qid != 0 ){
			//$this->db->join(TBL_PREFIX.TBL_SELL_BUSINESS_DETAILS, TBL_PREFIX.TBL_BUSINESS_DETAILS.'.form_id = '.TBL_PREFIX.TBL_SELL_BUSINESS_DETAILS.'.form_id');
			$this->db->where(TBL_PREFIX.TBL_LISTING_CONTACTS.'.id', $qid);
		}
		$this->db->where(TBL_PREFIX.TBL_LISTING_CONTACTS.'.CONTACT_TO', $this->session->userdata('userid'));
		$contactList = $this->db->get();
		log_message('debug','Query to get contacts['.$this->db->last_query().']');
		if ( !$contactList ){
			return null;
		}else{
			return $contactList->result();
		}
	}
	public function getUserContactDetails($contactId){
		return null;
	}
	public function getUserFavourite(){
		return null;
	}
	public function ProInfo(){
		$keyHeadline            = $this->input->post('keyHeadline');
		$businessDesc          = $this->input->post('bus_desc');
		$businessCountry       = $this->input->post('country');
		$businessRegion        = $this->input->post('region');
		$businessState         = $this->input->post('state');
		$businessCity          = $this->input->post('city');
		$property_type      = $this->input->post('property_type');
		$saleable_area   = $this->input->post('saleable_area');
		$age_construction           = $this->input->post('age_construction');
		$authority_approval     = $this->input->post('authority_approval');
		$other_state           = $this->input->post('other_state');
		if($this->input->post('other_city')!=''){
		
	    $other_city     = $this->input->post('other_city');
		
		
		}
		if($this->input->post('other_city_2')!=''){
			
		$other_city     = $this->input->post('other_city_2');

		
		}
		
		//select customer details
			$this->db->select('*');
			$this->db->where(array('customer_id'=>$this->session->userdata('userid')));
			$q = $this->db->get(TBL_PREFIX.TBL_CUSTOMER_DETAILS);
			$data = $q->result_array();
			
			
			
			$name     = $data[0]['name'];
			$contact_number     = $data[0]['contact_number'];
			$address     = $data[0]['address'];
			$location     = $data[0]['location'];
			$company_name     = $data[0]['company_name'];

		$userData = array('business_type'=>'re_business','name'=>$name,'email'=>$this->session->userdata('userid'),'contact'=>$contact_number,'address'=>$address,'location'=>$location,'company_name'=>$company_name,'key_headline'=>$keyHeadline,'description'=>$businessDesc,'location_country'=>$businessCountry,'location_region'=>$businessRegion,'location_state'=>$businessState,'location_city'=>$businessCity,'property_type_old'=>$property_type,'saleable_area'=>$saleable_area,'age_construction'=>$age_construction,'authority_approval'=>$authority_approval,'other_state'=>$other_state,'other_city'=>$other_city);

		$result = $this->db->update( TBL_PREFIX.TBL_BUSINESS_DETAILS, $userData, array( 'customer_id' => $this->session->userdata('userid'),'form_id' => $this->input->post('frm_id') ) );
		log_message('debug', 'RE_model: registerBusinessBasicInformation: ['.$this->db->last_query().']');
		if( !$result ){
			return 'error';
		}else{
			if( $this->db->affected_rows() > 0 ){
				return 'true';
			}else{
				return 'true';
			}
		}
	}
	
		public function TenDesc( $actType = "NEW" ){
		$tenancy_details         = $this->input->post('tenancy_details');
		$lease_period       = $this->input->post('lease_period');
		$lease_start             = $this->input->post('lease_start');
		$lease_end = $this->input->post('lease_end');
		$lock_period      = $this->input->post('lock_period');
		$security_received       = $this->input->post('security_received');
		$monthly_rental             = $this->input->post('monthly_rental');
		$annual_rental             = $this->input->post('annual_rental');
		$annual_maintenance        = $this->input->post('annual_maintenance');
		$escalation_after            = $this->input->post('escalation_after');
		$escalation_percent     = $this->input->post('escalation_percent');
		

		$userData = array('tenancy_details'=>$tenancy_details, 'lease_period'=>$lease_period, 'lease_start'=>$lease_start,
		'lease_end'=>$lease_end, 'lock_period'=>$lock_period, 'security_received'=>$security_received, 'monthly_rental'=>$monthly_rental,
		'annual_rental'=>$annual_rental,'annual_maintenance'=>$annual_maintenance, 'escalation_after'=>$escalation_after, 
		'escalation_percent'=>$escalation_percent);

		$result = $this->db->update( TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS, $userData, array( 'form_id' =>$this->input->post('frm_id') ) );
		log_message('debug', 'RE_model: registerSellerBusinessDescription  ['.$this->db->last_query().']');
		if( !$result ){
			return 'error';
		}else{
			if( $this->db->affected_rows() > 0 ){
				return 'true';
			}else{
				return 'true';
			}
		}
	}
	public function Tendetails( $actType = "NEW" ){
		$features         = $this->input->post('features');
		$location_advantages       = $this->input->post('location_advantages');
		$reason_sale             = $this->input->post('reason_sale');
		$roi_present = $this->input->post('roi_present');
		$roi_escalation      = $this->input->post('roi_escalation');
		$other_income       = $this->input->post('other_income');
		$price_currency             = $this->input->post('price_currency');
		$price_value             = $this->input->post('price_value');
		$price_unit        = $this->input->post('price_unit');
		
		

		$userData = array('features'=>$features, 'location_advantages'=>$location_advantages, 'reason_sale'=>$reason_sale,
		'roi_present'=>$roi_present, 'roi_escalation'=>$roi_escalation, 'other_income'=>$other_income, 'price_currency'=>$price_currency,
		'price_value'=>$price_value,'price_unit'=>$price_unit);
		
		$result = $this->db->update( TBL_PREFIX.TBL_REALESTATE_BUSINESS_DETAILS, $userData, array( 'form_id' => $this->input->post('frm_id') ) );
		log_message('debug', 'RE_model: registerSellerBusinessDetails  ['.$this->db->last_query().']');
		if( !$result ){
			return 'error';
		}else{
			if( $this->db->affected_rows() > 0 ){
				return 'true';
			}else{
				return 'true';
			}
		}
		
	}
	

}
?>
