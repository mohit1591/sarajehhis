<?php
$users_data = $this->session->userdata('auth_users');
?>
<!DOCTYPE html>
<html>

<head>
  <title><?php echo $page_title . PAGE_TITLE; ?></title>
  <meta name="viewport" content="width=1024">


  <!-- bootstrap -->
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>dataTables.bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datatable.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>font-awesome.min.css">

  <!-- links -->
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>my_layout.css">
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>menu_style.css">
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>menu_for_all.css">
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>withoutresponsive.css">

  <!-- js -->
  <script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap.min.js"></script>

  <!-- datatable js -->
  <script src="<?php echo ROOT_JS_PATH; ?>jquery.dataTables.min.js"></script>
  <script src="<?php echo ROOT_JS_PATH; ?>dataTables.bootstrap.min.js"></script>

  <style>
    span {
      font-weight: normal;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
  <script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>
  <script type="text/javascript">
    var save_method;
    var table;
    <?php
    if (in_array('2413', $users_data['permission']['action'])) {
      ?>
      $(document).ready(function () {
        table = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "order": [],
          "pageLength": '20',
          "ajax": {
            "url": "<?php echo base_url('help_desk/ajax_list') ?>",
            "type": "POST",
          },
          "columnDefs": [
            {
              "targets": [0, -1], // last column
              "orderable": false, // set not orderable
            },
            {
              // "targets": -1, // Hide the last column (emergency_status)
              // "visible": false,
            },
            {
              "targets": 5, // Ensure the fifth column is visible
              "visible": true,
            }
          ],
          "createdRow": function (row, data, dataIndex) {
            // console.log(data)
            // Access emergency_status (assuming it's the last column in the data array)
            var emergencyStatus = data[data.length - 1]; // Get the emergency_status value
            // console.log(emergencyStatus)

            // Change the background color of the first column based on emergency_status
            var firstColumn = $('td', row).eq(1); // Get the first column cell

            if (emergencyStatus == 1) {
              firstColumn.css({
                'background-color': 'red',   // Red background for emergency_status 1
                // 'color': 'white',            // White font color
                'font-weight': 'bold'        // Bold font
              });
            } else if (emergencyStatus == 2) {
              firstColumn.css({
                'background-color': 'blue',  // Blue background for emergency_status 2
                // 'color': 'white',            // White font color
                'font-weight': 'bold'        // Bold font
              });
            } else if (emergencyStatus == 3) {
              firstColumn.css({
                'background-color': 'yellow', // Yellow background for emergency_status 3
                // 'color': 'black',             // Black font color (or default)
                'font-weight': 'bold'         // Bold font
              });
            } else {
              firstColumn.css({
                // 'background-color': 'white',  // Default white background
                // 'color': 'black',             // Default font color
                'font-weight': 'bold'         // Bold font by default
              });
            }
          },
        });

        // Toggle columns visibility
        $('.tog-col').on('click', function (e) {
          var column = table.column($(this).attr('data-column'));
          column.visible(!column.visible());
        });
      });

    <?php } ?>


    $(document).ready(function () {
      var $modal = $('#load_add_modal_popup');
      $('#doctor_add_modal').on('click', function () {
        $modal.load('<?php echo base_url() . 'help_Desk/add/' ?>',
          {
            //'id1': '1',
            //'id2': '2'
          },
          function () {
            $modal.modal('show');
          });

      });


      $('#adv_search').on('click', function () {
        $modal.load('<?php echo base_url() . 'help_desk/advance_search/' ?>',
          {
          },
          function () {
            $modal.modal('show');
          });

      });

    });

    function edit_prescription(id) {
      var $modal = $('#load_add_modal_popup');
      $modal.load('<?php echo base_url() . 'help_desk/edit/' ?>' + id,
        {
          //'id1': '1',
          //'id2': '2'
        },
        function () {
          $modal.modal('show');
        });
    }

    function view_prescription(id) {
      var $modal = $('#load_add_modal_popup');
      $modal.load('<?php echo base_url() . 'help_desk/view/' ?>' + id,
        {
          //'id1': '1',
          //'id2': '2'
        },
        function () {
          $modal.modal('show');
        });
    }

    function reload_table() {
      table.ajax.reload(null, false); //reload datatable ajax 
    }



    function checkboxValues() {
      $('#table').dataTable();
      var allVals = [];
      $(':checkbox').each(function () {
        if ($(this).prop('checked') == true) {
          allVals.push($(this).val());
        }
      });
      allbranch_delete(allVals);
    }

    function allbranch_delete(allVals) {
      if (allVals != "") {
        $('#confirm').modal({
          backdrop: 'static',
          keyboard: false
        })
          .one('click', '#delete', function (e) {
            $.ajax({
              type: "POST",
              url: "<?php echo base_url('help_desk/deleteall'); ?>",
              data: { row_id: allVals },
              success: function (result) {
                flash_session_msg(result);
                reload_table();
              }
            });
          });
      }
    }

  </script>




