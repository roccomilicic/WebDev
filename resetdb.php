<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Status Posting System</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="content">
        <h1>Reset Database</h1>

        <?php
        require_once ('../../files/sqlinfo.inc.php');
        $conn = @mysqli_connect($sql_host, $sql_user, $sql_pass);

        // Get an array of SQL commands from the SQL script file    
        $sqlScript = file_get_contents('sqlscript.txt');
        if ($sqlScript === false) {
            echo "<p>Error reading SQL script file</p>"; // Failure to connect to the SQL script file
        } else {
            $queries = explode(';', $sqlScript);
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $sqlQueries[] = $query; // This array will be use to access the SQL commands
                }
            }
        }

        if (!$conn) {
            echo "<p>Database connection failure</p>"; // Failure to connect
        } else {
            echo "<p>Database connection successful</p>"; // Successful connection
        
            mysqli_select_db($conn, $sql_db); // Select the khf9116 database
        
            // Check if the status table exists
            $tableExists = mysqli_query($conn, $sqlQueries[5]);

            if (!$tableExists) {
                echo "<p>No table 'status' found in the system.";
            } else {
                $result = mysqli_query($conn, $sqlQueries[6]);

                if (!$result) {
                    echo "<p>Error executing SQL query: " . mysqli_error($conn) . "</p>";
                    returnBack();
                } else {
                    echo "Successfully dropped 'status' table.";
                    echo" <a href=\"index.html\">Return to Home</a>";
                }
            }
        }
        ?>
    </div>
</body>

</html>