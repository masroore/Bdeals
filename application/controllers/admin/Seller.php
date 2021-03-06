	<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seller extends CI_Controller {

	function __construct(){
		parent::__construct();
		check_isvalidated('admin');
        }

	public function index()
	{
		$this->load->library('parser');
                $this->load->helper('html');
                $header['title']  = 'Business Deals';
                $header[ 'css_name' ] = array( 'admin/css/jquery.dataTables.min', 'admin/css/entypo', 'admin/css/font-awesome.min', 'admin/css/bootstrap.min', 'admin/css/mouldifi-forms', 'admin/css/mouldifi-core' ) ;
                //$header[ 'js_name' ]  = array( 'jquery' );
                //$header[ 'showLeftPanel' ] = true;
                $footer['fjs_name'] = array( 'admin/js/jquery.min', 'admin/js/jquery.dataTables.min', 'admin/js/bootstrap.min', 'admin/js/bootstrap/validator.min', 'admin/js/plugins/metismenu/jquery.metisMenu', 'admin/js/plugins/blockui-master/jquery-ui', 'admin/js/plugins/blockui-master/jquery.blockUI', 'admin/js/plugins/flot/jquery.flot.min', 'admin/js/plugins/flot/jquery.flot.tooltip.min', 'admin/js/plugins/flot/jquery.flot.resize.min', 'admin/js/plugins/flot/jquery.flot.selection.min', 'admin/js/plugins/flot/jquery.flot.pie.min', 'admin/js/plugins/flot/jquery.flot.time.min', 'admin/js/scripts/functions', 'admin/js/plugins/chartjs/Chart.min' );

		$this->load->model('admin/Seller_model', 'seller');
		$data['userList'] = $this->seller->getBusinessUserProfile('sell_business');

                $this->load->view('admin/include/header', $header );
		$this->load->view('admin/seller', $data);
                $this->parser->parse('admin/include/footer', $footer );
	}

	public function seller_page(){
		$draw   = intval($this->input->get("draw"));
		$start  = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$this->load->model('admin/Seller_model', 'seller');
		$userList = $this->seller->getBusinessUserProfile('sell_business');
		$this->load->model('admin/Users_model', 'user');
		
		
		

		$assignToDetails = null;
		$data = array();
		$i    = 1;
		foreach($userList as $r){
			//$assignToDetails = $this->seller->getAssignToDetails( $r['form_id'] );
			$assignToDetails = $this->seller->getAssignToDetails( $this->user->getCustomerDetailsByCustId($r['customer_id'], array('id', 'name'))[0]['id'] );
			
			if ( strcasecmp($this->session->userdata('admin_type'), 'admin' ) == 0 ){
				$businessStatus  = $this->seller->getBusinessStatusByFormId( $r['form_id'] );
				log_message('debug','Assigned To Details ['.print_r($assignToDetails, true).'], user id from session ['.$this->session->userdata('admin_userid').']');
				if( $assignToDetails[0]['id'] == null ){
					$assignToDetails[0]['id'] = 'NA';
				}
				
				//city
					if($r['location_city']=='Other'){
						$city=$r['other_city'];
					}else{
						$city=$this->cachemethods->getCityNameById($r['location_city'],$r['location_state']);	

					}
				//count seller count
		
				$SellerCount=$this->seller->ContactCountByFormIdUser($r['form_id']);
				
				//die;
				
				if($r['business_add_date']==''){
					
					$dateval=$r['added_date'];
				}
				if($r['added_date']==''){
					
					$dateval=$r['business_add_date'];
				}
				if($r['added_date']!='' and $r['business_add_date']!=''){
					
					$dateval=$r['added_date'];
				}
				
				
				
				
				
				
				$data[] = array($i,$dateval,$r['form_id'],$this->cachemethods->getCatNameByCId($r['listing_category']),$r['name'],$r['contact'], $r['email'], $city,$r['expiry_date'],'<a data-toggle="modal" data-target="#myModal2" href="'.$r['form_id'].'" class="contactList" role="button">'.$r['num_business_viewed'].'</a>','<a data-toggle="modal" data-target="#myModal" href="'.$r['form_id'].'" class="favelist" role="button">'.$r['add_to_favorite'].'</a>','<a data-toggle="modal" data-target="#myModal3" href="'.$r['form_id'].'" class="SellerCount" role="button">'.$SellerCount.'</a>', $assignToDetails[0]['user_id'], $businessStatus, '<a href="seller_explore/'.$assignToDetails[0]['id'].'/'.$r['form_id'].'/'.$r['customer_id'].'/abc" class="btn btn-sm btn-danger" role="button">Explore</a>');
				$i++;
			}else{
				if ( strcasecmp( $this->session->userdata('admin_userid'), $assignToDetails[0]['user_id'] ) == 0 ){
					$businessStatus  = $this->seller->getBusinessStatusByFormId( $r['form_id'] );
					log_message('debug','Assigned To Details ['.print_r($assignToDetails, true).'], user id from session ['.$this->session->userdata('admin_userid').']');
					if( $assignToDetails[0]['id'] == null ){
						$assignToDetails[0]['id'] = 'NA';
					}
					
					
					
					$data[] = array($i,$dateval,$r['form_id'],$this->cachemethods->getCatNameByCId($r['listing_category']),$r['name'],$r['contact'], $r['email'], $city,$r['expiry_date'],'<a data-toggle="modal" data-target="#myModal2" href="'.$r['form_id'].'" class="contactList" role="button">'.$r['num_business_viewed'].'</a>','<a data-toggle="modal" data-target="#myModal" href="'.$r['form_id'].'" class="favelist" role="button">'.$r['add_to_favorite'].'</a>','<a data-toggle="modal" data-target="#myModal3" href="'.$r['form_id'].'" class="SellerCount" role="button">'.$SellerCount.'</a>', $assignToDetails[0]['user_id'], $businessStatus, '<a href="'.base_url().'admin/seller_explore/'.$assignToDetails[0]['id'].'/'.$r['form_id'].'/'.$r['customer_id'].'/abc" class="btn btn-sm btn-danger" role="button">Explore</a>');
					$i++;
				}
			}
		}

		$output = array( "draw" => $draw, "recordsTotal" => count($userList), "recordsFiltered"=>count($userList), "data"=>$data);
		echo json_encode($output);
		exit();

	}

	public function seller_explore( $assignedTo=NULL, $selectedFormId, $customerId ){
		$this->load->library('parser');
                $this->load->helper('html');
                $header['title']  = 'Business Deals';
                $header[ 'css_name' ] = array( 'admin/css/jquery.dataTables.min', 'admin/css/entypo', 'admin/css/font-awesome.min', 'admin/css/bootstrap.min', 'admin/css/mouldifi-forms', 'admin/css/mouldifi-core','css/default','css/layouts', 'css/shortcodes', 'css/font-awesome/css/font-awesome.min','css/responsive-leyouts' ) ;
                $footer['fjs_name'] = array( 'admin/js/jquery.min', 'admin/js/jquery.dataTables.min', 'admin/js/bootstrap.min', 'admin/js/bootstrap/validator.min', 'admin/js/plugins/metismenu/jquery.metisMenu', 'admin/js/plugins/blockui-master/jquery-ui', 'admin/js/plugins/blockui-master/jquery.blockUI', 'admin/js/plugins/flot/jquery.flot.min', 'admin/js/plugins/flot/jquery.flot.tooltip.min', 'admin/js/plugins/flot/jquery.flot.resize.min', 'admin/js/plugins/flot/jquery.flot.selection.min', 'admin/js/plugins/flot/jquery.flot.pie.min', 'admin/js/plugins/flot/jquery.flot.time.min', 'admin/js/scripts/functions', 'admin/js/plugins/chartjs/Chart.min', 'js/state_city_js', 'js/ajaxmethods', 'admin/js/seller_edit' );

		$this->load->model('Business_model', 'business');
		$data['businessDetails']  = $this->business->getBusinessDetailsByFormId('sell_business', $selectedFormId);
		
			
		$this->load->model('User_model', 'user');
		$this->user->setFormSession($selectedFormId);
		
		
		log_message('debug', 'FORM ID IS ['.$selectedFormId.'] IN SELLER_EXPLORE');
		
		$data['sellBusinessAdditionalDetails'] = $this->user->getSellerAdditionalDetails();
		$data['sellBusinessAdditionalFinancialDetails']= $this->user->getSellerAdditionalFinancialDetails();
		
		$data['user_package'] = $this->user->currentPackage( $selectedFormId );
		$data['additionalServices'] = $this->user->currentAdditionalServices($selectedFormId);
		
		$data['medias'] = $this->user->getBusinessMedia($selectedFormId); 
		$this->load->model('package_model', 'package');
        $data['packages'] = $this->package->getAvailablePackage();
		
		
		//for property tags
		$this->load->model('admin/Users_model', 'users');
		$data['getPropertyTagList'] = $this->users->getPropertyTagList();

		$data['assignedTo'] = $assignedTo;
		$data['formId']     = $selectedFormId;
		$data['customerId'] = $customerId;

                $this->load->view('admin/include/header', $header );
				$this->load->view('admin/seller_explore', $data);
                $this->parser->parse('admin/include/footer', $footer );
	}

	public function seller_edit($formType){
		log_message('debug','FORM DATA IS ['.print_r($this->input->post(), true).']');
		
		$this->load->library('parser');
		$this->load->helper('html');
		$this->load->model('admin/Business_model','business');
		
		if( strcasecmp( $formType, 'seller_up' ) == 0 ){
			$retVal = $this->business->updateUserProfile();
		}else if ( strcasecmp( $formType, 'seller_binfo' ) == 0 ){
			$retVal = $this->business->updateBasicInformation();
		}else if ( strcasecmp( $formType, 'seller_bd' ) == 0 ){
			$retVal = $this->business->updateBusinessDetails();
		}else if ( strcasecmp( $formType, 'seller_adetails' ) == 0 ){
			$retVal = $this->business->updateSellerAdditionalDetails();
		}
		else if ( strcasecmp( $formType, 'seller_upload' ) == 0 ){
			$retVal = $this->userProfileCreate($this->input->post('customer_id'));
			//$retVal = $this->business->updateSellerAdditionalDetails();
			//$this->load->controller('lb_userregister','sell_controller');
			//$retVal = $this->sell_controller->userProfileCreate();
		}else if ( strcasecmp( $formType, 'seller_package' ) == 0 ){
			
			//$this->load->controller('lb_userregister','sell_controller');
			//$retVal = $this->sell_controller->userProfileCreate();
			if ( $retVal ){
				$retVal = array('status'=>'true','next_tab'=>'Additionalservices');
			}else{
				$retVal = array('status'=>'false','next_tab'=>'Packagedetails');
			}
		}else if ( strcasecmp( $formType, 'seller_aservice' ) == 0 ){
                        $retVal = $this->userProfileCreate();
                }
		log_message('debug','RETURN IS ['.json_encode($retVal).']');
		echo json_encode($retVal);
	}

	public function userProfile(){
		$this->load->model('Business_model','business');
	}
	
	public function userProfileCreate(){
		$this->load->model('user_model', 'user');
		$dbResult = null;
		$frmValue = $this->input->post('frm_submit');
		log_message('debug','FORM TANUJ VALUE ['.$frmValue.']');
		if ( strcasecmp( $frmValue, 'sb_up' ) == 0 ){
				if( $this->input->post('frm_id') != null ){
					$dbResult = $this->user->registerBusinessUser("UPDATE")[0];
				}else{
					$dbResult = $this->user->registerBusinessUser("NEW")[0];
				}

				if( $dbResult ){
					redirect('list-your-business-basic-information');
				}else{
					redirect('list-your-business-user-profile'); //Redirect user on user profile page in case any db error
				}
			
		}else if ( strcasecmp( $frmValue, 'sb_bi' ) == 0 ){
			if($this->session->userdata('last_form_id')=='NEW'){
				redirect('list-your-business-basic-information');
			}else{
				if( $this->user->registerBusinessBasicInformation() ){
					redirect('list-your-business-description');
				}else{
					redirect('list-your-business-basic-information'); //Redirect user on basic information page in case any db error
				}
			}
		}else if ( strcasecmp( $frmValue, 'sb_bd' ) == 0 ){
			if($this->session->userdata('last_form_id')=='NEW'){
				redirect('list-your-business-description');
			}else{
				if( $this->input->post('frm_id') != null ){
					$dbResult = $this->user->registerSellerBusinessDescription("UPDATE");
				}else{
					$dbResult = $this->user->registerSellerBusinessDescription( "NEW" );
				}

				if( $dbResult ){
					redirect('list-your-business-additional-details');
				}else{
					redirect('list-your-business-description'); //Redirect user on business description page in case any db error
				}
			}
		}else if ( strcasecmp( $frmValue, 'sb_ad' ) == 0 ){
			if($this->session->userdata('last_form_id')=='NEW'){
				redirect('list-your-business-additional-details');
			}else{
				log_message('debug','In additional details:::::::::::::::yehoo');
				if( $this->user->registerSellerAdditionalInformation() ){
					redirect('list-your-business-additional-details-uploads');
				}else{
					redirect('list-your-business-additional-details'); //Redirect user on addition information page in case any db error
				}
			}
		}else if ( strcasecmp( $frmValue, 'sb_pa' ) == 0 ){//Selected Pack
			
			log_message('debug', 'FORM TANUJ ID  ['.$this->session->userdata('last_form_id').']');
			
			if($this->session->userdata('last_form_id')=='NEW'){
				log_message('debug', 'FORM TANUJ ID  ------------Its New');
				redirect('list-your-business-package-details');
				//echo '{"status":"nok"}';
			}else{

				if ( $this->user->registerSellerPackage() ){
					log_message('debug', 'FORM TANUJ ID  ------------package successfullu updated');
					//redirect('list-your-business-package-details');
					 echo '{"status":"ok"}';
				}else{
					redirect('list-your-business-package-details');
					echo  '{"status":"nok"}';
				}
			}
		}else if ( strcasecmp( $frmValue, 'SB_PA_UP' ) == 0 ){//Selected Pack
			
			log_message('debug', 'FORM TANUJ ID  ['.$this->session->userdata('last_form_id').']');
			
			if($this->session->userdata('last_form_id')=='NEW'){
				log_message('debug', 'FORM TANUJ ID ---- In New Form');
				echo '{"status":"nok","next_tab":"Packagedetails"}';
				//return array('status'=>'nok', 'next_tab'=>'Packagedetails');
			}else{
				if ( $this->user->registerSellerPackageAdmin() ){
					log_message('debug', 'FORM TANUJ ID ----successfully submitted');
					echo '{"status":"true","next_tab":"Additionalservices"}';
				}else{
					 //$retVal =  array('status'=>'true', 'next_tab'=>'Packagedetails');
					log_message('debug', 'FORM TANUJ ID ---- failed');
					//redirect('list-your-business-package-details');
					echo '{"status":"nok","next_tab":"Packagedetails"}';
					//$retVal =  array('status'=>'nok', 'next_tab'=>'Packagedetails');
				}
			}
		}else if( strcasecmp( $frmValue, 'sb_as' ) == 0 ){
			if($this->session->userdata('last_form_id')=='NEW'){
				redirect('list-your-business-additional-services');
			}else{
				if( $this->user->registerAdditionService() ){
					redirect('list-your-business-payment');
				}else{
					redirect('list-your-business-additional-services');
				}
			}
		}else if( strcasecmp( $frmValue, 'sb_aservice' ) == 0 ){
			if($this->session->userdata('last_form_id')=='NEW'){
				redirect('list-your-business-additional-services');
			}else{
				if( $this->user->registerAdditionServiceAdmin() ){
					$next_tab = $this->input->post('next_tab');
					return array('status'=>'true','next_tab'=>$next_tab);
				}else{
					$next_tab = $this->input->post('next_tab');
					return array('status'=>'false','next_tab'=>$next_tab);
				}
			}
		}else if( strcasecmp( $frmValue, 'sb_pay' ) == 0 ){
			if( $this->user->registerBusinessPayment() ){
				redirect('list-your-business-preview');
			}else{
				redirect('list-yout-business-payment');
			}
		}else if( strcasecmp( $frmValue, 'sb_pre' ) == 0 ){
			//Need to check where should user navigate in this case.

			//if( $this->user->showPreview() ){
			redirect('home');
			//}else{
			//	redirect('list-your-business-preview');
			//}
		}else{// if( /*strcasecmp( $frmValue, 'sb_au' ) == 0 ||*/ isset($_FILES['uploadfile']['name']) ){
			//Code to upload files and insert file names with uploaded path in DB.
			if($this->input->post('frm_id') != 'NEW'){
			$uploadedImageFileNames = array();
			$uploadedVideoFileNames = array();
			$uploadedExecutiveFileNames = array();
			$uploadedMemorandumFileNames = array();
			$uploadedFinancialsFileNames = array();
			$uploadedAdditionalFileNames = array();

			log_message('debug', 'In Upload ['.print_r( $this->input->post(), true ).']');
			
			$customer_id = $this->input->post('customer_id');
			$assigned_to = $this->input->post('assigned_to');
			$form_id     = $this->input->post('frm_id');
			//$userpath = str_replace('@','',$this->session->userdata('userid'));
			$userpath = str_replace('@','',$customer_id);
			log_message('debug', 'user id is ['.$this->session->userdata('userid').'], ['.$userpath.']');
			$path = './uploads/'.$userpath.'/'.date('Y-m-d').'/';
			if( !is_dir($path) ){
				mkdir($path, 0777, TRUE);
			}

			$config['upload_path']   = $path;
			$config['allowed_types'] = "gif|jpg|png|mp4|doc|docx|pdf|xls|txt";

			//Code to upload image files
			//log_message('debug', 'FILE ['.print_r($_FILES['uploadfile'], true).']');
			//log_message('debug', 'FILE uploadexesumfile ['.print_r($_FILES['uploadexesumfile'], true).']');
			//log_message('debug', 'FILE uploadmemofile ['.print_r($_FILES['uploadmemofile'], true).']');
			//log_message('debug', 'FILE uploadfinancialfile ['.print_r($_FILES['uploadfinancialfile'], true).']');
			//log_message('debug', 'FILE uploadadditionfile ['.print_r($_FILES['uploadadditionfile'], true).']');

			/*Start code to upload and insert file details for image/video*/
			/*$file_ary = $this->reArrayFilesMultiple($_FILES['uploadfile']);
			$fileName = '';
			$j        = 0;
			foreach( $file_ary as $file ){
				$fileName         = $file['name'];
				$fileBaseName     = pathinfo( $fileName, PATHINFO_FILENAME);
				$originalFileName = $fileBaseName;
				$fileExtension    = pathinfo( $fileName, PATHINFO_EXTENSION);

				while( file_exists( $path.'/'.$fileBaseName.'.'.$fileExtension ) ){
					$fileBaseName = (String)$originalFileName.'_'.$i;
					$fileName     = $fileBaseName.'.'.$fileExtension;
					$i++;
				}
				if ( move_uploaded_file( $file['tmp_name'], $path.'/'.$fileName ) ){
					$uploadedFileNames[$j]['name']     = $file['name'];
					$uploadedFileNames[$j]['type']     = $file['type'];
					$uploadedFileNames[$j]['path']     = $path.$fileName;

					if( strstr($file['type'], 'image') != null ){
						$uploadedFileNames[$j]['doc_type'] = "image";
					}else{
						$uploadedFileNames[$j]['doc_type'] = "video";
					}
					$j++;
				}
			}*/
			log_message('debug', 'Uploaded file Namejjjjjjjjjjjjjjjjjjjjjjjjjjjjjj ' .print_r($_FILES,true));
			$uploadedFileNames = $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadfile']));
			log_message('debug', 'Uploaded file Name ['.print_r( $uploadedFileNames, true).']');
			/*if( count($uploadedFileNames) > 0 ){
				if( $this->user->saveAllDetails( $uploadedFileNames ) ){
					redirect('list-your-business-package-details'); //Redirect user after successfull business registration
				}else{
					redirect('list-your-business-additional-details-uploads');//Redirect user on upload document page in case any db error
				}
			}*/

			$uploadedFileNamesFin = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadfinancialfile']), 'financials' );
			$uploadedFileNamesAdd = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadadditionfile']), 'additional' );

			$fileInfoToSaveInDB = array_merge( $uploadedFileNames, $uploadedFileNamesExe, $uploadedFileNamesMem, $uploadedFileNamesFin, $uploadedFileNamesAdd );

			if( $this->user->saveAllDetailsAdmin( $fileInfoToSaveInDB ) ){
				if( strcasecmp( $frmValue, 'UA_FU' ) == 0 ){
					//$next_tab = $this->input->post('next_tab');
					//return array('status'=>'true','next_tab'=>$next_tab);
					redirect('admin/seller_explore/'.$assigned_to.'/'.$form_id.'/'.$customer_id.'/Packagedetails');
				}else{
					redirect('list-your-business-package-details'); //Redirect user after successfull business registration
				}
			}else{
			$uploadedFileNamesExe = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadexesumfile']), 'executive' );
			$uploadedFileNamesMem = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadmemofile']), 'memorandum' );
				if( strcasecmp( $frmValue, 'UA_FU' ) == 0 ){
					$next_tab = $this->input->post('next_tab');
					return array('status'=>'false','next_tab'=>$next_tab);
				}else{
					redirect('list-your-business-additional-details-uploads');//Redirect user on upload document page in case any db error
				}
			}
			}else{
				redirect('list-your-business-additional-details-uploads');//Redirect if its a new form
			}

			/*End of code for image/video upload*/

		}
	}

	
	public function uploadMedia( $path, $file_ary, $type = NULL ){
		$uploadedFileNames = array();
		$fileName = '';
		$j        = 0;
		log_message('debug','hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh '.print_r($file_ary,true));
		foreach( $file_ary as $file ){
			$fileName         = $file['name'];
			$fileBaseName     = pathinfo( $fileName, PATHINFO_FILENAME);
			$originalFileName = $fileBaseName;
			$fileExtension    = pathinfo( $fileName, PATHINFO_EXTENSION);

			while( file_exists( $path.'/'.$fileBaseName.'.'.$fileExtension ) ){
				$fileBaseName = (String)$originalFileName.'_'.$i;
				$fileName     = $fileBaseName.'.'.$fileExtension;
				$i++;
			}
			log_message('debug','File Name IS::::::::::::::::::::::::::::::::::::::::: ['.$fileName.']');
			if ( move_uploaded_file( $file['tmp_name'], $path.'/'.$fileName ) ){
				$uploadedFileNames[$j]['name'] = $file['name'];
				$uploadedFileNames[$j]['path'] = $path.$fileName;
				log_message('debug','Upload File Name IS::::::::::::::::::::::::::::::::::::::::: ['.$uploadedFileNames[$j]['name'].']');
				if( strstr($file['type'], 'image') != null ){
					$uploadedFileNames[$j]['doc_type'] = "image";
				}
				if( strstr($file['type'], 'video') != null ){
					$uploadedFileNames[$j]['doc_type'] = "video";
				}
				if( $type != null ){
					$uploadedFileNames[$j]['doc_type'] = $type;
				}
				$j++;
			}
		}
		return $uploadedFileNames;
	}

	public function reArrayFilesMultiple($files) {
		$file_ary = array();
		log_message('debug', 'FILE ARRAY IS ['.print_r($files, true).']');
		$file_count = count($files['name']);
		log_message('debug','File count is::::::::::::::::::::::::::::::::' .$file_count);
		for ($i=0; $i<$file_count; $i++) {
			$file_ary[$i]['name']     = $files['name'][$i];
			$file_ary[$i]['type']     = $files['type'][$i];
			$file_ary[$i]['tmp_name'] = $files['tmp_name'][$i];
			$file_ary[$i]['error']    = $files['error'][$i];
			$file_ary[$i]['size']     = $files['size'][$i];
		}

		return $file_ary;
	}
	
	public function UpdateSellerForm(){
		//print_r($this->input->post());
		
		//die;
					$frmValue = $this->input->post('frm_submit');	
					$this->load->model('admin/Business_model','business');
					
					//$dbResult1 = $this->business->updateUserProfile();
					$dbResult2 = $this->business->updateBasicInformation();
					$dbResult3 = $this->business->updateBusinessDetails();
					$dbResult4 = $this->business->updateSellerAdditionalDetails();
					
					$this->load->model('user_model', 'user');
					$dbResult5 = $this->user->registerSellerPackageAdmin();
					$dbResult6 = $this->user->registerAdditionServiceAdmin();
					
			$customer_id = $this->input->post('customer_id');
			$userpath = str_replace('@','',$customer_id);
			log_message('debug', 'user id is ['.$this->session->userdata('userid').'], ['.$userpath.']');
			$path = './uploads/'.$userpath.'/'.date('Y-m-d').'/';
			if( !is_dir($path) ){
				mkdir($path, 0777, TRUE);
			}

			$config['upload_path']   = $path;
			$config['allowed_types'] = "gif|jpg|png|mp4|doc|docx|pdf|xls|txt";
					log_message('debug', 'Uploaded Images ' .print_r($_FILES,true));
					
					
			$uploadedFileNames = $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadfile']));//for Upload Photos
			
			$uploadedVideos= $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadvideo']));//for Upload video
			
			$uploadedFileNamesFin = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadfinancialfile']), 'financials' );
			$uploadedFileNamesExe = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadexesumfile']), 'executive' );
					
					
			$uploadedFileNamesMem = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadmemofile']), 'memorandum' );
			$uploadedFileNamesAdd = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadadditionfile']), 'additional' );

			$fileInfoToSaveInDB = array_merge( $uploadedFileNames, $uploadedFileNamesExe, $uploadedFileNamesMem, $uploadedFileNamesFin, $uploadedFileNamesAdd ,$uploadedVideos);

			if( $this->user->saveAllDetailsAdmin( $fileInfoToSaveInDB ) ){
				/*if( strcasecmp( $frmValue, 'UA_FU' ) == 0 ){
					//$next_tab = $this->input->post('next_tab');
					//return array('status'=>'true','next_tab'=>$next_tab);
					
				}*/
				redirect('admin/seller_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
			}
				
				/*if($dbResult5){
				
				redirect('admin/seller_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
				*/
	}
	
		
	public function UpdateAdminSellerForm(){
		//print_r($this->input->post());
		
		//die;
					$frmValue = $this->input->post('frm_submit');	
					$this->load->model('admin/Business_model','business');
					
					//$dbResult1 = $this->business->updateUserProfile();
					$dbResult2 = $this->business->updateBasicInformation();
					$dbResult3 = $this->business->updateBusinessDetails();
					$dbResult4 = $this->business->updateAdminSellerAdditionalDetails();
					
					$this->load->model('user_model', 'user');
					$dbResult5 = $this->user->registerSellerPackageAdmin();
					$dbResult6 = $this->user->registerAdditionServiceAdmin();
					
			$customer_id = $this->input->post('customer_id');
			$userpath = str_replace('@','',$customer_id);
			log_message('debug', 'user id is ['.$this->session->userdata('userid').'], ['.$userpath.']');
			$path = './uploads/'.$userpath.'/'.date('Y-m-d').'/';
			if( !is_dir($path) ){
				mkdir($path, 0777, TRUE);
			}

			$config['upload_path']   = $path;
			$config['allowed_types'] = "gif|jpg|png|mp4|doc|docx|pdf|xls|txt";
					log_message('debug', 'Uploaded Images ' .print_r($_FILES,true));
					
					
			$uploadedFileNames = $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadfile']));//for Upload Photos
			
			$uploadedVideos= $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadvideo']));//for Upload video
			
			$uploadedFileNamesFin = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadfinancialfile']), 'financials' );
			$uploadedFileNamesExe = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadexesumfile']), 'executive' );
					
					
			$uploadedFileNamesMem = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadmemofile']), 'memorandum' );
			$uploadedFileNamesAdd = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadadditionfile']), 'additional' );

			$fileInfoToSaveInDB = array_merge( $uploadedFileNames, $uploadedFileNamesExe, $uploadedFileNamesMem, $uploadedFileNamesFin, $uploadedFileNamesAdd ,$uploadedVideos);

			if( $this->user->saveAllDetailsAdmin( $fileInfoToSaveInDB ) ){
				/*if( strcasecmp( $frmValue, 'UA_FU' ) == 0 ){
					//$next_tab = $this->input->post('next_tab');
					//return array('status'=>'true','next_tab'=>$next_tab);
					
				}*/
				redirect('admin/seller_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
			}
				
				/*if($dbResult5){
				
				redirect('admin/seller_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
				*/
	}
	
/**************update seller*****************/

	public function UpdateJVForm(){
		
					$frmValue = $this->input->post('frm_submit');	
					$this->load->model('admin/Business_model','business');
					
					//$dbResult1 = $this->business->updateUserProfile();
					$dbResult2 = $this->business->updateBasicInformation();
					$dbResult3 = $this->business->updateJVBusinessDetails();
					$dbResult4 = $this->business->updateSellerAdditionalDetails();
					
					$this->load->model('user_model', 'user');
					$dbResult5 = $this->user->registerSellerPackageAdmin();
					$dbResult6 = $this->user->registerAdditionServiceAdmin();
					
					$this->load->model('admin/Jv_Business_model', 'Business_model');
					$dbResult7 = $this->Business_model->updateJvDetails();
					
			$customer_id = $this->input->post('customer_id');
			$userpath = str_replace('@','',$customer_id);
			log_message('debug', 'user id is ['.$this->session->userdata('userid').'], ['.$userpath.']');
			$path = './uploads/'.$userpath.'/'.date('Y-m-d').'/';
			if( !is_dir($path) ){
				mkdir($path, 0777, TRUE);
			}

			$config['upload_path']   = $path;
			$config['allowed_types'] = "gif|jpg|png|mp4|doc|docx|pdf|xls|txt";
					log_message('debug', 'Uploaded Images ' .print_r($_FILES,true));
					
					
			$uploadedFileNames = $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadfile']));//for Upload Photos
			
			$uploadedVideos= $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadvideo']));//for Upload video
			
			$uploadedFileNamesFin = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadfinancialfile']), 'financials' );
			$uploadedFileNamesExe = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadexesumfile']), 'executive' );
					
					
			$uploadedFileNamesMem = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadmemofile']), 'memorandum' );
			$uploadedFileNamesAdd = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadadditionfile']), 'additional' );

			$fileInfoToSaveInDB = array_merge( $uploadedFileNames, $uploadedFileNamesExe, $uploadedFileNamesMem, $uploadedFileNamesFin, $uploadedFileNamesAdd ,$uploadedVideos);

			if( $this->user->saveAllDetailsAdmin( $fileInfoToSaveInDB ) ){
				/*if( strcasecmp( $frmValue, 'UA_FU' ) == 0 ){
					//$next_tab = $this->input->post('next_tab');
					//return array('status'=>'true','next_tab'=>$next_tab);
					
				}*/
				redirect('admin/jv_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
			}
				
				/*if($dbResult5){
				
				redirect('admin/seller_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
				*/
	}

	public function UpdateAdminJVForm(){
		
					$frmValue = $this->input->post('frm_submit');	
					$this->load->model('admin/Business_model','business');
					
					//$dbResult1 = $this->business->updateUserProfile();
					$dbResult2 = $this->business->updateBasicInformation();
					$dbResult3 = $this->business->updateJVBusinessDetails();
					$dbResult4 = $this->business->updateAdminSellerAdditionalDetails();
					
					$this->load->model('user_model', 'user');
					$dbResult5 = $this->user->registerSellerPackageAdmin();
					$dbResult6 = $this->user->registerAdditionServiceAdmin();
					
					$this->load->model('admin/Jv_Business_model', 'Business_model');
					$dbResult7 = $this->Business_model->updateJvDetails();
					
			$customer_id = $this->input->post('customer_id');
			$userpath = str_replace('@','',$customer_id);
			log_message('debug', 'user id is ['.$this->session->userdata('userid').'], ['.$userpath.']');
			$path = './uploads/'.$userpath.'/'.date('Y-m-d').'/';
			if( !is_dir($path) ){
				mkdir($path, 0777, TRUE);
			}

			$config['upload_path']   = $path;
			$config['allowed_types'] = "gif|jpg|png|mp4|doc|docx|pdf|xls|txt";
					log_message('debug', 'Uploaded Images ' .print_r($_FILES,true));
					
					
			$uploadedFileNames = $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadfile']));//for Upload Photos
			
			$uploadedVideos= $this->uploadMedia($path, $this->reArrayFilesMultiple($_FILES['uploadvideo']));//for Upload video
			
			$uploadedFileNamesFin = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadfinancialfile']), 'financials' );
			$uploadedFileNamesExe = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadexesumfile']), 'executive' );
					
					
			$uploadedFileNamesMem = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadmemofile']), 'memorandum' );
			$uploadedFileNamesAdd = $this->uploadMedia( $path, $this->reArrayFilesMultiple($_FILES['uploadadditionfile']), 'additional' );

			$fileInfoToSaveInDB = array_merge( $uploadedFileNames, $uploadedFileNamesExe, $uploadedFileNamesMem, $uploadedFileNamesFin, $uploadedFileNamesAdd ,$uploadedVideos);

			if( $this->user->saveAllDetailsAdmin( $fileInfoToSaveInDB ) ){
				/*if( strcasecmp( $frmValue, 'UA_FU' ) == 0 ){
					//$next_tab = $this->input->post('next_tab');
					//return array('status'=>'true','next_tab'=>$next_tab);
					
				}*/
				redirect('admin/jv_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
			}
				
				/*if($dbResult5){
				
				redirect('admin/seller_explore/'.$this->input->post('user_id').'/'.$this->input->post('formId').'/'.$this->input->post('customer_id').'/abc');
				*/
	}	
	
	public function deleteImgid(){
		
		$this->load->model('user_model', 'user');
		$dbResult = $this->user->DeleteSellerImg();
		
	}
	public function DeleteProperty($idval){
		
		$this->load->model('User_model', 'user');
        $data['delete'] = $this->user->DeleteProperty($idval);
		
	}
	public function DeleteFinProperty($idval){
		
		$this->load->model('User_model', 'user');
        $data['delete'] = $this->user->DeleteFinProperty($idval);
		
	}
	
	public function AddProperty(){
		
		$idval=$this->input->get('idval');
		
		$this->load->model('User_model', 'user');
       $data['addProperty']=$this->user->AddProperty($idval);
	   
	   echo $data['addProperty'];
	   
	  // die;
		
	}
	
	public function AddFinProperty(){
		
		$idval=$this->input->get('idval');
		
		$this->load->model('User_model', 'user');
        $data['addProperty']=$this->user->AddFinProperty($idval);
		
		 echo $data['addProperty'];
		
	}
		
	public function UpdateSellerCityState(){
		
	
		
					$form_Id = $this->input->post('form_Id');	
					$redirect = $this->input->post('redirect');	
					$this->load->model('admin/Seller_model','seller');
					
					$dbResult1 = $this->seller->UpdateSellerCityState($form_Id);
					
					if($dbResult1=='false'){
				$this->session->set_flashdata('msg', 'Oops! some thing went wrong please try again.');
				
				
			}else if($dbResult1=='true'){
				$this->session->set_flashdata('msg', 'State & City approved successfully!.');
				
			}
					
					redirect($redirect);//Redirect 
					
					
	}
	
	public function getApprovedCount() //for all approved seller form
		{
			
				$this->load->model('admin/Seller_model', 'seller');
				$data['queryCount']  = $this->seller->getApprovedCount('sell_a_business');
				echo $data['queryCount'];
			
		}

	public function getPendingCount() //for all pending seller form
		{
			
				$this->load->model('admin/Seller_model', 'seller');
				$data['queryCount']  = $this->seller->getPendingCount('sell_a_business');
				echo $data['queryCount'];
			
		}
public function getRejectedCount() //for all pending seller form
		{
			
				$this->load->model('admin/Seller_model', 'seller');
				$data['queryCount']  = $this->seller->getRejectedCount('sell_a_business');
				echo $data['queryCount'];
			
		}		
public function getInactiveCount() //for all pending seller form
		{
			
				$this->load->model('admin/Seller_model', 'seller');
				$data['queryCount']  = $this->seller->getInactiveCount('sell_a_business');
				echo $data['queryCount'];
			
		}
public function getClosedCount() //for all closed seller form
		{
			
				$this->load->model('admin/Seller_model', 'seller');
				$data['queryCount']  = $this->seller->getClosedCount('sell_a_business');
				echo $data['queryCount'];
			
		}
public function getAllCount() //for all seller form
		{
			
				$this->load->model('admin/Seller_model', 'seller');
				$data['queryCount']  = $this->seller->getAllCount('sell_a_business');
				echo $data['queryCount'];
			
		}		
 /***************download report in xls format for users start here*******/
 public function SellReport()

  {

  // filename for download

 
  $this->load->model('admin/Seller_model', 'seller');
  $data['userBusinessData']  = $this->seller->getSellExport();
  
 // print_r($data['userBusinessData']);
  
 // die;
  
  $filename = "seller-data-" . date('d-m-Y') . ".xls";



  
  
  $this->load->view('admin/sell_data_report', $data);
  
  header("Content-Disposition: attachment; filename=\"$filename\"");

  header("Content-Type: application/vnd.ms-excel");
  
  
  
  
 
	}	
/***************download report in xls format for users end here*********/	

	
		//seller favorite list
	public function GetFavorite(){
		
			$formid=$this->input->get('idval');
		
			 $this->load->model('admin/Seller_model', 'seller');
			
			$data['Favoritelist'] = $this->seller->GetFavoriteList($formid);
			
			echo '<table id="" class="table table-striped table-bordered table-hover">
    <thead>
     <tr>
      <th class="center">Sr.No.</th>
      <th class="center">Email</th>
      <th class="center">Date</th>
     
     </tr>
    </thead>
    <tbody>';
	$i=1;
	foreach($data['Favoritelist'] as $list){
	echo '<tr>
      <th class="center">'.$i++.'</th>
      <th class="center">'.$list['like_user_id'].'</th>
      <th class="center">'.$list['like_datetime'].'</th>
     
     </tr>'; 
	}
	
	echo ' </tbody>
   </table>';
			
			
		
	}
	
	//seller viewed list
	public function GetContactlist(){
		
			$formid=$this->input->get('idval');
		
			 $this->load->model('admin/Seller_model', 'seller');
			
			$data['Favoritelist'] = $this->seller->GetContactlist($formid);
			
			echo '<table id="" class="table table-striped table-bordered table-hover">
    <thead>
     <tr>
      <th class="center">Sr.No.</th>
    
      <th class="center">Email</th>
    
      <th class="center">Date</th>
     
     </tr>
    </thead>
    <tbody>';
	$i=1;
	foreach($data['Favoritelist'] as $list){
	echo '<tr>
      <th class="center">'.$i++.'</th>
    
      <th class="center">'.$list['viewed_user_id'].'</th>
    
      <th class="center">'.$list['view_datetime'].'</th>
     
     </tr>'; 
	}
	
	echo ' </tbody>
   </table>';
			
			
		
	}

	//seller viewed list
	public function GetSellerCount(){
		
			$formid=$this->input->get('idval');
		
			 $this->load->model('admin/Seller_model', 'seller');
			
			$data['Favoritelist'] = $this->seller->GetSellerCount($formid);
			
			echo '<table id="" class="table table-striped table-bordered table-hover">
    <thead>
     <tr>
      <th class="center">Sr.No.</th>
    
      <th class="center">Name</th>
      <th class="center">Email</th>
      <th class="center">Mobile</th>
      <th class="center">Message</th>
    
      <th class="center">Date</th>
     
     </tr>
    </thead>
    <tbody>';
	$i=1;
	foreach($data['Favoritelist'] as $list){
	echo '<tr>
      <th class="center">'.$i++.'</th>
    
      <th class="center">'.$list['name'].'</th>
      <th class="center">'.$list['email'].'</th>
      <th class="center">'.$list['mobile'].'</th>
    
      <th class="center">'.$list['message'].'</th>
      <th class="center">'.$list['date'].'</th>
     
     </tr>'; 
	}
	
	echo ' </tbody>
   </table>';
			
			
		
	}	
}
?>