</head>

<body>


  <div class="container-fluid">
    <?php
    $this->load->view('include/header');
    $this->load->view('include/inner_header');
    ?>
    <!-- ============================= Main content start here ===================================== -->
    <section class="userlist">
      <div class="userlist-box">
        <form name="search_form" id="search_form">

          <div class="row">
            <div class="col-sm-4">
              <div class="row m-b-5">
                <div class="col-xs-5"><label>From Date</label></div>
                <div class="col-xs-7">
                  <input id="start_date_patient" name="start_date" class="datepicker start_datepicker m_input_default"
                    type="text" value="<?php echo $form_data['start_date'] ?>">
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-5"><label><?php echo $data = get_setting_value('PATIENT_REG_NO'); ?></label></div>
                <div class="col-xs-7">
                  <input name="patient_code" class="m_input_default" id="patient_code" onkeyup="return form_submit();"
                    value="<?php echo $form_data['patient_code'] ?>" type="text" autofocus>
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-5"><label>Mobile No.</label></div>
                <div class="col-xs-7">
                  <input name="mobile_no" value="<?php echo $form_data['mobile_no'] ?>" id="mobile_no"
                    onkeyup="return form_submit();" class="numeric m_input_default" maxlength="10" value="" type="text">
                </div>
              </div>




            </div> <!-- 4 -->

            <div class="col-sm-4">
              <div class="row m-b-5">
                <div class="col-xs-4"><label>To Date</label></div>
                <div class="col-xs-8">
                  <input name="end_date" id="end_date_patient"
                    class="datepicker datepicker_to end_datepicker m_input_default"
                    value="<?php echo $form_data['end_date'] ?>" type="text">
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-4"><label>Patient Name</label></div>
                <div class="col-xs-8">
                  <input name="patient_name" value="<?php echo $form_data['patient_name'] ?>" id="patient_name"
                    onkeyup="return form_submit();" class="alpha_space m_input_default" value="" type="text">
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-4"><label> Booking Type</label></div>
                <div class="col-xs-8">
                  <input name="emergency_booking" id="emergency_booking" onclick="return form_submit();" value="3"
                    type="radio" <?php if ($form_data['emergency_booking'] == '3') {
                      echo 'checked';
                    } ?>> Normal
                  <input name="emergency_booking" id="emergency_booking" onclick="return form_submit();" value="4"
                    type="radio" <?php if ($form_data['emergency_booking'] == '4') {
                      echo 'checked';
                    } ?>> FastTrack
                  <input name="emergency_booking" id="emergency_booking" onclick="return form_submit();" value=""
                    type="radio" <?php echo 'checked'; ?>> All
                </div>
              </div>

              <?php
              $users_data = $this->session->userdata('auth_users');

              if (array_key_exists("permission", $users_data)) {
                $permission_section = $users_data['permission']['section'];
                $permission_action = $users_data['permission']['action'];
              } else {
                $permission_section = array();
                $permission_action = array();
              }
              //print_r($permission_action);
              
              $new_branch_data = array();
              $users_data = $this->session->userdata('auth_users');
              $sub_branch_details = $this->session->userdata('sub_branches_data');
              $parent_branch_details = $this->session->userdata('parent_branches_data');


              if (!empty($users_data['parent_id'])) {
                $new_branch_data['id'] = $users_data['parent_id'];

                $users_new_data[] = $new_branch_data;
                $merg_branch = array_merge($users_new_data, $sub_branch_details);

                $ids = array_column($merg_branch, 'id');
                $branch_id = implode(',', $ids);
                $option = '<option value="' . $branch_id . '">All</option>';
              }

              ?>
              <?php if (in_array('1', $permission_section)) { ?>
                <div class="row m-b-5">
                  <div class="col-xs-5"><label>Branch</label></div>
                  <div class="col-xs-7">



                    <select name="branch_id" id="branch_id" onchange="return form_submit();">
                      <?php echo $option; ?>
                      <option selected="selected" <?php if (isset($_POST['branch_id']) && $_POST['branch_id'] == $users_data['parent_id']) {
                        echo 'selected="selected"';
                      } ?>
                        value="<?php echo $users_data['parent_id']; ?>">Self</option>';
                      <?php
                      if (!empty($sub_branch_details)) {
                        $i = 0;
                        foreach ($sub_branch_details as $key => $value) {
                          ?>
                          <option value="<?php echo $sub_branch_details[$i]['id']; ?>" <?php if (isset($_POST['branch_id']) && $_POST['branch_id'] == $sub_branch_details[$i]['id']) {
                               echo 'selected="selected"';
                             } ?>>
                            <?php echo $sub_branch_details[$i]['branch_name']; ?>
                          </option>
                          <?php
                          $i = $i + 1;
                        }

                      }
                      ?>
                    </select>


                  </div>
                </div>

              <?php } else { ?>
                <input type="hidden" name="branch_id" id="branch_id" value="<?php echo $users_data['parent_id']; ?>">
              <?php } ?>


              <script>
                $(document).ready(function () {
                  // Function to show/hide additional selection based on radio button selection
                  $('input[name="search_type"]').change(function () {
                    if ($(this).val() == "0") { // If Pending is selected
                      $('#additional_selection').show();
                    } else {
                      $('#additional_selection').hide();
                    }
                  });
                });
              </script>

            </div> <!-- 4 -->

            <div class="col-sm-4 d-flex justify-content-center" style="margin-left: 177px;margin-top: 35px;">

              <!--<a class="btn-custom" id="reset_date" onclick="reset_search();"><i class="fa fa-refresh"></i> Reset</a>
          <br>
            <a href="javascript:void(0)" class="btn-a-search" id="patient_adv_search">
              <i class="fa fa-cubes" aria-hidden="true"></i> 
              Search
            </a>-->
              <a class="btn-custom" id="reset_date" onclick="reset_search();"> Reset</a>
              <!--<a href="javascript:void(0)" class="btn-custom" id="patient_adv_search">
              <i class="fa fa-cubes" aria-hidden="true"></i> 
              Advance Search
            </a>-->
            </div> <!-- 4 -->


          </div> <!-- row -->

          <div class="row">
            <div class="col-sm-12">
              <div id="additional_selection">
                <div class="col-xs-2"><label style="margin-left: -15px;">Type</label></div>
                <div class="col-xs-10" style="margin-left: -43px;">
                  <label class="radio-label">
                    <input type="radio" name="priority_type" value="1" id="priority_red"
                      onclick="return form_submit();">
                    <span>Priority</span>
                  </label>

                  <label class="radio-label">
                    <input type="radio" name="priority_type" value="2" id="fasttrack_blue"
                      onclick="return form_submit();">
                    <span>Fast Track OPD Consultation</span>
                  </label>

                  <label class="radio-label">
                    <input type="radio" name="priority_type" value="3" id="priority_yellow"
                      onclick="return form_submit();">
                    <span>Post-Operative</span>
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="priority_type" value="4" id="priority_normal"
                      onclick="return form_submit();">
                    <span>Normal</span>
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="priority_type" value="" id="priority_all" onclick="return form_submit();"
                      checked>
                    <span>All</span>
                  </label>
                </div>
              </div>
            </div>
          </div>


        </form>
        <form>
          <?php if (in_array('2413', $users_data['permission']['action'])) {
            ?>
            <!-- bootstrap data table -->
            <table id="table" class="table table-striped table-bordered prescription_list_tbl" cellspacing="0"
              width="100%">
              <thead>
                <tr>
                  <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value="">
                  </th>
                  <th> Token No. </th>
                  <th> OPD No. </th>
                  <th> <?php echo $data = get_setting_value('PATIENT_REG_NO'); ?> </th>
                  <th> Patient Name </th>
                  <th> Gender </th>
                  <th> Mobile </th>
                  <th> Age </th>
                  <th> Patient Status </th>
                  <th> Created Date </th>
                  <th> Action </th>
                </tr>
              </thead>
            </table>
          <?php } ?>


        </form>


      </div> <!-- close -->





      <div class="userlist-right relative">
        <div class="fixed">
          <div class="btns">
            <?php if (in_array('2413', $users_data['permission']['action'])) {
              ?>
              <!-- <button class="btn-update" onclick="window.location.href='<?php echo base_url('opd'); ?>'">
               <i class="fa fa-plus"></i> New
             </button> -->
            <?php } ?>
            <a data-toggle="tooltip" title="Download list in excel" href="#" id="help_desk_download_excel"
              class="btn-anchor m-b-2">
              <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <a data-toggle="tooltip" title="Download list in pdf" href="#" id="help_desk_download_pdf"
              class="btn-anchor m-b-2">
              <i class="fa fa-file-pdf-o"></i> PDF
            </a>
            <?php if (in_array('2413', $users_data['permission']['action'])) {
              ?>
              <button class="btn-update" id="deleteAll" onclick="return checkboxValues();">
                <i class="fa fa-trash"></i> Delete
              </button>
            <?php } ?>

            <button class="btn-update" onclick="reload_table()">
              <i class="fa fa-refresh"></i> Reload
            </button>


            <button class="btn-exit" onclick="window.location.href='<?php echo base_url(); ?>'">
              <i class="fa fa-sign-out"></i> Exit
            </button>
          </div>
        </div>
      </div>
      <!-- right -->

      <!-- cbranch-rslt close -->





    </section> <!-- cbranch -->
    <?php
    $this->load->view('include/footer');
    ?>

    <script>
      $(document).ready(function () {
        form_submit();
        $('#load_add_medicine_unit_modal_popup').on('shown.bs.modal', function (e) {
          $(this).find('.inputFocus').focus();
        })
      });
      function reset_search() {
        $('#start_date_patient').val('');
        $('#end_date_patient').val('');
        $('#patient_code').val('');
        $('#patient_name').val('');
        $('#mobile_no').val('');

        $.ajax({
          url: "<?php echo base_url(); ?>help_desk/reset_search/",
          success: function (result) {
            // $('#additional_selection').hide();
            reload_table();
          }
        });
      }

      $(document).on('click', '.open-popup', function () {
        // Get the data attributes from the clicked button
        var bookingId = $(this).data('booking-id');
        var patientId = $(this).data('patient-id');
        var referredBy = $(this).data('referred-by');

        // Build the dynamic URL with route parameters
        var routeUrl = '<?php echo base_url(); ?>doctore_patient/add/' + bookingId + '/' + patientId + '/' + referredBy;

        // Select the modal
        var $modal = $('#load_add_medicine_unit_modal_popup');

        // Load the modal content
        $modal.load(routeUrl, function () {
          // Show the modal once content is loaded
          $modal.modal('show');
        });
      });

      $(document).on('click', '.book-now-btn-url', function () {
        const btn = $(this); 
        const patientId = btn.data('id'); 
        const bookingUrl = btn.data('url');

        btn.prop('disabled', true).text('In Progress');

        $.ajax({
          url: '<?= base_url("refraction/check_booking_status"); ?>', // Backend URL to check status
          type: 'POST',
          data: { patient_id: patientId },
          success: function (response) {
            const data = JSON.parse(response);

            if (data.status === '1') {
              alert('Booking is already in progress for this patient.');
              btn.prop('disabled', false).text('Book Now');
              return;
            } else if (data.status === '0') {
              $.ajax({
                url: '<?= base_url("refraction/book_patient"); ?>', 
                type: 'POST',
                data: { patient_id: patientId },
                success: function (bookingResponse) {
                  const bookingData = JSON.parse(bookingResponse);
                  if (bookingData.status === 'success') {
                    window.location.href = bookingUrl; // Redirect to booking URL
                  } else {
                    alert(bookingData.message || 'An error occurred.');
                    btn.prop('disabled', false).text('Book Now');
                  }
                },
                error: function () {
                  alert('An error occurred while booking. Please try again.');
                  btn.prop('disabled', false).text('Book Now');
                }
              });
            }
          },
          error: function () {
            // Handle AJAX error
            alert('An error occurred while checking status. Please try again.');
            btn.prop('disabled', false).text('Book Now');
          }
        });
      });


      $(document).on('click', '.refresh-btn', function () {
        const patientId = $(this).data('patient_id');
        const refreshButton = $(this);

        if (!patientId) {
          alert('Patient ID is missing!');
          return;
        }

        refreshButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
          url: 'refraction/update_status_opd', 
          type: 'POST',
          data: { patient_id: patientId },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              reload_table();
            } else {
              alert(response.message || 'Failed to update status.');
            }
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('An error occurred while updating the status.');
          },
          
        });
      });



      $('.start_datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        endDate: new Date(),
      }).on("change", function (selectedDate) {
        var start_data = $('.start_datepicker').val();
        $('.end_datepicker').datepicker('setStartDate', start_data);
        form_submit();
      });

      $('.end_datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
      }).on("change", function (selectedDate) {
        form_submit();
      });

      function form_submit() {
        $('#search_form').delay(200).submit();
      }
      $("#search_form").on("submit", function (event) {
        event.preventDefault();

        $.ajax({
          url: "<?php echo base_url('help_desk/advance_search/'); ?>",
          type: "post",
          data: $(this).serialize(),
          success: function (result) {
            reload_table();
          }
        });

      });
      <?php
      $flash_success = $this->session->flashdata('success');
      if (isset($flash_success) && !empty($flash_success)) {
        echo 'flash_session_msg("' . $flash_success . '");';
      }
      ?>

