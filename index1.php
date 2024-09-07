<!DOCTYPE html>
<html>

<head>
  <title>Fishpond Azizi</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;

    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      border-radius: 5px;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    canvas {
      max-width: 100%;
      margin-bottom: 20px;
    }

    .footer {
      text-align: center;
      margin-top: 20px;
      color: #777;
      font-size: 12px;
    }

    .wrapper {
      width: 100%;
      padding-top: 50px;
    }

    .col_3 {
      width: 33.3333333%;
      float: left;
      min-height: 1px;
    }

    #submit_button {
      background-color: #2bbaff;
      color: #FFF;
      font-weight: bold;
      font-size: 40;
      border-radius: 15px;
      text-align: center;
    }

    .led_img {
      height: 400px;
      width: 200%;
      ;
      object-fit: cover;
      object-position: center;

    }

    @media only screen and (max-width: 600px) {
      .col_3 {
        width: 100%;
      }

      .wrapper {
        width: 100%;
        padding-top: 5px;
      }

      .led_img {
        height: 100px;
        width: 290px;
        margin-right: 10%;
        margin-left: 10%;
        object-fit: cover;
        object-position: center;
      }
    }
  </style>
</head>

<body>
  <div class="container" id="cart-container">
    <h1>Temperature Of Fishpond</h1>
    <canvas id="myChart"></canvas>

    <div class="footer">
      <p>Data recived from MySQL database</p>
    </div>
  </div>



  <?php
  // Database connection
  $host = "fdb1029.awardspace.net";
  $username = "4272950_temp";
  $password = "Mm@9901969183";
  $dbname = "4272950_temp";
  $conn = mysqli_connect($host, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
  // Query to retrieve data
  $sql = "SELECT id ,time, Temperature FROM Temperature ORDER BY id DESC LIMIT 50";
  $result = mysqli_query($conn, $sql);

  // Convert result set to arrays for Chart.js
  $time_utc_array = array();
  $temp_array = array();
  while ($row = mysqli_fetch_assoc($result)) {
    array_unshift($time_utc_array, $row['time']); // add to the beginning of the array
    array_unshift($temp_array, $row['Temperature']); // add to the beginning of the array
  }


  $time_local_array = [];
  foreach ($time_utc_array as $time_utc_element) {
    $time_utc_element = DateTime::createFromFormat('Y-m-d G:i:s', $time_utc_element, new DateTimeZone('UTC'));
    $time_local_element = $time_utc_element;
    $time_local_element->setTimeZone(new DateTimeZone('Asia/Tehran'));
    $time_local_array[] = $time_utc_element->format('Y-m-d g:i:s A');
  }

  $time_local_array = json_encode($time_local_array);
  $temp_array = json_encode($temp_array);

  $sql = "SELECT * FROM Heater_Status;";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);



  if (isset($_POST['toggle_LED'])) {
    $sql = "SELECT * FROM Heater_Status;";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['Status'] == 0) {
      $update = mysqli_query($conn, "UPDATE Heater_Status SET status = 1 WHERE id = 1;");
    } else {
      $update = mysqli_query($conn, "UPDATE Heater_Status SET status = 0 WHERE id = 1;");
    }
  }



  $sql = "SELECT * FROM Heater_Status;";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);



  mysqli_close($conn);
  ?>

  <div class="wrapper" id="refresh">
    <div class="col_3">
    </div>

    <div class="col_3">

      <?php echo '<h2 style="text-align: center;">The Status of the LED is: ' . $row['Status'] . '</h2>'; ?>

      <div class="col_3">
      </div>

      <div class="col_3" style="text-align: center;">
        <form action="index1.php" method="post" id="LED" enctype="multipart/form-data">
          <input id="submit_button" type="submit" name="toggle_LED" value="Toggle LED" />
        </form>

        <br>
        <br>
        <?php
        if ($row['Status'] == 0) { ?>
          <div class="led_img">
            <img id="contest_img" src="led_off.png" width="100%" height="100%">
          </div>
          <?php
        } else { ?>
          <div class="led_img">
            <img id="contest_img" src="led_on.png" width="100%" height="100%">
          </div>
          <?php
        }
        ?>

      </div>

      <div class="col_3">
      </div>
    </div>

    <div class="col_3">
    </div>
  </div>

  <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo $time_local_array; ?>,
        datasets: [{
          label: 'Temperature',
          data: <?php echo $temp_array; ?>,
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderColor: 'rgba(255, 99, 132, 1)',
          lineTension: 0.3,
          pointRadius: 0,
          borderWidth: 3
        }]
      },
      options: {
        scales: {
          yAxes: [{
            scaleLabel: {
              display: true,
              labelString: 'Temperature'
            }
          }],
          xAxes: [{
            scaleLabel: {
              display: true,
              labelString: 'ID'
            }
          }]
        }
      }
    });
  </script>
  <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
  </script>
</body>

</html>