<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hospital_code_entry extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        auth_users();
        $this->load->model('hospital_code_entry/hospital_code_entry_model', 'hospital_entry');
        $this->load->library('form_validation');
    }


    public function index()
    {
        // echo "<pre>";
        // print_r('He;llo');
        // die;
        unauthorise_permission(56, 367);
        $data['page_title'] = 'Hospital Code Entry List';
        $data['manuf_company_list'] = $this->hospital_entry->manuf_company_list();
        $this->session->unset_userdata('medicine_entry_search');
        // Default Search Setting
        $this->load->model('default_search_setting/default_search_setting_model');
        $default_search_data = $this->default_search_setting_model->get_default_setting();
        if (isset($default_search_data[1]) && !empty($default_search_data) && $default_search_data[1] == 1) {
            $start_date = '';
            $end_date = '';
        } else {
            $start_date = date('d-m-Y');
            $end_date = date('d-m-Y');
        }
        // End Defaul Search
        $data['form_data'] = array('start_date' => $start_date, 'medicine_name' => '', 'end_date' => $end_date, 'medicine_company' => '');
        $this->load->view('hospital_code_entry/list', $data);
    }

    public function ajax_list()
    {
        unauthorise_permission(56, 367);
        $list = $this->hospital_entry->get_datatables();
        $branch_type = $this->session->userdata('medicine_entry_search');
        // echo "<pre>";
        // print_r( $list);
        // die;
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        $total_num = count($list);
        //$row='';
        foreach ($list as $hospital_code_entry) {
            $no++;
            $row = array();
            if ($hospital_code_entry->status == 1) {
                $status = '<font color="green">Active</font>';
            } else {
                $status = '<font color="red">Inactive</font>';
            }
            ///// State name ////////
            $state = "";
            if (!empty($hospital_entry->state)) {
                $state = " ( " . ucfirst(strtolower($hospital_entry->state)) . " )";
            }
            //////////////////////// 

            ////////// Check  List /////////////////
            $check_script = "";
            if ($i == $total_num) {

                $check_script = "<script>$('#selectAll').on('click', function () { 
                                  if ($(this).hasClass('allChecked')) {
                                      $('.checklist').prop('checked', false);
                                  } else {
                                      $('.checklist').prop('checked', true);
                                  }
                                  $(this).toggleClass('allChecked');
                              })</script>";
            }
            // $doctor_type = array('0'=>'Referral','1'=>'Attended','2'=>'Referral/Attended');
            $row[] = '<input type="checkbox" name="hospital_code_entry[]" class="checklist" value="' . $hospital_code_entry->id . '">' . $check_script;
            $rack_name = rack_list($hospital_entry->rack_no);
            $row[] = $hospital_code_entry->hospital_code;
            $row[] = $hospital_code_entry->item_desc;
            $row[] = $hospital_code_entry->medicine_unit;
            $row[] = $hospital_code_entry->company_name;
            $row[] = $hospital_code_entry->qty;
            $row[] = $hospital_code_entry->hospital_rate;
            // $row[] = $hospital_code_entry->rack_no;
            // $row[] = $hospital_code_entry->mrp;
            // $row[] = $hospital_code_entry->purchase_rate;
            // $row[] = $hospital_code_entry->discount;
            $row[] = $status;
            $users_data = $this->session->userdata('auth_users');
            $parent_branch_details = $this->session->userdata('parent_branches_data');

            $btnedit = '';
            $btndelete = '';
            $btnview = '';
            $btn_print_barcode = '';
            //if($parent_branch_details[0]['parent_id']==$branch_type['branch_type'] && $users_data['users_role']!=1)
            /*if($parent_branch_details[0]['parent_id']==$branch_type['branch_type'] && $users_data['users_role']!=1)
            {
              $btnedit='';
              $btnview='';
              $btndelete='';
            }
            else
            {*/
            //if(in_array('369',$users_data['permission']['action'])){
            $btnedit = ' <a onClick="return edit_medicine_entry(' . $hospital_code_entry->id . ');" class="btn-custom" href="javascript:void(0)" style="' . $hospital_code_entry->id . '" title="Edit"><i class="fa fa-pencil"></i> Edit</a>';
            //}
            // if (in_array('374', $users_data['permission']['action'])) {
            //     $btnview = ' <a class="btn-custom" onclick="return view_medicine_entry(' . $hospital_code_entry->id . ')" href="javascript:void(0)" title="View"><i class="fa fa-info-circle"></i> View </a>';
            // }
            if (in_array('370', $users_data['permission']['action'])) {
                $btndelete = ' <a class="btn-custom" onClick="return delete_medicine_entry(' . $hospital_code_entry->id . ')" href="javascript:void(0)" title="Delete" data-url="512"><i class="fa fa-trash"></i> Delete</a> ';
            }

            $print_barcode_url = "'" . base_url('hospital_code_entry/print_barcode/') . $hospital_code_entry->id . "'";
            // $btn_print_barcode = ' <a class="btn-custom" href="javascript:void(0)" onclick = "return print_window_page(' . $print_barcode_url . ');" title="Print Bill" ><i class="fa fa-print"></i> Print Barcode </a>';

            // $btn_print_barcode .= ' <a class="btn-custom"  href="javascript:void(0)" onClick="return print_label(' . $hospital_code_entry->id . ');"  title="Print Label" ><i class="fa fa-barcode"></i> Print Label </a>';

            // }

            $row[] = $btnedit . $btnview . $btndelete . $btn_print_barcode;
            $data[] = $row;
            $i++;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->hospital_entry->count_all(),
            "recordsFiltered" => $this->hospital_entry->count_filtered(),
            "data" => $data,
        );
        //print_r($output);
        //output to json format
        echo json_encode($output);
    }

    public function test_record()
    {
        $data = $this->hospital_entry->count_filtered();
        echo "<pre>";
        print_r($data);
    }

    public function medicine_entry_excel()
    {
        $this->load->library('excel');
        $this->excel->IO_factory();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        $objPHPExcel->setActiveSheetIndex(0);
        // Field names in the first row
        $fields = array('Hospital Code', 'Item Desc', 'Unit', 'Manufacturer', 'Qty', 'Hospital Rate', 'Created Date');
        $col = 0;
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        foreach ($fields as $field) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $col++;
        }
        $list = $this->hospital_entry->search_report_data();
        $rowData = array();
        $data = array();
        if (!empty($list)) {

            $i = 0;
            foreach ($list as $reports) {

                array_push($rowData, $reports->hospital_code, $reports->item_desc, $reports->medicine_unit, $reports->company_name, $reports->qty, $reports->hospital_rate, date('d-M-Y H:i A', strtotime($reports->created_date)));
                $count = count($rowData);
                for ($j = 0; $j < $count; $j++) {

                    $data[$i][$fields[$j]] = $rowData[$j];
                }
                unset($rowData);
                $rowData = array();
                $i++;
            }

        }

        // Fetching the table data
        $row = 2;
        if (!empty($data)) {
            foreach ($data as $boking_data) {
                $col = 0;
                foreach ($fields as $field) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $boking_data[$field]);
                    $col++;
                }
                $row++;
            }
            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        }

        // Sending headers to force the user to download the file
        //header('Content-Type: application/octet-stream');
        header('Content-Type: application/vnd.ms-excel charset=UTF-8');
        header("Content-Disposition: attachment; filename=hospital_code_list_" . time() . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        if (!empty($data)) {
            ob_end_clean();
            $objWriter->save('php://output');
        }
    }

    public function medicine_entry_csv()
    {
        // Starting the PHPExcel library
        $this->load->library('excel');
        $this->excel->IO_factory();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        $objPHPExcel->setActiveSheetIndex(0);
        // Field names in the first row
        $fields = array('Medicine Code', 'Medicine Name', 'Medicine company', 'Packing', 'Rack No.', 'MRP', 'Purchase Rate');
        $col = 0;
        foreach ($fields as $field) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $col++;
        }
        $list = $this->hospital_entry->search_report_data();
        $rowData = array();
        $data = array();
        if (!empty($list)) {

            $i = 0;
            foreach ($list as $reports) {

                array_push($rowData, $reports->medicine_code, $reports->medicine_name, $reports->company_name, $reports->packing, $reports->rack_no, $reports->mrp, $reports->purchase_rate);
                $count = count($rowData);
                for ($j = 0; $j < $count; $j++) {

                    $data[$i][$fields[$j]] = $rowData[$j];
                }
                unset($rowData);
                $rowData = array();
                $i++;
            }

        }

        // Fetching the table data
        $row = 2;
        if (!empty($data)) {
            foreach ($data as $reports_data) {
                $col = 0;
                foreach ($fields as $field) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $reports_data[$field]);
                    $col++;
                }
                $row++;
            }
            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        }

        // Sending headers to force the user to download the file
        header('Content-Type: application/octet-stream charset=UTF-8');
        header("Content-Disposition: attachment; filename=medicine_report_" . time() . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        if (!empty($data)) {
            ob_end_clean();
            $objWriter->save('php://output');
        }

    }

    public function pdf_medicine_entry()
    {
        $data['print_status'] = "";
        $data['data_list'] = $this->hospital_entry->search_report_data();
        // echo "<pre>";
        // print_r($data['data_list']);
        // die;
        $this->load->view('hospital_code_entry/medicine_report_html', $data);
        $html = $this->output->get_output();
        // Load library
        $this->load->library('pdf');
        //echo $html; exit;
        // Convert to PDF
        $this->pdf->load_html($html);
        $this->pdf->render();
        $this->pdf->stream("hospital_code_list_" . time() . ".pdf");
    }
    public function print_medicine_entry()
    {
        $data['print_status'] = "1";
        $data['data_list'] = $this->hospital_entry->search_report_data();
        $this->load->view('hospital_code_entry/medicine_report_html', $data);
    }
    public function add()
    {
        unauthorise_permission(56, 368);
        $this->load->model('general/general_model');
        $data['page_title'] = "Hospital Code Entry";
        $data['form_error'] = [];
        $data['hospital_code_list'] = $this->hospital_entry->hospital_code_list();
        $data['item_desc_list'] = $this->hospital_entry->item_desc_list();
        $data['unit_list'] = $this->hospital_entry->unit_list();
        $data['manuf_company_list'] = $this->hospital_entry->manuf_company_list();
        $data['medicine_type_list'] = $this->hospital_entry->medicine_type_list();
        $data['rack_list'] = $this->hospital_entry->rack_list();
        $reg_no = generate_unique_id(10);
        //echo $reg_no;die;
        $post = $this->input->post();
        // echo "<pre>";
        // print_r($post);
        // die;
        $data['form_data'] = array(
            "data_id" => "",
            "branch_id" => "",
            "hos_code_id" => '',
            "unit_id" => "",
            "item_desc_id" => "",
            "manuf_company" => "",
            "qty" => "",
            "hospital_rate" => "",
            "status" => "1",
        );
        if (isset($post) && !empty($post)) {
            $data['form_data'] = $this->_validate();
            if ($this->form_validation->run() == TRUE) {
                $this->hospital_entry->save();
                echo 1;
                return false;
            } else {
                $data['form_error'] = validation_errors();
            }
        }
        $this->session->unset_userdata('comission_data');
        $this->load->model('inventory_discount_setting/inventory_discount_setting_model');
        $discount_setting_data = $this->inventory_discount_setting_model->get_default_setting();

        $data['discount_setting'] = $discount_setting_data[1];
        $this->load->view('hospital_code_entry/add', $data);
    }



    public function reset_search()
    {
        $this->session->unset_userdata('medicine_entry_search');
    }
    public function advance_search()
    {

        $this->load->model('general/general_model');
        $data['page_title'] = "Advance Search";
        $post = $this->input->post();
        $data['simulation_list'] = $this->general_model->simulation_list();
        $data['doctors_list'] = $this->general_model->doctors_list();
        $data['unit_list'] = $this->hospital_entry->unit_list();
        $data['form_data'] = array(
            "start_date" => "",
            "end_date" => "",
            "hos_code_id" => "",
            "unit1" => "",
            "item_desc_id" => "",
            "qty" => "",
            "hospital_rate" => "",
        );
        if (isset($post) && !empty($post)) {
            //print_r($post);die;
            $marge_post = array_merge($data['form_data'], $post);
            $this->session->set_userdata('medicine_entry_search', $marge_post);
        }
        $medicine_entry_search = $this->session->userdata('medicine_entry_search');
        if (isset($medicine_entry_search) && !empty($medicine_entry_search)) {
            $data['form_data'] = $medicine_entry_search;
        }
        $this->load->view('hospital_code_entry/advance_search', $data);
    }

    public function edit($id = "")
    {
        unauthorise_permission(56, 369);
        if (isset($id) && !empty($id) && is_numeric($id)) {
            $result = $this->hospital_entry->get_by_id($id);
            // print_r($result);die;
            //$reg_no = generate_unique_id(10);
            $this->load->model('general/general_model');
            $data['unit_list'] = $this->hospital_entry->unit_list();
            $data['manuf_company_list'] = $this->hospital_entry->manuf_company_list();
            $data['hospital_code_list'] = $this->hospital_entry->hospital_code_list();
            $data['item_desc_list'] = $this->hospital_entry->item_desc_list();
            $data['medicine_type_list'] = $this->hospital_entry->medicine_type_list();
            $data['page_title'] = "Update Hospital Code Entry";
            $post = $this->input->post();
            $data['form_error'] = '';
            $data['form_data'] = array(
                "data_id" => $result['id'],
                "hos_code_id" => $result['hos_code_id'],
                "unit_id" => $result['unit_id'],
                "item_desc_id" => $result['item_desc_id'],
                "manuf_company" => $result['manuf_company'],
                "qty" => $result['qty'],
                "hospital_rate" => $result['hospital_rate'],
                "status" => $result['status'],
            );

            if (isset($post) && !empty($post)) {
                $data['form_data'] = $this->_validate();
                if ($this->form_validation->run() == TRUE) {
                    $this->hospital_entry->save();
                    echo 1;
                    return false;

                } else {
                    $data['form_error'] = validation_errors();
                }
            }

            $this->session->unset_userdata('comission_data');
            $this->load->model('inventory_discount_setting/inventory_discount_setting_model');
            $discount_setting_data = $this->inventory_discount_setting_model->get_default_setting();

            $data['discount_setting'] = $discount_setting_data[1];
            $this->load->view('hospital_code_entry/add', $data);
        }
    }

    private function _validate()
    {
        $field_list = mandatory_section_field_list(5);
        $users_data = $this->session->userdata('auth_users');
        $post = $this->input->post();
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
        $this->form_validation->set_rules('hos_code_id', 'medicine name', 'trim|required');
        $this->form_validation->set_rules('unit_id', 'unit', 'trim|required');
        $this->form_validation->set_rules('item_desc_id', 'unit 2nd.', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $data['form_data'] = array(
                "data_id" => $_POST['data_id'],
                "hos_code_id" => $_POST['hos_code_id'],
                "unit_id" => $_POST['unit_id'],
                "item_desc_id" => $_POST['item_desc_id'],
                "manuf_company" => $_POST['manuf_company'],
                // "qty" => $_POST['qty'],
                // "hospital_rate" => $_POST['hospital_rate'],
                "status" => $_POST['status'],
            );
            return $data['form_data'];
        }
    }

    public function check_purchase_rate($str)
    {

        $post = $this->input->post();
        if (!empty($str)) {
            $this->load->model('general/general_model', 'general');
            if (!empty($post['data_id']) && $post['data_id'] > 0) {

                if (!empty($post['purchase_rate']) && $post['purchase_rate'] > $post['mrp']) {
                    $this->form_validation->set_message('check_purchase_rate', 'Purchase rate must be less and equal to MRP');
                    return false;
                } else {
                    return true;
                }
            } else {
                if (!empty($post['purchase_rate']) && $post['purchase_rate'] > $post['mrp']) {
                    $this->form_validation->set_message('check_purchase_rate', 'Purchase rate must be less and equal to MRP');
                    return false;
                } else {
                    return true;
                }
            }
        }
        /* else
         {
           $this->form_validation->set_message('check_purchase_rate', 'Purchase rate field is required.');
                 return false; 
         } */
    }


    public function delete($id = "")
    {
        unauthorise_permission(56, 370);
        if (!empty($id) && $id > 0) {
            $result = $this->hospital_entry->delete($id);
            $response = "Medicine entry successfully deleted.";
            echo $response;
        }
    }

    function deleteall()
    {
        unauthorise_permission(56, 370);
        $post = $this->input->post();
        if (!empty($post)) {
            $result = $this->hospital_entry->deleteall($post['row_id']);
            $response = "Medicine entry successfully deleted.";
            echo $response;
        }
    }

    function allbranch_typedata()
    {
        unauthorise_permission(56, 367);
        $post = $this->input->post();
        if (!empty($post)) {

            $result = $this->hospital_entry->check_medicine($post['row_id'], $post['branch_id'], $post['self_id']);
            $response = "Medicine has been downloaded successfully.";
            echo $response;
        }
    }


    public function view($id = "")
    {
        unauthorise_permission(56, 374);
        if (isset($id) && !empty($id) && is_numeric($id)) {
            $data['form_data'] = $this->hospital_entry->get_by_id($id);
            $data['page_title'] = $data['form_data']['medicine_name'] . " detail";
            $this->load->view('hospital_code_entry/view', $data);
        }
    }


    ///// employee Archive Start  ///////////////
    public function archive()
    {
        unauthorise_permission(56, 371);
        $data['page_title'] = 'Medicine entry archive list';
        $this->load->helper('url');
        $this->load->view('hospital_code_entry/archive', $data);
    }

    public function archive_ajax_list()
    {
        unauthorise_permission(56, 371);
        $this->load->model('hospital_code_entry/hospital_code_entry_archive_model', 'hospital_entry_archive');

        $list = $this->hospital_entry_archive->get_datatables();
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        $total_num = count($list);
        foreach ($list as $hospital_code_entry) {
            $no++;
            $row = array();
            if ($hospital_entry->status == 1) {
                $status = '<font color="green">Active</font>';
            } else {
                $status = '<font color="red">Inactive</font>';
            }


            ////////// Check  List /////////////////
            $check_script = "";
            if ($i == $total_num) {

                $check_script = "<script>$('#selectAll').on('click', function () { 
                                  if ($(this).hasClass('allChecked')) {
                                      $('.checklist').prop('checked', false);
                                  } else {
                                      $('.checklist').prop('checked', true);
                                  }
                                  $(this).toggleClass('allChecked');
                              })</script>";
            }

            $row[] = '<input type="checkbox" name="hospital_code_entry[]" class="checklist" value="' . $hospital_code_entry->id . '">' . $check_script;
            $rack_name = rack_list($hospital_code_entry->rack_no);
            $row[] = $hospital_code_entry->hospital_code;
            $row[] = $hospital_code_entry->item_desc;
            $row[] = $hospital_code_entry->medicine_unit;
            $row[] = $hospital_code_entry->company_name;
            $row[] = $status;
            $row[] = date('d-M-Y H:i A', strtotime($hospital_code_entry->created_date));
            $users_data = $this->session->userdata('auth_users');
            $btnrestore = '';
            $btndelete = '';

            if (in_array('373', $users_data['permission']['action'])) {
                $btnrestore = ' <a onClick="return restore_medicine_entry(' . $hospital_code_entry->id . ');" class="btn-custom" href="javascript:void(0)"  title="Restore"><i class="fa fa-window-restore" aria-hidden="true"></i> Restore </a>';
            }
            if (in_array('372', $users_data['permission']['action'])) {
                $btndelete = ' <a onClick="return trash(' . $hospital_code_entry->id . ');" class="btn-custom" href="javascript:void(0)" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
            }
            $row[] = $btnrestore . $btndelete;


            $data[] = $row;
            $i++;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->hospital_entry_archive->count_all(),
            "recordsFiltered" => $this->hospital_entry_archive->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function restore($id = "")
    {
        unauthorise_permission(56, 373);
        $this->load->model('hospital_code_entry/hospital_code_entry_archive_model', 'hospital_entry_archive');
        if (!empty($id) && $id > 0) {
            $result = $this->hospital_entry_archive->restore($id);
            $response = "Hosipital Code entry successfully restore in medicine entry list.";
            echo $response;
        }
    }

    function restoreall()
    {
        unauthorise_permission(56, 373);
        $this->load->model('hospital_code_entry/hospital_code_entry_archive_model', 'hospital_entry_archive');
        $post = $this->input->post();
        if (!empty($post)) {
            $result = $this->hospital_entry_archive->restoreall($post['row_id']);
            $response = "Medicine entry successfully restore in medicine entry list.";
            echo $response;
        }
    }

    public function trash($id = "")
    {
        unauthorise_permission(56, 372);
        $this->load->model('hospital_code_entry/hospital_code_entry_archive_model', 'hospital_entry_archive');
        if (!empty($id) && $id > 0) {
            $result = $this->hospital_entry_archive->trash($id);
            $response = "Hospital code entry successfully deleted parmanently.";
            echo $response;
        }
    }

    function trashall()
    {
        unauthorise_permission(56, 372);
        $this->load->model('hospital_code_entry/hospital_code_entry_archive_model', 'hospital_entry_archive');
        $post = $this->input->post();
        if (!empty($post)) {
            $result = $this->hospital_entry_archive->trashall($post['row_id']);
            $response = "Medicine entry successfully deleted parmanently.";
            echo $response;
        }
    }
    ///// employee Archive end  ///////////////

    public function medicine_entry_dropdown()
    {
        $medicine_entry_list = $this->hospital_entry->employee_type_list();
        $dropdown = '<option value="">Select Hospital Code Entry</option>';
        if (!empty($medicine_entry_list)) {
            foreach ($medicine_entry_list as $hospital_code_entry) {
                $dropdown .= '<option value="' . $hospital_entry->id . '">' . $hospital_entry->medicine_name . '</option>';
            }
        }
        echo $dropdown;
    }


    function get_salt_vals($vals = "")
    {
        if (!empty($vals)) {
            $result = $this->hospital_entry->get_salt_vals($vals);
            if (!empty($result)) {
                echo json_encode($result, true);
            }
        }
    }

    function get_hsn_vals($vals = "")
    {
        if (!empty($vals)) {
            $result = $this->hospital_entry->get_hsn_vals($vals);
            if (!empty($result)) {
                echo json_encode($result, true);
            }
        }
    }


    public function import_medicine_excel()
    {
        //unauthorise_permission(97,628);
        $this->load->library('excel');
        $data['page_title'] = 'Import Medicine excel';
        $arr_data = array();
        $header = array();
        $path = '';

        //print_r($_FILES); die;
        if (isset($_FILES) && !empty($_FILES)) {

            $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
            if (!isset($_FILES['medicine_list']) || $_FILES['medicine_list']['error'] > 0) {

                $this->form_validation->set_rules('medicine_list', 'file', 'trim|required');
            }
            $this->form_validation->set_rules('name', 'name', 'trim|required');
            if ($this->form_validation->run() == TRUE) {

                $config['upload_path'] = DIR_UPLOAD_PATH . 'temp/';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('medicine_list')) {
                    $error = array('error' => $this->upload->display_errors());
                    $data['file_upload_eror'] = $error;

                    //echo "<pre> dfd";print_r(DIR_UPLOAD_PATH.'patient/'); exit;
                } else {
                    $data = $this->upload->data();
                    $path = $config['upload_path'] . $data['file_name'];
                    // echo "<pre>"; print_r($path); exit;
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($path);
                    //get only the Cell Collection
                    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    //extract to a PHP readable array format

                    foreach ($cell_collection as $cell) {
                        $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                        $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                        $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1) {
                            $header[$row][$column] = $data_value;
                        } else {
                            $arr_data[$row][$column] = $data_value;
                        }
                    }
                    //send the data in an array format
                    $data['header'] = $header;
                    $data['values'] = $arr_data;

                }


                if (!empty($arr_data)) {
                    //echo '<pre>'; print_r($arr_data); exit;
                    $arrs_data = array_values($arr_data);
                    $total_medicine = count($arrs_data);

                    $array_keys = array('medicine_name', 'unit_id', 'unit_second_id', 'conversion', 'min_alrt', 'packing', 'rack_no', 'salt', 'manuf_company', 'mrp', 'purchase_rate', 'hsn_no', 'cgst', 'sgst', 'igst', 'discount', 'medicine_type', 'bar_code');

                    $count_array_keys = count($array_keys);
                    $array_values_keys = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S');


                    $j = 0;
                    $m = 0;
                    $data = '';
                    $medicine_all_data = array();
                    for ($i = 0; $i < $total_medicine; $i++) {
                        $med_data_count_values[$i] = count($arrs_data[$i]);
                        for ($p = 0; $p < $count_array_keys; $p++) {
                            if (array_key_exists($array_values_keys[$p], $arrs_data[$i])) {
                                $medicine_all_data[$i][$array_keys[$p]] = $arrs_data[$i][$array_values_keys[$p]];
                            } else {
                                $medicine_all_data[$i][$array_keys[$p]] = "";
                            }
                        }
                    }

                    $this->hospital_entry->save_all_medicine($medicine_all_data);
                }
                if (!empty($path)) {
                    unlink($path);
                }

                echo 1;
                return false;
            } else {
                $data['form_error'] = validation_errors();
            }
        }

        $this->load->view('hospital_code_entry/import_medicine_excel', $data);
    }


    public function sample_import_excel()
    {
        //unauthorise_permission(97,627);
        // Starting the PHPExcel library
        $this->load->library('excel');
        $this->excel->IO_factory();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        $objPHPExcel->setActiveSheetIndex(0);
        // Field names in the first row
        $fields = array('Medicine Name', 'Unit 1', 'Unit 2', 'Conversion', 'min alert', 'packing', 'rack no', 'salt', 'Mfg company', 'MRP', 'Pur Rate', 'HSN code', 'cgst', 'sgst', 'igst', 'discount', 'Medicine Type', 'Barcode');



        $col = 0;
        foreach ($fields as $field) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $col++;
        }
        $rowData = array();
        $data = array();

        // Fetching the table data
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // Sending headers to force the user to download the file
        header('Content-Type: application/vnd.ms-excel charset=UTF-8');
        header("Content-Disposition: attachment; filename=medicine_import_sample_" . time() . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        ob_end_clean();
        $objWriter->save('php://output');

    }

    public function print_barcode($medicine_id = '')
    {
        $users_data = $this->session->userdata('auth_users');
        if (!empty($medicine_id)) {
            $medicine_data = $this->hospital_entry->get_by_id($medicine_id);
            $barcode_setting = barcode_setting($users_data['parent_id']);
            //print_r($barcode_setting);die;
            //$data['total_no'] = $barcode_setting->total_receipt;

            $data['barcode_image'] = $medicine_data['barcode_image'];
            $data['barcode_type'] = $medicine_data['barcode_type'];
            //echo "<pre>";print_r($medicine_data); exit;
            $data['barcode_id'] = $medicine_data['medicine_code'];

            $this->load->view('patient/barcode', $data);
        }
    }

    public function ajax_list_medicine()
    {
        $result_medicine = $this->hospital_entry->medicine_list_search();
        $post = $this->input->post();
        if (count($result_medicine) > 0 && isset($result_medicine)) {
            foreach ($result_medicine as $medicine) {
                if (!in_array($medicine->id, $ids)) {
                    $table .= '<div class="append_row_opt" data-id="' . $medicine->id . '">' . $medicine->medicine_name;
                    $table .= '</div>';
                }
            }
        }

        $output = array('data' => $table);
        echo json_encode($output);
    }

    public function ajax_list_medicine_com()
    {
        $result_medicine_com = $this->hospital_entry->medicine_com_list_search();
        $post = $this->input->post();
        if (count($result_medicine_com) > 0 && isset($result_medicine_com)) {
            foreach ($result_medicine_com as $medicine_com) {
                if (!in_array($medicine_com->id, $ids)) {
                    $table .= '<div class="append_row_opt_com" data-id="' . $medicine_com->id . '">' . $medicine_com->company_name;
                    $table .= '</div>';
                }
            }
        }

        $output = array('data' => $table);
        echo json_encode($output);
    }



    public function print_template($id)
    {
        $data['page_title'] = 'Select No of Label';
        //$post = $this->input->post();  
        $data['id'] = $id;
        $this->load->view('hospital_code_entry/template', $data);

    }


    public function print_label($sale_id, $total_no)
    {

        if (!empty($sale_id)) {
            $get_by_id_data = $this->hospital_entry->get_by_id($sale_id);
            //print_r($get_by_id_data); die;

            $data['barcode_text'] = $get_by_id_data['medicine_code'];
            $data['total_no'] = $total_no;

            $this->load->view('hospital_code_entry/print_label_template', $data);
        }
    }

}