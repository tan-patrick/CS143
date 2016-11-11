<html>
<head><title>Patrick Tan - Movie Database</title></head>
<body>

<h1>Movie Database</h1>
By Patrick Tan<br />

Type an SQL query in the following box:
<p>
  <form method="GET">
    <textarea name="query" cols="60" rows="8"><?php echo htmlspecialchars($_GET["query"]);?></textarea>
    <input type="submit" value="Submit" />
  </form>
</p>

<p><small>Note: tables and fields are case sensitive. Run "show tables" to see the list of
available tables.</small>
</p>

<?php
  $sql = $_GET["query"];
  if($sql)
  {
    $db_connection = mysql_connect("localhost", "cs143", "");
    if (mysqli_connect_errno())
    {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    mysql_select_db("CS143", $db_connection);
    $rs = mysql_query($sql, $db_connection);
    if (!$rs) {
      die('Invalid query: ' . mysql_error());
    }

    echo "<h2>Results from MySQL:</h2>";

    echo '<table border=1 cellspacing=1 cellpadding=2><tr align=center>';
    for($i = 0; $i < mysql_num_fields($rs); $i++)
    {
      $col_name = mysql_fetch_field($rs, $i);
      echo '<td><b>' . $col_name->name . '</b></td>';
    }
    echo '</tr>';

    while($row = mysql_fetch_row($rs))
    {
      echo '<tr align=center>';
      foreach($row as $data)
      {
        echo '<td>' . $data . '</td>';
      }
      echo "</tr>";
    }

    echo "</table>";

    mysql_close($db_connection);
  }
?>

</body>
</html>