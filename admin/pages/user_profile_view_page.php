<?php
$user_image_name = '';
$error_msg = '';
$user_name ='';
$user_email = '';
$user_contact_number = '';
$user_date_of_birth = '';
$user_gender = '';
$user_address = '';
    if (isset($_GET['view'])) {
      $user_email = $_GET['view'];
      $table_name = 'users_information';
      $info_user = new DatabaseHelper();
      $result = $info_user->retrieveData($table_name,$user_email);
      if ($result!=0) {
        $user_image_name = $result['image_name'];
        $user_name = $result['name'];
        $user_email = $result['email'];
        $user_contact_number = $result['contact_number'];
        $user_date_of_birth = $result['date_of_birth'];
        $user_gender = $result['gender'];
        $user_address = $result['address'];
      }
    }else{
      header("Location: ./member_list.php");
    }
///////////////////////////////////
$plan = "";
$plan_price = '';
$error_msg = '';
$total_payment = '';
$status = '';
$array = array('starter'=>9, 'basic'=>27, 'pro'=>74, 'unlimited'=>140 );
if (!empty($user_email)) {
    $plan_status = new DatabaseHelper();
    $table_name = "users_plan";
    $result=$plan_status->retrieveData($table_name,$user_email);
    if ($result==0) {
      $error_msg =  'Plan not Found!!';
    }else{
      $plan = $result['plan'];
      $plan_price = $array[$plan];
    }

    if (isset($_POST['payment'])) {
      $today = new DateTime('today');
      $today = $today->format('Y-m-d');

      $from_date = $_POST['from_date'];
      $to_date = $_POST['to_date'];

      $amount = $_POST['amount'];
      if (!empty($from_date) && !empty($to_date) && !empty($today) && !empty($plan) && !empty($amount)) {
        $months = getMonth($from_date,$to_date);
        $total_amount  = $months*$plan_price;
        if($total_amount==$amount){
          //echo ($total_amount/$months);
          $temp = new DateTime($from_date);
          $query = new DatabaseHelper();
          for($i = 0;$i < $months;$i++){
            $temp = $temp->format('Y-m-d');
            $query = new DatabaseHelper();
            $sql = "INSERT INTO `payment`(`email`, `payment_date`, `payment_month`, `plan`, `amount`) VALUES ('$user_email','$today','$temp','$plan','$plan_price')";
            $result = $query->insertQuery($sql);
          if ($result) {
            //header("refresh: 0");
          }
          $temp = new DateTime($temp);
          $temp->add(new DateInterval("P1M"));
          }
          header("refresh: 0");
        }else{
          echo 'error';
        }
      }else{
        $error_msg = "Field can't be empty";
      }
    }
    $query = new DatabaseHelper();
    $sql = "SELECT SUM(amount) AS total_pay FROM payment WHERE email = '$user_email'";
    $total_pay = $query->runQuery($sql);
    $row_pay = $total_pay->fetch_assoc();
    $total_payment = $row_pay['total_pay'];

      $query = new DatabaseHelper();
      $sql = "SELECT * FROM `payment` WHERE email = '$user_email' ORDER BY id DESC";
      $result = $query->runQuery($sql);
      
      while($row=$result->fetch_array()){
        $pay_month = new DateTime($row['payment_month']);
        if (checkDateValidation($pay_month)) {
          $status = 'ACTIVE';
          break;
        }else{
          $status = '';
        }
      }

}
function getMonth($from,$to){
  $from_date = new DateTime($from);
  $to_date = new DateTime($to);
  $f_y = $from_date->format('Y');
  $f_m = $from_date->format('m');
  $t_y = $to_date->format('Y');
  $t_m = $to_date->format('m');
  $months = ((($t_y-$f_y)*12)+($t_m-$f_m))+1;
  return $months;
}
function checkDateValidation($pay_month){
  $to_day = new DateTime('today');
    if($to_day->format('m') == $pay_month->format('m')) {
        return TRUE;
    } else {
        return FALSE; 
    } 
}
?>
<h2 class="content-subhead" style="text-align: center;">User Profile</h2>
<hr style="border-style: inset;border-width: 1px;">
<div >
<form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">

	<div  style="text-align: center;">

    <img src="../images/<?php if (!empty($user_image_name)){echo $user_image_name;}else{echo 'default.png';}?>" style="width: 200px;height: auto;">
  <div class="row">
  <div class="col-lg-6">
    <h5>Current Status: <?php if(!empty($status)){echo "<pa style='color:green;'>".$status."</pa>";}else{echo "<pa style='color:red;'>DEACTIVE</pa><br>*Pay for active your account*";}?></h5>
  </div>
  <div class="col-lg-6">
    <h5>Total Pay: <?php if(!empty($total_payment)){echo $total_payment." $";}else{echo "0 $";}?></h5>
  </div>
  </div>
    <hr>
  <?php if(!empty($error_msg)){echo "<p style='color:red;'>$error_msg</p>";}?>
	<div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
    <input type="text" class="form-control input-lg" name="name" placeholder="Name" value="<?php if(!empty($user_name))echo $user_name;?>" readonly>
  	</div><br>

	<div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
    <input type="email" class="form-control input-lg" name="email" placeholder="Email" value="<?php if(!empty($user_email))echo $user_email;?>" readonly>
  	</div><br>

  <div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-phone-alt"></i></span>
    <input type="number" class="form-control input-lg" name="contact_number" placeholder="Contact Number" value="<?php if(!empty($user_contact_number)){echo $user_contact_number;}?>" readonly>
    </div><br>

  <div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
    <input type="date" class="form-control input-lg" name="date_of_birth" placeholder="Date of Birth" value="<?php if(!empty($user_date_of_birth)){echo $user_date_of_birth;}?>" readonly>
    </div><br>

    <div style="text-align: left;">
      <div class="row">
        <div class="col-lg-12" style="font-weight:bold;" >
          Select you Gender:
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="radio-inline">
            <label><input type="radio" name="gender" <?php if($user_gender=='Male'){echo 'checked';}?> value="Male" disabled>Male</label>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="radio-inline">
            <label><input type="radio" name="gender" <?php if($user_gender=='Female'){echo 'checked';}?> value="Female" disabled>Female</label>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="radio-inline">
            <label><input type="radio" name="gender" <?php if($user_gender=='Other'){echo 'checked';}?> value="Other" disabled>Other</label>
          </div>
        </div>
      </div>
      </div><br>

  <div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
    <input type="address" class="form-control input-lg" name="address" placeholder="Current Address" value="<?php if(!empty($user_address)){echo $user_address;}?>" readonly>
    </div><br>

