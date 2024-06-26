<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
    <?php echo $message; ?>
    <form method="post" id="earlyAlertForm">
      <input type="hidden" name="session_id" value='<?php echo session_id(); ?>'>
      <input type ="hidden" name="groupCategoryId" value='<?php echo $groupCategoryId; ?>'>
      <input type ="hidden" name="groupId" value='<?php echo $groupId; ?>'>
      <?php 
        $advisorSelect = '<div class="mb-3">
          <select class="form-select" id="advisor" required name="advisor" >
            <option value="" disabled selected>Select Advisor</option>';
        foreach($advisors as $advisorId=>$advisorName){
            $advisorSelect .= "<option value=\"$advisorId\">$advisorName</option>";
        }
        $advisorSelect .= '</select></div>';
        if (!$hasAuditor) {
          echo $advisorSelect; 
        }
      ?>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary" id="early_alert_btn"><?php if ($hasAuditor) echo 'Cancel'; else echo 'Consent';?></button>
      </div>
    </form>
    <div id="responseContainer" tabindex="-1"></div>
  <div>
<script type="text/javascript" src="js/earlyAlert.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
