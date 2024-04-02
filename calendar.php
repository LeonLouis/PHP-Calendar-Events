<?php

class Calendar {

  // Connect to Database
  function connect(){
    return mysqli_connect('localhost', 'root', '', 'php_calendar_db');
  }

  function get_calendar($month,$year){
    // Change Year or Month of Calendar
    if(isset($_POST['update_calendar'])){
      header('location:index.php?m='.$_POST['month'].'&y='.$_POST['year']);
    }
    // Add Events
    if(isset($_POST['add_event'])){
      $event_title = $_POST['event_title'];
      $description = $_POST['description'];
      $date_time = $_POST['date_time'];
      $event_owner = $_POST['event_owner'];
      $sql = "INSERT INTO events (event_title, description, date_time, event_owner) VALUES ('$event_title', '$description', '$date_time', '$event_owner')";
      mysqli_query($this->connect(), $sql);
      $params = $_GET;
      if(isset($params['id'])){
        unset($params['id']);
      }
      header('location:'.$_SERVER['PHP_SELF'].'?'.http_build_query($params));
    }

    // Get Data by Month and Year
    $get_all_events = mysqli_query($this->connect(), 'SELECT *,DATE(date_time) as only_date FROM `events` WHERE MONTH(DATE(date_time)) = '.$month.' AND YEAR(DATE(date_time)) = '.$year.'');
    while( $result = mysqli_fetch_assoc($get_all_events)){
      $results[] = $result; 
    };
    // Get Day of Month today ( 1-31 ) 
    $today = date('j');
    // Today Month Number ( 01-12 )
    $t_month = date('m');
    // Today Year ( YYYY )
    $t_year = date('Y');
    // Convert Todays Date
    $current_date_int = strtotime($t_year.'-'.$t_month.'-'.$today);
    // Array of Days
    $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
    // Array of Months
    $months = array( 
      '01' => 'January', 
      '02' => 'February', 
      '03' => 'March', 
      '04' => 'April', 
      '05' => 'May', 
      '06' => 'June', 
      '07' => 'July', 
      '08' => 'August', 
      '09' => 'Septempter', 
      '10' => 'October', 
      '11' => 'November', 
      '12' => 'December'
    );
      // Day Number ( 0 Sunday - 6 Saturday )
      $running_day = date('w',mktime(0,0,0,$month,1,$year));
      // Todays Number of Days Month
      $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
      $days_in_this_week = 1;
      $day_counter = 0;
      $current_data = $year.'-'.$month;
  
      if($month - 1 < 1){
        $prev_month = '12';
        $prev_year = $year - 1;
      } else {
        $prev_month = ( $month - 1 ) <= 9 ? '0' . ( $month - 1 ) : $month - 1;
        $prev_year = $year;
      }

      if($month + 1 > 12){
        $next_month = '01';
        $next_year = $year + 1;
      } else {
        $next_month = ( $month + 1 ) <= 9 ? '0' . ( $month + 1 ) : $month + 1;
        $next_year = $year;
      }
    ?>
    <div class="calendar-wrapper container col-md-12">
      <div class="row align-items-center card shadow">
        <div class="card-body py-5">
          <h2 class="text-center mb-4 text-uppercase">
            <a href="<?php echo ($year == '1902' && $month == '01') ? '#' : $_SERVER['PHP_SELF'].'?m='.$prev_month.'&y='.$prev_year; ?>" class="icon <?php echo ($year == '1902' && $month == '01') ? 'disabled' : ''; ?>"><i class="fa-solid fa-angle-left"></i></a>
            <?php echo date('F', strtotime($current_data)); ?>
            <a href="<?php echo ($year == '2075' && $month == '12') ? '#' : $_SERVER['PHP_SELF'].'?m='.$next_month.'&y='.$next_year; ?>" class="icon <?php echo ($year == '2075' && $month == '12') ? 'disabled' : ''; ?>"><i class="fa-solid fa-angle-right"></i></a>
          </h2>
          <div class="calendar-btns mb-4">
            <form method="post">
              <select name="month" id="month">
                <?php foreach($months as $month_key => $month_val):
                  if($month_key == $month):
                ?>
                  <option value="<?php echo $month_key; ?>" selected><?php echo $month_val; ?></option>
                <?php else: ?>
                  <option value="<?php echo $month_key; ?>"><?php echo $month_val; ?></option>
                <?php endif; ?>
                <?php endforeach; ?>
              </select>
              <select name="year" id="year">
                <?php for($y = 1902; $y <= 2075; $y++): 
                  if($y == $year):
                ?>
                  <option value="<?php echo $y; ?>" selected><?php echo $y; ?></option>
                <?php else: ?>
                  <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endif; ?>
                <?php endfor; ?>
              </select>
              <button type="submit" name="update_calendar" class="btn btn-warning text-white"><span><i class="fa fa-clock"></i></span> Show</button>
            </form>
            <button type="button" class="btn btn-addevent" data-toggle="modal" data-target="#addEvent">Add an Event</button>
          </div>
          <table class="table-responsive main-calendar w-100">
            <tr>
              <?php foreach( $days as $day ): ?>
                <th class="calendar-head text-center py-2"><div class="font-weight-normal"><?php echo $day; ?></div></th>
              <?php endforeach; ?>
            </tr>
              <tr>
                <?php for($x = 0; $x < $running_day; $x++): ?>
                  <td class="day calendar-day-prev"> </td>
                  <?php $days_in_this_week++; ?>
                <?php endfor; ?>
                  <?php for($list_day = 1; $list_day <= $days_in_month; $list_day++): ?>
                    <td class="day calendar-day">
                      <?php 
                        $m_day = $list_day < 10 ? '0'.$list_day : $list_day; 
                        $data_date = $year.'-'.$month.'-'.$m_day;
                      ?>
                      <div class="day-number" data-date="<?php echo $data_date; ?>">
                        <span class="day-span"><?php echo $list_day; ?></span>
                          <?php
                            if(!empty($results)):
                              foreach($results as $result_key => $result_val){
                                if ( $result_val['only_date'] == $data_date ) { 
                                  $params = $_GET;
                                  $params['id'] = $result_val['id'];
                                ?>
                                  <a href="<?php echo $_SERVER['PHP_SELF'].'?'.http_build_query($params); ?>" data-id="<?php echo $result_val['id']; ?>">
                                    <span class="icon"><i class="fa fa-calendar"></i></span>
                                  </a>
                                <?php
                                }
                              }
                            endif;
                          ?>
                      </div>
                    </td>
                    <?php if($running_day == 6): ?>
                      </tr>
                      <?php if(($day_counter+1) != $days_in_month): ?>
                          <tr class="calendar-row">
                      <?php endif; ?>
                      <?php 
                        $running_day = -1;
                        $days_in_this_week = 0;
                      ?>
                    <?php endif; ?>
                    <?php $days_in_this_week++; $running_day++; $day_counter++; ?>
                  <?php endfor; ?>
              <?php if($days_in_this_week < 8): ?>
                <?php for($x = 1; $x <= (8 - $days_in_this_week); $x++): ?>
                  <td class="calendar-day-prev"> </td>
                <?php endfor; ?>
              <?php endif; ?>
              </tr>
          </table>
        </div>
      </div>
    </div>
    <?php if(isset($_GET['id'])){  ?>
      <div class="modal fade" id="showEvent" tabindex="-1" role="dialog" aria-labelledby="showEventTitle" aria-hidden="true">
    <?php 
      foreach($results as $result_key => $result_val){
        if ( $result_val['id'] == $_GET['id'] ) {
    ?>
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="showEvent">Event Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="events">
                <div class="form-group">
                  <label>Event Title</label>
                  <label class="form-control mb-2" ><?php echo $result_val['event_title']?></label>
                </div>
                <div class="form-group">
                  <label>Description</label>
                  <label class="form-control mb-2" ><?php echo $result_val['description']?></label>
                </div>
                <div class="form-group">
                  <label>Date and Time</label>
                  <label class="form-control mb-2" ><?php echo $result_val['date_time']?></label>
                </div>
                <div class="form-group">
                  <label>Event Owner</label>
                  <label class="form-control mb-2" ><?php echo $result_val['event_owner']?></label>
                </div>
              </div>
            </div>
          </div>
        </div>
    <?php
        }
      }
    }
    ?>
    </div>
    <?php
  }  
}

  