</form>
</div>
<h2 class="content-subhead" style="text-align: center;">Payment</h2>
<hr style="border-style: inset;border-width: 1px;">
<div>
<div>
<form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
<?php if(!empty($error_msg)){echo "<p style = 'color:red;'>$error_msg</p>";}?>
  <div class="row">
    <div class="col-sm-3">
      From:<input id="from_date" type="date" class="form-control input-sm" name="from_date" onclick="undo()">
      
    </div>
    <div class="col-sm-3">
      To:<input id="to_date" type="date" class="form-control input-sm" name="to_date" onclick="undo()">
    </div>
    <div class="col-sm-3">
      Plan:<br><input id="plan" type="radio" name="plan" onclick="calculateTotal()" value="<?php echo $plan;?>" > <?php echo strtoupper($plan);?>
    </div>
    <div class="col-sm-3">
      Amount:
      <div class="input-group">
          <span class="input-group-addon">$ </span>
          <input id="total_amount" type="text" class="form-control" name="amount" placeholder="Amount" readonly>
        </div>
    </div>
  </div>
  <p id="error" style="color: red;"><?php ?></p>
  <div>
    <button type="submit" class="btn btn-primary lg" name="payment">Payment</button>
  </div>
</form>
</div>
<br>
<!--payment List-->
<div>
  <?php
    $query = new DatabaseHelper();
    $sql = "SELECT * FROM `payment` WHERE email = '$user_email' ORDER BY id DESC";
    $result = $query->runQuery($sql);
    $i = 1;
    while($row=$result->fetch_array()){
  ?>
  <div class="row">
    <div class="col-sm-3">
      <div class="row">
        <div class="col-sm-3">
          <?php echo $i.'. '?>
        </div>
        <div class="col-sm-9">
          <?php echo $row['id']?>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
      <span class="glyphicon glyphicon-calendar" style="padding: 5px"></span>
      <?php echo $row['payment_date'];?>
    </div>
    <div class="col-sm-3">
      <span class="glyphicon glyphicon-calendar" style="padding: 5px"></span>
      <?php 
      $payment_month = new DateTime($row['payment_month']);
      echo $payment_month->format("F-Y");
      ?>
    </div>
    <div class="col-sm-3">
      $ <?php echo $row['amount'];?>
    </div>
  </div>
  <?php
    $i++;
    }
  ?>
</div>
</div>
















<!-- Script-->
<script>
    function undo(){
      document.getElementById('plan').checked = false;
    }
    function calculateTotal(){
      var price = {starter:9, basic:27, pro:74, unlimited:140};
      var plan = document.querySelector('input[name="plan"]:checked').value;

      var from_date = new Date(document.getElementById("from_date").value);
      var to_date = new Date(document.getElementById("to_date").value);
      var total_months = (((to_date.getFullYear() - from_date.getFullYear())*12) + (to_date.getMonth() - from_date.getMonth()))+1;
      if (total_months>0) {
        var x  = price[plan];
        var y = total_months;
        var sum = parseFloat(x*y);
        document.getElementById("total_amount").value = sum;
        document.getElementById("error").innerHTML = "";
      }else{
        document.getElementById("error").innerHTML = "Date Selection Error";
        document.getElementById('plan').checked = false;
        document.getElementById("total_amount").value = "";
      }
      
    }
  </script>