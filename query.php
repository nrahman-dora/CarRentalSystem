<?php
// Oracle connection
$username = "car_rental";         // your Oracle DB username
$password = "mypassword123";      // your Oracle DB password
$connection_string = "//localhost/XEPDB1";  // or "//localhost/XE"

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    echo "<p style='color:red;'>❌ Connection failed: " . htmlentities($e['message']) . "</p>";
    exit;
}

// Get SQL query from form
$query = $_POST['query'] ?? '';

if (!$query) {
    echo "<p style='color:red;'>⚠️ No query submitted.</p>";
    exit;
}

// Run the query
$stid = oci_parse($conn, $query);

if (!$stid) {
    $e = oci_error($conn);
    echo "<p style='color:red;'>❌ Parse error: " . htmlentities($e['message']) . "</p>";
    exit;
}

$r = @oci_execute($stid);
if (!$r) {
    $e = oci_error($stid);
    echo "<p style='color:red;'>❌ Execution error: " . htmlentities($e['message']) . "</p>";
    exit;
}

// Display results if it's a SELECT query
if (stripos($query, "select") === 0) {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    $ncols = oci_num_fields($stid);
    echo "<tr>";
    for ($i = 1; $i <= $ncols; $i++) {
        echo "<th>" . htmlentities(oci_field_name($stid, $i)) . "</th>";
    }
    echo "</tr>";

    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr>";
        foreach ($row as $item) {
            echo "<td>" . htmlentities($item !== null ? $item : "&nbsp;") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:green;'>✅ Query executed successfully.</p>";
}

oci_free_statement($stid);
oci_close($conn);
?>