$(document).on('click', '.book-now-btn-url-vision', function () {
        const btn = $(this); 
        const patientId = btn.data('id'); 
        const bookingUrl = btn.data('url');

        btn.prop('disabled', true).text('In Progress');

        $.ajax({
          url: '<?= base_url("vision/check_booking_status"); ?>', // Backend URL to check status
          type: 'POST',
          data: { patient_id: patientId },
          success: function (response) {
            const data = JSON.parse(response);

            if (data.status === '1') {
              alert('Booking is already in progress for this patient.');
              btn.prop('disabled', false).text('Book Now');
              return;
            } else if (data.status === '0') {
              $.ajax({
                url: '<?= base_url("vision/book_patient"); ?>', 
                type: 'POST',
                data: { patient_id: patientId },
                success: function (bookingResponse) {
                  const bookingData = JSON.parse(bookingResponse);
                  if (bookingData.status === 'success') {
                    window.location.href = bookingUrl; // Redirect to booking URL
                  } else {
                    alert(bookingData.message || 'An error occurred.');
                    btn.prop('disabled', false).text('Book Now');
                  }
                },
                error: function () {
                  alert('An error occurred while booking. Please try again.');
                  btn.prop('disabled', false).text('Book Now');
                }
              });
            }
          },
          error: function () {
            // Handle AJAX error
            alert('An error occurred while checking status. Please try again.');
            btn.prop('disabled', false).text('Book Now');
          }
        });
      });


      $(document).on('click', '.refresh-btn-vision', function () {
        const patientId = $(this).data('patient_id');
        const refreshButton = $(this);

        if (!patientId) {
          alert('Patient ID is missing!');
          return;
        }

        refreshButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
          url: 'vision/update_status_opd', 
          type: 'POST',
          data: { patient_id: patientId },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              reload_table();
            } else {
              alert(response.message || 'Failed to update status.');
            }
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('An error occurred while updating the status.');
          },
          
        });
      });





      function delete_eye_prescription(prescription_id) {
        $('#confirm').modal({
          backdrop: 'static',
          keyboard: false
        })
          .one('click', '#delete', function (e) {
            $.ajax({
              url: "<?php echo base_url('eye/add_eye_prescription/delete_eye_prescription/'); ?>" + prescription_id,
              success: function (result) {
                flash_session_msg(result);
                reload_table();
              }
            });
          });
      }
      $('document').ready(function () {
        <?php if (isset($_GET['status']) && $_GET['status'] == 'print' && !isset($_GET['type'])) { ?>
          $('#confirm_print').modal({
            backdrop: 'static',
            keyboard: false
          })

            .one('click', '#cancel', function (e) {
              window.location.href = '<?php echo base_url('help_desk'); ?>';
            });

        <?php } ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'print_eye' && !isset($_GET['type'])) { ?>
          $('#confirm_print_eye').modal({
            backdrop: 'static',
            keyboard: false
          })

            .one('click', '#cancel', function (e) {
              window.location.href = '<?php echo base_url('help_desk'); ?>';
            });

        <?php } ?>
      });


      $('#patient_adv_search').on('click', function () {
        var $modal = $('#load_add_modal_popup');
        $modal.load('<?php echo base_url() . 'opd/patient_adv_search/' ?>',
          {
          },
          function () {
            $modal.modal('show');
          });

      });


      document.getElementById('help_desk_download_excel').addEventListener('click', function (e) {
        e.preventDefault();


        var fromDate = document.getElementById('start_date_patient').value;
        var toDate = document.getElementById('end_date_patient').value;


        var url = '<?php echo base_url("help_desk/help_desk_excel"); ?>';


        if (fromDate || toDate) {
          url += '?';
          if (fromDate) {
            url += 'start_date=' + encodeURIComponent(fromDate);
          }
          if (toDate) {
            url += (fromDate ? '&' : '') + 'end_date=' + encodeURIComponent(toDate);
          }
        }
        window.location.href = url;
      });

      document.getElementById('help_desk_download_pdf').addEventListener('click', function (e) {
        e.preventDefault();

        var fromDate = document.getElementById('start_date_patient').value;
        var toDate = document.getElementById('end_date_patient').value;


        var fromDateObj = new Date(fromDate);
        var toDateObj = new Date(toDate);



        var url = '<?php echo base_url("help_desk/help_desk_pdf"); ?>';
        url += '?start_date=' + encodeURIComponent(fromDate) + '&end_date=' + encodeURIComponent(toDate);

        window.location.href = url;
      });
      $(document).ready(function () {
        var $modal = $('#load_add_medicine_unit_modal_popup');
        $('#modal_add').on('click', function () {
          $modal.load('<?php echo base_url() . 'room_master/add/' ?>',
            {
              //'id1': '1',
              //'id2': '2'
            },
            function () {
              $modal.modal('show');
            });

        });

      });

    </script>
    <!-- Confirmation Box -->

    <div id="confirm_print" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-theme">
            <h4>Are You Sure?</h4>
          </div>
          <!-- <div class="modal-body"></div> -->
          <div class="modal-footer">
            <a data-dismiss="modal" class="btn-anchor"
              onClick="return print_window_page('<?php echo base_url("help_desk/print_prescriptions"); ?>');">Print</a>


            <button type="button" data-dismiss="modal" class="btn-cancel" id="cancel">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div id="confirm" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-theme">
            <h4>Are You Sure?</h4>
          </div>
          <div class="modal-body" style="font-size:8px;">*Data that have been in Archive more than 60 days will be
            automatically deleted.</div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn-update" id="delete">Confirm</button>
            <button type="button" data-dismiss="modal" class="btn-cancel">Close</button>
          </div>
        </div>
      </div>
    </div> <!-- modal -->
    <div id="confirm_print_eye" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-theme">
            <h4>Are You Sure?</h4>
          </div>
          <!-- <div class="modal-body"></div> -->
          <div class="modal-footer">
            <a data-dismiss="modal" class="btn-anchor"
              onClick="return print_window_page('<?php echo base_url("eye/add_prescription/print_prescriptions"); ?>');">Print</a>


            <button type="button" data-dismiss="modal" class="btn-cancel" id="cancel">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Box end -->
    <div id="load_add_modal_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>
    <div id="load_add_medicine_unit_modal_popup" class="modal fade" role="dialog" data-backdrop="static"
      data-keyboard="false"></div>
  </div><!-- container-fluid -->
</body>

</html>