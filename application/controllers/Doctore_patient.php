<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Doctore_patient extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('doctore_patient/Doctore_patient_model', 'doctore_patient');
        $this->load->model('opd/opd_model', 'opd');
        $this->load->model('doctors/Doctors_model', 'doctor');
        $this->load->model('room_master/room_master_model', 'room_master');
        $this->load->library('form_validation');
    }

    public function index()
    {
        // echo "Hiii";die('okay');
        $data['page_title'] = 'Doctore Patient List';
        $this->load->model('default_search_setting/default_search_setting_model');
        $default_search_data = $this->default_search_setting_model->get_default_setting();
        if (isset($default_search_data[1]) && !empty($default_search_data) && $default_search_data[1] == 1) {
            $start_date = '';
            $end_date = '';
        } else {
            $start_date = date('d-m-Y');
            $end_date = date('d-m-Y');
        }
        $data['form_data'] = array('patient_name' => '', 'patient_code' => '', 'start_date' => $start_date, 'end_date' => $end_date, 'emergency_booking' => '', 'doc_id' => '', 'room_id' => '');
        $data['attended_doctor_list'] = $this->opd->attended_doctor_list();
        $data['room_no_list'] = $this->doctore_patient->room_no_list();
        $this->load->view('doctore_patient/list', $data);
    }

    public function ajax_list()
    {
        $users_data = $this->session->userdata('auth_users');
        $list = $this->doctore_patient->get_datatables();
        // echo "<pre>";
        // print_r($list);
        // die;
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        foreach ($list as $doctore_patient) {
            $no++;
            $row = array();
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
            $pat_status = '';
            $patient_status = $this->opd->get_by_id_patient_status($prescription->booking_id);
            if ($patient_status == 1) {
                $pat_status = '<font style="background-color: #228B22;color:white">Vision</font>';
            }
            // else if ($patient_status['doctor'] == '1') {
            //   $pat_status = '<font style="background-color: #1CAF9A;color:white">Doctor</font>';
            // }
            //  else if ($patient_status['optimetrist'] == '1') {
            //   $pat_status = '<font style="background-color: #1CAF9A;color:white">Opt.Optom</font>';
            // } 
            // else if ($patient_status['reception'] == '1') {
            //   $pat_status = '<font style="background-color: #1CAF9A;color:white">Reception</font>';
            // }
            //  else if ($patient_status['arrive'] == '1') {
            //   $pat_status = '<font style="background-color: #1CAF9A;color:white">Arrived</font>';
            // } 
            else {
                $pat_status = '<font style="background-color: #1CAF9A;color:white">Not Arrived</font>';
            }
            $age_y = $doctore_patient->age_y;
            $age_m = $doctore_patient->age_m;
            $age_d = $doctore_patient->age_d;

            $age = "";
            if ($age_y > 0) {
                $year = 'Years';
                if ($age_y == 1) {
                    $year = 'Year';
                }
                $age .= $age_y . " " . $year;
            }
            if ($age_m > 0) {
                $month = 'Months';
                if ($age_m == 1) {
                    $month = 'Month';
                }
                $age .= ", " . $age_m . " " . $month;
            }
            if ($age_d > 0) {
                $day = 'Days';
                if ($age_d == 1) {
                    $day = 'Day';
                }
                $age .= ", " . $age_d . " " . $day;
            }
            $gender = array('0' => 'Female', '1' => 'Male', '2' => 'Others');
            // $row[] = $doctore_patient->id;
            $row[] = '<input type="checkbox" name="prescription[]" class="checklist" value="' . $doctore_patient->id . '">' . $check_script;
            // $row[] = $doctore_patient->patient_code_auto;
            $row[] = $doctore_patient->token_no;
            $row[] = $doctore_patient->booking_code;
            $row[] = $doctore_patient->patient_code;
            $row[] = $doctore_patient->patient_name;
            $row[] = $gender[$doctore_patient->gender];
            $row[] = $doctore_patient->mobile_no;
            $row[] = $age;
            $row[] = $doctore_patient->status == 0 ? '<font color="green">Pending</font>' : '<font color="red">Completed</font>';
            $statuses = explode(',', $doctore_patient->pat_status);

            // Trim any whitespace from the statuses and get the last one
            $last_status = trim(end($statuses));

            // Display the last status with the desired styling
            $row[] = '<font style="background-color: #228B30;color:white">' . $last_status . '</font>';
            // Trim any whitespace from the statuses and get the last one
            $last_status = trim(end($statuses));

            $row[] = $doctore_patient->doctor_name;
            $row[] = $doctore_patient->room_no;
            $row[] = date('d-M-Y', strtotime($doctore_patient->created_date));

            $btn_history = '';
            $btn_prescription = "";
            // if ($doctore_patient->status == 0) {
            //     $flag = 'doct_patie_add_eye';
            //     $type = 'doct_patient';
            //     $row[] = ' <a href="' . base_url("eye/add_eye_prescription/test/" . $doctore_patient->booking_id) . '?flag=' . $flag . "&type=" . $type . '" class="btn-custom" href="javascript:void(0)" style="' . $doctore_patient->id . '" title="Edit"> Add Adv. Eye Prescription</a>
            //                ';
            // } else {
            //     $row[] = '<a class="btn-custom disabled" href="javascript:void(0);" title="Refraction below 8 Years" style="pointer-events: none; opacity: 0.6;" data-url="512">Add Adv. Eye Prescription</a>';

            // }

            if (in_array('524', $users_data['permission']['action'])) {
                // $btn_edit = ' <a class="" href="' . base_url("opd/edit_booking/" . $doctore_patient->booking_id) . '" title="Edit Booking"><i class="fa fa-pencil"></i> Edit</a>';
                $btn_edit = '<a onClick="return edit_medicine_unit(' . $doctore_patient->id . ');" href="javascript:void(0)" style="' . $room_master->id . '" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';

                // $btn_edit = ' <a class="" href="' . base_url("opd/edit_booking/" . $test->id) . '" title="Edit Booking"><i class="fa fa-pencil"></i> Edit</a>';
            }
            $btn_prescription .= '<li><a  href="' . base_url("eye/add_eye_prescription/test/" . $doctore_patient->id) . '" title="Add Prescription"><i class="fa fa-eye"></i> Add Adv. Eye Prescription</a></li>';
            if (in_array('1419', $users_data['permission']['action'])) {
                $print_url_eye = "'" . base_url('eye/add_prescription/print_blank_prescriptions/' . $doctore_patient->booking_id . '/' . $doctore_patient->branch_id) . "'";
                // $btn_prescription .= '<div class="btn-ipd">';
                $btn_prescription .= '<li><a  href="javascript:void(0)" onClick="return print_window_page(' . $print_url_eye . ')" title="Print" ><i class="fa fa-eye"></i
                      > Blank Eye Prescription  </a></li>';
                //$btn_prescription .='</div>';                
            }
            if ($doctore_patient->status == 0) {
                $flag = 'eye_history';
                $type = 'opd_booking';
                // $btn_history .= '<a class="btn-custom" href="' . base_url("eye/add_eye_prescription/test/" . $doctore_patient->booking_id . "?flag=" . $flag . "&type=" . $type) . '" title="Add Prescription"><i class="fa fa-history"></i> History</a>';
                if ($doctore_patient->patient_history_status == 1) {
                    // Render disabled button for already booked patients
                    $btn_history = '<div class="action-buttons">
                                      <button class="btn-custom book-now-btn book-now-btn-ortho-ptics" disabled style="width: 71%;">
                                          <i class="fa fa-spinner fa-spin"></i> In Progress
                                      </button>
                                      <a href="javascript:void(0);" title="Refresh" class="btn btn-secondary refresh-btn-history" data-patient_id="' . $doctore_patient->patient_id . '" >
                                          <i class="fa fa-refresh"></i>
                                      </a>
                                      </div>';
                  } else {
                   
                    $btn_history = '<button class="btn-custom book-now-btn-url-history" title="Hess Chart" 
                          data-id="' . $doctore_patient->patient_id . '" 
                          data-url="' . base_url("eye/add_eye_prescription/test/" . $doctore_patient->booking_id ) . '?flag=' . $flag . "&type=" . $type . '" >History</button>';
                  }
            }
            $btn_a = '<div class="slidedown">
              <button disabled class="btn-custom">More <span class="caret"></span></button>
              <ul class="slidedown-content">
                ' . $btn_edit . $btn_prescription . '
              </ul>
            </div> ';
            // Add action buttons
            //            <a href="javascript:void(0)" class="btn-custom" onClick="return print_window_page(\'' . base_url("doctore_patient/print_vision/" . $doctore_patient->id) . '\');">
            //     <i class="fa fa-print"></i> Print
            // </a>
            $row[] = $btn_history . $btn_a;

            $row[] = $doctore_patient->emergency_status;


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->doctore_patient->count_all(),
            "recordsFiltered" => $this->doctore_patient->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function add($booking_id = null, $patient_id = null, $referred_by = null)
    {
        // echo "<pre>";
        // print_r($booking_id);
        // print_r($patient_id);
        // print_r($referred_by);
        // die('sagar');
        // Load required models and libraries
        $this->load->library('form_validation');
        $this->load->model('doctore_patient/doctore_patient_model'); // Ensure this model is loaded
        $data['side_effects'] = $this->doctore_patient->get_all_side_effects(); // Fetch side effects
        $data['page_title'] = 'Add Doctore';
        $data['booking_id'] = isset($booking_id) ? $booking_id : '';
        $data['patient_id'] = isset($patient_id) ? $patient_id : '';
        $data['booking_data'] = $this->doctore_patient->get_booking_patient_details($data['patient_id']);
        $data['doctor'] = $this->doctor->doctors_list();
        $data['room_no_list'] = $this->doctore_patient->room_no_list();
        // echo "<pre>";
        // print_r($data['room_no_list']);
        // die;
        $patient_details = $this->doctore_patient->get_patient_name_by_booking_id($booking_id);
        if ($booking_id && $patient_details) {
            $data['patient_name'] = $patient_details['patient_name'];
        } else {
            $data['patient_name'] = ''; // Default value if no booking_id is provided
        }

        // Initialize form data
        $data['form_data'] = array(
            "booking_id" => $booking_id,
            "patient_id" => $patient_id,
            "room_id" => '',
            "referred_by" => $referred_by,
        );


        $post = $this->input->post();
        // Check if the form is submitted
        if (isset($post) && !empty($post)) {

            $patient_exists = $this->doctore_patient->patient_exists($post['patient_id']);
            //   echo "<pre>";
            // print_r( $patient_exists);
            // die;
            if ($patient_exists) {
                // Redirect to OPD list page with a warning message
                $this->session->set_flashdata('warning', 'Patient ' . $patient_exists['patient_name'] . ' is already in Refraction above 8 years.');
                echo json_encode(['faield' => true, 'message' => 'Patient ' . $patient_exists['patient_name'] . ' is already in Refraction above 8 years.']);
                // redirect('help_desk'); // Change 'opd_list' to your OPD list page route
                return;
            }
            // Validate the form
            $valid_response = $this->_validate();

            // Check if validation passed
            if ($valid_response === true) {
                // echo "<pre>";
                // print_r('abhay');
                // print_r($post);
                // die;
                // If validation passes, save the record
                $this->doctore_patient->save($this->input->post()); // Save the validated data
                $this->session->set_flashdata('success', 'Room no store successfully.');
                echo json_encode(['success' => true, 'message' => 'Room no store successfully.']);
                return; // Exit to prevent further output
            } else {
                // Handle validation errors
                $data['form_data'] = $valid_response['form_data']; // Retain form data for re-display
                $data['form_error'] = validation_errors(); // Get validation errors
            }
        }




        // If the form is not submitted or validation fails, load the view with the existing data


        // Load the view with the data
        $this->load->view('doctore_patient/add', $data);
    }
    public function book_patient()
    {
        // public function book_patient() {
        $patient_id = $this->input->post('patient_id');
        // $this->load->model('token_no');

        // Perform booking logic
        $booking_result = $this->doctore_patient->book_patient($patient_id);

        if ($booking_result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Booking failed.']);
        }
        // }
    }

    public function check_booking_status()
    {
        $patient_id = $this->input->post('patient_id');
        // $this->load->model('Booking_model');

        // Check status in the database
        $status = $this->doctore_patient->get_booking_status($patient_id);
        // echo "<pre>";
        // print_r($status);
        // die('sagar');
        if ($status == 1) {
            echo json_encode(['status' => '1']); // Already in progress
        } else {
            echo json_encode(['status' => '0']); // Not booked yet
        }
    }

    public function update_status_opd()
    {
        $patientId = $this->input->post('patient_id');

        if (!$patientId) {
            echo json_encode(['status' => 'error', 'message' => 'Patient ID is required.']);
            return;
        }

        // Update status logic
        $updated = $this->doctore_patient->update_patient_list_opd_status($patientId, 'new_status'); // Adjust as needed

        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
        }
    }
    public function book_doc_patient()
    {
        // public function book_patient() {
        $patient_id = $this->input->post('patient_id');
        // $this->load->model('token_no');

        // Perform booking logic
        $booking_result = $this->doctore_patient->book_doc_patient($patient_id);

        if ($booking_result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Booking failed.']);
        }
        // }
    }

    public function check_doc_booking_status()
    {
        $patient_id = $this->input->post('patient_id');
        // $this->load->model('Booking_model');

        // Check status in the database
        $status = $this->doctore_patient->get_booking_doc_status($patient_id);
        // echo "<pre>";
        // print_r($status);
        // die('sagar');
        if ($status == 1) {
            echo json_encode(['status' => '1']); // Already in progress
        } else {
            echo json_encode(['status' => '0']); // Not booked yet
        }
    }

    public function update_status_doc()
    {
        $patientId = $this->input->post('patient_id');

        if (!$patientId) {
            echo json_encode(['status' => 'error', 'message' => 'Patient ID is required.']);
            return;
        }

        // Update status logic
        $updated = $this->doctore_patient->update_patient_list_doc_status($patientId, 'new_status'); // Adjust as needed

        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
        }
    }
    // public function add($booking_id = null)
    // {
    //     $data['side_effects'] = $this->doctore_patient->get_all_side_effects(); // Fetch side effects
    //     $data['page_title'] = 'Add Vision Record';
    //     $data['booking_id'] = isset($booking_id)?$booking_id:'';

    //     // Fetch patient name using booking_id if it is provided
    //     if ($booking_id) {
    //         $data['patient_name'] = $this->doctore_patient->get_patient_name_by_booking_id($booking_id);
    //     } else {
    //         $data['patient_name'] = ''; // Default value if no booking_id is provided
    //     }
    //     // print_r($data['patient_name'] );
    //     // die;

    //     $post = $this->input->post();

    //     if (isset($post) && !empty($post)) {
    //         // Save the form data
    //         $this->doctore_patient->save();

    //         // Return a JSON response indicating success
    //         echo json_encode(['success' => true]);
    //         return; // Exit to prevent further output
    //     }

    //     // If the form is not submitted, load the view
    //     $this->load->view('doctore_patient/add', $data);
    // }

    private function _validate()
    {
        $this->load->library('form_validation');
        $post = $this->input->post();
        // echo "<pre>";print_r($post);die;

        // Assuming this function returns the necessary fields
        $field_list = mandatory_section_field_list(2);
        $users_data = $this->session->userdata('auth_users');
        $data['form_data'] = [];
        $data['photo_error'] = [];

        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

        // Validation rules for required fields
        $this->form_validation->set_rules('room_no', 'Patient Name', 'trim|required');

        // Custom validation for mobile number if certain conditions are met
        // if (!empty($field_list)) {
        //     if ($field_list[0]['mandatory_field_id'] == '5' && $field_list[0]['mandatory_branch_id'] == $users_data['parent_id']) {
        //         $this->form_validation->set_rules('mobile_no', 'Mobile No.', 'trim|required|numeric|min_length[10]|max_length[10]');
        //     }
        // }
        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Prepare form data to retain user inputs
            $data['form_data'] = array(
                "data_id" => isset($post['data_id']) ? $post['data_id'] : '',
                "room_no" => isset($post['room_no']) ? $post['room_no'] : '',
            );

            return $data; // Return the form data with errors
        }
        return true; // Return true if validation passes
    }




    public function edit($id = "")
    {
        // echo "<pre>";
        //     print_r('$post');
        //     print_r($id);
        //     die;
        // Check user permissions
        unauthorise_permission('411', '2486');
        $data['side_effects'] = $this->doctore_patient->get_all_side_effects(); // Fetch side effects
        $this->load->model('doctore_patient/doctore_patient');

        // Validate the ID
        if (isset($id) && !empty($id) && is_numeric($id)) {
            $data['page_title'] = 'Edit Doctore';
            // $data['doctore_patient'] = 

            // Retrieve the brand by ID
            $result = $this->doctore_patient->get_by_id($id);
            $data['booking_data'] = $this->doctore_patient->get_booking_patient_details_edit($result['booking_id']);
            $data['doctor'] = $this->doctor->doctors_list();
            $data['room_no_list'] = $this->doctore_patient->room_no_list();
            // echo "<pre>";print_r($result);die;
            // If no result is found, you might want to handle this case
            if (!$result) {
                // Optionally, set an error message or redirect
                show_error('Doctore not found', 404);
                return;
            }


            // Prepare data for the view
            $data['page_title'] = "Update Doctore";
            $data['form_error'] = '';
            $data['form_data'] = array(
                'data_id' => $result['doc_pat_id'],
                "booking_id" => $result['booking_id'],
                "patient_id" => $result['patient_id'],
                "room_no" => $result['room_id'],
                "referred_by" => $result['referred_by'],
            );

            // echo "<pre>";
            // print_r('$post');
            // print_r($data['form_data']);
            // die;

            // Check if there is form submission
            if ($this->input->post()) {
                // echo "<pre>";
                // print_r('$post');
                // print_r($post);
                // die;
                // Validate the form
                $data['form_data'] = $this->_validate();
                if ($this->form_validation->run() == TRUE) {
                    // Save the updated brand details
                    $this->doctore_patient->save();
                    echo json_encode(['success' => true, 'message' => 'Room no Update successfully.']);; // Return a success response
                    return;
                } else {
                    // Capture validation errors
                    $data['form_error'] = validation_errors();
                }
            }
            // echo "ok";die;
            // Load the view with the prepared data
            $this->load->view('doctore_patient/add', $data);
        } else {
            // Handle the case when the ID is invalid
            show_error('Invalid Brand ID', 400);
        }
    }


    public function save()
    {
        // echo "kodfs";die;
        $post = $this->input->post();

        // Validation rules
        $this->form_validation->set_rules('patient_name', 'Patient Name', 'required');
        // Add other validation rules for your fields as needed
        $this->form_validation->set_rules('s_creatinine', 'Serum Creatinine', 'required|numeric');
        $this->form_validation->set_rules('blood_sugar', 'Blood Sugar', 'required|numeric');
        $this->form_validation->set_rules('blood_pressure', 'Blood Pressure', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            $this->session->set_flashdata('error', validation_errors());
            redirect('doctore_patient/add');
        } else {
            // Save the record
            $this->doctore_patient->save();
            $this->session->set_flashdata('success', 'Record saved successfully');
            redirect('doctore_patient');
        }
    }

    public function update()
    {
        $post = $this->input->post();

        if (empty($post['data_id'])) {
            $this->session->set_flashdata('error', 'No record found to update');
            redirect('doctore_patient');
        }

        $this->doctore_patient->save();
        $this->session->set_flashdata('success', 'Record updated successfully');
        redirect('doctore_patient');
    }


    public function doctore_patient_excel()
    {

        // Starting the PHPExcel library
        $this->load->library('excel');
        $this->excel->IO_factory();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        $objPHPExcel->setActiveSheetIndex(0);

        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');

        // Main header with date range if provided
        $mainHeader = "Doctore Patient List";
        if (!empty($from_date) && !empty($to_date)) {
            $mainHeader .= " (From: " . date('d-m-Y', strtotime($from_date)) . " To: " . date('d-m-Y', strtotime($to_date)) . ")";
        }

        // Set the main header in row 1
        $objPHPExcel->getActiveSheet()->mergeCells('A1:L1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $mainHeader);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // Leave row 2 blank
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

        // Field names (header row) should start in row 3
        $fields = array('Token No', 'OPD No', 'Patient Reg No.', 'Patient Name', 'Gender', 'Mobile No', 'Age', 'Status', 'Patient Status', 'Doctore', 'Room No', 'Created Date');

        $col = 0; // Initialize the column index
        foreach ($fields as $field) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 3, $field); // Row 3 for headers
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true); // Auto-size columns
            $col++;
        }

        // Style for header row (Row 3)
        $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // Fetching the OPD data
        $list = $this->doctore_patient->get_datatables();

        // Populate the data starting from row 4
        $row = 4; // Start at row 4 for data
        if (!empty($list)) {
            foreach ($list as $doctore_patient) {
                // Reset column index for each new row
                $col = 0;
                $age_y = $doctore_patient->age_y;
                $age_m = $doctore_patient->age_m;
                $age_d = $doctore_patient->age_d;

                $age = "";
                if ($age_y > 0) {
                    $year = 'Years';
                    if ($age_y == 1) {
                        $year = 'Year';
                    }
                    $age .= $age_y . " " . $year;
                }
                if ($age_m > 0) {
                    $month = 'Months';
                    if ($age_m == 1) {
                        $month = 'Month';
                    }
                    $age .= ", " . $age_m . " " . $month;
                }
                if ($age_d > 0) {
                    $day = 'Days';
                    if ($age_d == 1) {
                        $day = 'Day';
                    }
                    $age .= ", " . $age_d . " " . $day;
                }
                $gender = array('0' => 'Female', '1' => 'Male', '2' => 'Others');
                $statuses = explode(',', $doctore_patient->pat_status);

                // Trim any whitespace from the statuses and get the last one
                $last_status = trim(end($statuses));
                // Prepare data to be populated
                $data = array(
                    $doctore_patient->token_no,
                    $doctore_patient->booking_code,
                    $doctore_patient->patient_code,
                    $doctore_patient->patient_name,
                    $gender[$doctore_patient->gender],
                    $doctore_patient->mobile_no, // Make sure this is retrieved correctly
                    $age,
                    $doctore_patient->status == 0 ? 'Pending' : 'Completed',
                    $last_status,
                    $doctore_patient->doctor_name,
                    $doctore_patient->room_no,
                    date('d-M-Y', strtotime($doctore_patient->created_date)),
                );

                foreach ($data as $cellValue) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cellValue);
                    $col++;
                }
                $row++; // Move to the next row
            }
        }

        // Send headers to force download of the file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="doctore_patient_list_' . time() . '.xls"');
        header('Cache-Control: max-age=0');

        // Write the Excel file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
    }


    public function doctore_patient_pdf()
    {
        // Increase memory limit and execution time for PDF generation
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 300);

        // Prepare data for the PDF
        // $data['print_status'] = "";
        // $from_date = $this->input->get('start_date');
        // $to_date = $this->input->get('end_date');

        // Fetch OPD data
        $data['data_list'] = $this->doctore_patient->get_datatables();
        // echo "<pre>";
        // print_r($data);
        // die;
        $data['data_list']['side_effect_name'] = $this->doctore_patient->get_side_effect_name($data['data_list']['side_effects']);
        // Create main header
        $data['mainHeader'] = "Doctore Patient List";
        // if (!empty($from_date) && !empty($to_date)) {
        // $data['mainHeader'] .= " (From: " . date('d-m-Y', strtotime($from_date)) . " To: " . date('d-m-Y', strtotime($to_date)) . ")";
        // }

        // Load the view and capture the HTML output
        $this->load->view('doctore_patient/doct_patient_html', $data);
        $html = $this->output->get_output();

        // Load PDF library and convert HTML to PDF
        $this->load->library('pdf');
        $this->pdf->load_html($html);
        $this->pdf->render();

        // Stream the generated PDF to the browser
        $this->pdf->stream("help_desk_list_" . time() . ".pdf", array("Attachment" => 1));
    }

    public function delete($id)
    {
        $this->doctore_patient->delete($id);
        $this->session->set_flashdata('success', 'Record deleted successfully');
        redirect('doctore_patient');
    }

    public function delete_multiple()
    {
        $ids = $this->input->post('row_id');
        // echo "<pre>";
        // print_r($ids);
        // die;
        if (!empty($ids)) {
            $this->doctore_patient->deleteall($ids);
            // echo json_encode(array("status" => TRUE));
            $response = "Vision successfully deleted.";
            echo $response;
        }
    }

    public function print_vision($id)
    {
        $data['print_status'] = "1";

        // Fetch the form data based on the ID
        $data['form_data'] = $this->doctore_patient->print_vision_details($id);
        // echo "<pre>";print_r($data['form_data']);die;

        // Fetch the side effect name based on the side_effect ID from form data
        if (!empty($data['form_data']['side_effects'])) {
            $side_effect_id = $data['form_data']['side_effects'];
            $data['form_data']['side_effect_name'] = $this->doctore_patient->get_side_effect_name($side_effect_id);
        }

        // Fetch the OPD billing details based on the ID
        $booking_id = isset($data['form_data']['booking_id']) ? $data['form_data']['booking_id'] : '';
        $data['billing_data'] = $this->doctore_patient->get_patient_name_by_booking_id($booking_id);

        // Load the print view with the data
        $this->load->view('doctore_patient/print_vision', $data);
    }
    public function reset_search()
    {
        $this->session->unset_userdata('prescription_search');
    }

    public function advance_search()
    {
        $this->load->model('general/general_model');
        $data['page_title'] = "Advance Search";
        $post = $this->input->post();

        $data['form_data'] = array(
            "start_date" => '',
            "end_date" => '',
            "patient_code" => "",
            "patient_name" => "",
        );
        if (isset($post) && !empty($post)) {
            $marge_post = array_merge($data['form_data'], $post);
            $this->session->set_userdata('prescription_search', $marge_post);

        }
        $prescription_search = $this->session->userdata('prescription_search');
        if (isset($prescription_search) && !empty($prescription_search)) {
            $data['form_data'] = $prescription_search;
        }
        $this->load->view('doctore_patient/advance_search', $data);
    }

}