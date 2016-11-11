<html>
<head><title>Patrick Tan - Calculator</title></head>
<body>

<h1>Calculator</h1>
By Patrick Tan<br />

Please input an expression in the following box (e.g., 10.5+20*3/25).

<ul>
    <li>Only numbers and +,-,* and / operators are allowed in the expression.
    <li>The evaluation follows the standard operator precedence.
    <li>The calculator does not support parentheses.
    <li>Dividing by zero is not allowed.
    <li>Subtracting a negative is not supported (multiple subtraction signs in a row).
</ul>

<p>
    <form method="GET">
        <input type="text" name="expr">
        <input type="submit" value="Calculate">
    </form>
</p>

<?php
  $expression = $_GET["expr"];
  $regexp = "/^[0-9\.\+\-\*\/]+$/";
  if($expression)
  {
    $expression = preg_replace('/\s+/', '', $expression);
    if(@preg_match($regexp, $expression))
    {
      $eval_expression = preg_replace('/-{2}/',"+", $expression);

      $zero = FALSE;

      function e($errno, $errstr, $errfile, $errline) {
          if ($errstr == "Division by zero") {
              global $zero;
              $zero = TRUE;
              // echo $errstr."<br />";
          }
      }

      set_error_handler('e');

      @eval('$result = '.$eval_expression.';');
    }   
    else
      $result = false;

    echo "<h2>Result!</h2>";

    if ($zero == TRUE)
        echo "Division by zero error.";
    elseif($result)
      echo $expression . ' = ' . $result;
    else
      echo "Invalid Expression";
  }
?>

</body>
</html>