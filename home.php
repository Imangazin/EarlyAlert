<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/styles.css">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- jquery -->
<script src="https://s.brightspace.com/lib/jquery/3.7.1/jquery.min.js"></script>
<style>
        .btn-primary {
                color: #ffffff;
                background-color: #006fbf;
        }
        .btn-primary:hover {
                color: #ffffff;
                background-color: #004489;
        }
        .btn-primary:active {
                color: #ffffff;
                background-color: #004489;
        }
  </style>
</head>
<body style="font-family: 'Lato', sans-serif;color: #202122;">
  <div class="container">
    <?php 
    $message = $hasAuditor ? "Your advisor(s) can see your course progress for current semester.<br>":"Please consent to give your advisor access to your course progress for current semester.<br>";
    echo $message; 
    ?>
    <form method="post" id="earlyAlertForm">
      <input type="hidden" name="session_id" value='<?php echo session_id(); ?>'>
      <input type ="hidden" name="myAuditors" value='<?php echo $myAuditors; ?>'>
      <?php 
        $advisorSelect = '<div class="mb-3">
          <select class="form-select" id="advisor" required name="advisor" >
            <option value="" disabled selected>Select Advisor</option>';
          foreach($advisors as $advisorId=>$advisorName){
            $advisorSelect .= "<option value=\"$advisorId\">$advisorName</option>";
          }
        $advisorSelect .= '</select>
        </div>';
        if (!$hasAuditor) echo $advisorSelect;
      ?>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary"><?php if ($hasAuditor) echo 'Cancel'; else echo 'Consent';?></button>
      </div>
    </form>
    <div id="responseContainer" tabindex="-1"></div>
  <div>
<script type="text/javascript" src="js/earlyAlert.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
