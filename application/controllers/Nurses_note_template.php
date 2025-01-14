<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nurses_note_template extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        auth_users();
        $this->load->model('nurses_note_template/Nurses_note_template_model','nurses_note_template');
        $this->load->library('form_validation');
    }


    public function index()
    { 
        //unauthorise_permission('71','446');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('446',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $data['page_title'] = 'Nurses Note Template List'; 
        $this->load->view('nurses_note_template/list',$data);
    }

    public function ajax_list()
    { 
        //unauthorise_permission('71','446');
        $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('446',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $users_data = $this->session->userdata('auth_users');
        $list = $this->nurses_note_template->get_datatables();  
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        $total_num = count($list);
        foreach ($list as $diagnosis) {
         
            $no++;
            $row = array();
            if($diagnosis->status==1)
            {
                $status = '<font color="green">Active</font>';
            }   
            else{
                $status = '<font color="red">Inactive</font>';
            } 
            
            ////////// Check  List /////////////////
            $check_script = "";
            if($i==$total_num)
            {
            $check_script = "<script>$('#selectAll').on('click', function () { 
                                  if ($(this).hasClass('allChecked')) {
                                      $('.checklist').prop('checked', false);
                                  } else {
                                      $('.checklist').prop('checked', true);
                                  }
                                  $(this).toggleClass('allChecked');
                              })</script>";
            }                 
            ////////// Check list end ///////////// 
            $row[] = '<input type="checkbox" name="employee[]" class="checklist" value="'.$diagnosis->id.'">'.$check_script; 
            $row[] = $diagnosis->name;  
            $row[] = $status;
            //$row[] = date('d-M-Y H:i A',strtotime($diagnosis->created_date)); 
            
          $btnedit='';
          $btndelete='';
          //if(in_array('448',$users_data['permission']['action'])){
          if(in_array('448',$permission_action) || in_array('121',$permission_section)){
               $btnedit = ' <a onClick="return edit_diagnosis('.$diagnosis->id.');" class="btn-custom" href="javascript:void(0)" style="'.$diagnosis->id.'" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
          }
           //if(in_array('449',$users_data['permission']['action'])){
          if(in_array('449',$permission_action) || in_array('121',$permission_section)){
               $btndelete = ' <a class="btn-custom" onClick="return delete_diagnosis('.$diagnosis->id.')" href="javascript:void(0)" title="Delete" data-url="512"><i class="fa fa-trash"></i> Delete</a> '; 
          }
          $row[] = $btnedit.$btndelete;
             
        
            $data[] = $row;
            $i++;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->nurses_note_template->count_all(),
                        "recordsFiltered" => $this->nurses_note_template->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    
    public function add()
    {
        //unauthorise_permission('71','447');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('447',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $data['page_title'] = "Add Nurses Note Template";  
        $post = $this->input->post();
        $data['form_error'] = []; 
        $data['form_data'] = array(
                                  'data_id'=>"", 
                                  'diagnosis'=>"",
                                  'status'=>"1"
                                  );    

        if(isset($post) && !empty($post))
        {   
            $data['form_data'] = $this->_validate('');
           
            if($this->form_validation->run() == TRUE)
            {
                $this->nurses_note_template->save();
                echo 1;
                return false;
                
            }
            else
            {
                $data['form_error'] = validation_errors();  
            }     
        }
       $this->load->view('nurses_note_template/add',$data);       
    }
    
    public function edit($id="")
    {
      //unauthorise_permission('71','448');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('448',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
     if(isset($id) && !empty($id) && is_numeric($id))
      {      
        $result = $this->nurses_note_template->get_by_id($id);  
        $data['page_title'] = "Update Nurses Note Template";  
        $post = $this->input->post();
        $data['form_error'] = ''; 
        $data['form_data'] = array(
                                  'data_id'=>$result['id'],
                                  'name'=>$result['name'], 
                                  'content'=>$result['content'], 
                                  'status'=>$result['status']
                                  );  
        
        if(isset($post) && !empty($post))
        {   
            $data['form_data'] = $this->_validate($id);
            if($this->form_validation->run() == TRUE)
            {
                $this->nurses_note_template->save();
                echo 1;
                return false;
                
            }
            else
            {
                $data['form_error'] = validation_errors();  
            }     
        }
       $this->load->view('nurses_note_template/add',$data);       
      }
    }
     
    private function _validate($id='')
    {
        $post = $this->input->post();    
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');  
        $this->form_validation->set_rules('name', 'name', 'trim|required'); 
        
        if ($this->form_validation->run() == FALSE) 
        {  
            $reg_no = generate_unique_id(2); 
            $data['form_data'] = array(
                                        'data_id'=>$post['data_id'],
                                        'name'=>$post['name'], 
                                        'status'=>$post['status']
                                       ); 
            return $data['form_data'];
        }   
    }
 
    public function delete($id="")
    {
       //unauthorise_permission('71','449');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('449',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
       if(!empty($id) && $id>0)
       {
           $result = $this->nurses_note_template->delete($id);
           $response = "Nurses Note Template successfully deleted.";
           echo $response;
       }
    }

    function deleteall()
    {
       //unauthorise_permission('71','449');
        $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('449',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $post = $this->input->post();  
        if(!empty($post))
        {
            $result = $this->nurses_note_template->deleteall($post['row_id']);
            $response = "Nurses Note Template successfully deleted.";
            echo $response;
        }
    }

    public function view($id="")
    {  
     if(isset($id) && !empty($id) && is_numeric($id))
      {      
        $data['form_data'] = $this->nurses_note_template->get_by_id($id);  
        $data['page_title'] = $data['form_data']['diagnosis']." detail";
        $this->load->view('nurses_note_template/view',$data);     
      }
    }  


    ///// employee Archive Start  ///////////////
    public function archive()
    {
        //unauthorise_permission('71','450');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('450',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $data['page_title'] = 'Nurses Note Template archive list';
        $this->load->helper('url');
        $this->load->view('nurses_note_template/archive',$data);
    }

    public function archive_ajax_list()
    {
       //unauthorise_permission('71','450');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('450',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
         $users_data = $this->session->userdata('auth_users');
        $this->load->model('nurses_note_template/diagnosis_archive_model','diagnosis_archive'); 

        $list = $this->diagnosis_archive->get_datatables();  
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        $total_num = count($list);
        foreach ($list as $diagnosis) { 
            $no++;
            $row = array();
            if($diagnosis->status==1)
            {
                $status = '<font color="green">Active</font>';
            }   
            else{
                $status = '<font color="red">Inactive</font>';
            } 
            
            ////////// Check  List /////////////////
            $check_script = "";
            if($i==$total_num)
            {
            $check_script = "<script>$('#selectAll').on('click', function () { 
                                  if ($(this).hasClass('allChecked')) {
                                      $('.checklist').prop('checked', false);
                                  } else {
                                      $('.checklist').prop('checked', true);
                                  }
                                  $(this).toggleClass('allChecked');
                              })</script>";
            }                 
            ////////// Check list end ///////////// 
            $row[] = '<input type="checkbox" name="employee[]" class="checklist" value="'.$diagnosis->id.'">'.$check_script; 
            $row[] = $diagnosis->diagnosis;  
            $row[] = $status;
            //$row[] = date('d-M-Y H:i A',strtotime($diagnosis->created_date)); 
           
          $btnrestore='';
          $btndelete='';
          //if(in_array('452',$users_data['permission']['action'])){
          if(in_array('452',$permission_action) || in_array('121',$permission_section)){
               $btnrestore = ' <a onClick="return restore_diagnosis('.$diagnosis->id.');" class="btn-custom" href="javascript:void(0)"  title="Restore"><i class="fa fa-window-restore" aria-hidden="true"></i> Restore </a>';
          }
          //if(in_array('451',$users_data['permission']['action'])){
          if(in_array('451',$permission_action) || in_array('121',$permission_section)){
               $btndelete = ' <a onClick="return trash('.$diagnosis->id.');" class="btn-custom" href="javascript:void(0)" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>'; 
          }
          $row[] = $btnrestore.$btndelete;
        
            $data[] = $row;
            $i++;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->diagnosis_archive->count_all(),
                        "recordsFiltered" => $this->diagnosis_archive->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function restore($id="")
    {
        //unauthorise_permission('71','452');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('452',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $this->load->model('nurses_note_template/diagnosis_archive_model','diagnosis_archive');
       if(!empty($id) && $id>0)
       {
           $result = $this->diagnosis_archive->restore($id);
           $response = "Nurses Note Template successfully restore in Nurses Note Template list.";
           echo $response;
       }
    }

    function restoreall()
    { 
        //unauthorise_permission('71','452');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('452',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $this->load->model('nurses_note_template/diagnosis_archive_model','diagnosis_archive');
        $post = $this->input->post();  
        if(!empty($post))
        {
            $result = $this->diagnosis_archive->restoreall($post['row_id']);
            $response = "Nurses Note Template successfully restore in Nurses Note Template list.";
            echo $response;
        }
    }

    public function trash($id="")
    {
        //unauthorise_permission('71','451');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('451',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $this->load->model('nurses_note_template/diagnosis_archive_model','diagnosis_archive');
       if(!empty($id) && $id>0)
       {
           $result = $this->diagnosis_archive->trash($id);
           $response = "Nurses Note Template successfully deleted parmanently.";
           echo $response;
       }
    }

    function trashall()
    {
        //unauthorise_permission('71','451');
      $users_data = $this->session->userdata('auth_users');
        if (array_key_exists("permission",$users_data))
        {
             $permission_section = $users_data['permission']['section'];
             $permission_action = $users_data['permission']['action'];
        }
        else
        {
             $permission_section = array();
             $permission_action = array();
        }
        if(in_array('451',$permission_action) || in_array('121',$permission_section))
        {

        }
        else
        {
          redirect('401');
        }
        $this->load->model('nurses_note_template/diagnosis_archive_model','diagnosis_archive');
        $post = $this->input->post();  
        if(!empty($post))
        {
            $result = $this->diagnosis_archive->trashall($post['row_id']);
            $response = "Nurses Note Template successfully deleted parmanently.";
            echo $response;
        }
    }
    ///// employee Archive end  ///////////////

  public function diagnosis_dropdown()
  {

      $diagnosis_list = $this->nurses_note_template->nurses_note_list();
      $dropdown = '<option value="">Select Nurses Note Template</option>'; 
      if(!empty($diagnosis_list))
      {
        foreach($diagnosis_list as $diagnosis)
        {
           $dropdown .= '<option value="'.$diagnosis->id.'">'.$diagnosis->name.'</option>';
        }
      } 
      echo $dropdown; 
  }
  
  function check_unique_value($diagnosis, $id='') 
  {     
        $users_data = $this->session->userdata('auth_users');
        $result = $this->nurses_note_template->check_unique_value($users_data['parent_id'], $diagnosis, $id);
        if($result == 0)
            $response = true;
        else {
            $this->form_validation->set_message('check_unique_value', 'This Nurses Note Template already exist.');
            $response = false;
        }
        return $response;
    }

}
?>