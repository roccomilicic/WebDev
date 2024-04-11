<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Status Search Result</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1>Status Search Result</h1>

    <div class="content">
        <?php
        function returnBack()
        {
            echo "<p><a href=\"index.html\">Return to Home Page</a> <a href=\"searchstatusform.html\">Return to Search Status Page</a></p>";
        }

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

        require_once ('../../files/sqlinfo.inc.php');
        $conn = @mysqli_connect($sql_host, $sql_user, $sql_pass);

        if (!$conn) {
            echo "<p>Database connection failure</p>"; // Failure to connect
        } else {
            echo "<p>Database connected</p>"; // Failure to connect
            mysqli_select_db($conn, $sql_db); // Select the khf9116 database
        
            // Check if the status table exists
            $tableExists = mysqli_query($conn, $sqlQueries[5]);

            if (!$tableExists) {
                // Table does not exist, provide a message and a link to the post status page
                echo "<p>No status found in the system. Please go to the <a href=\"poststatusform.php\">post status page</a> to post one.</p>";
                returnBack();
            } else {
                if (isset($_GET["Search"])) {
                    $searchString = trim($_GET["Search"]); // Trim whitespace from the search string
                    // Check if the search string is not empty
                    if (!empty($searchString)) {
                        $sqlQueries[4] = str_replace(
                            '{st}',
                            $searchString,
                            $sqlQueries[4]
                        );
                        $result = mysqli_query($conn, $sqlQueries[4]);

                        if (!$result) {
                            echo "<p>Error executing SQL query: " . mysqli_error($conn) . "</p>";
                            returnBack();
                        } else {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<br>Search string found!</p>";
                                // Fetch and display each row's data
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<Strong>Status Code:</Strong> " . $row['status_code'] . "<br>";
                                    echo "<Strong>Status:</Strong> " . $row['status'] . "<br>";
                                    echo "<Strong>Date:</Strong>" . $row['date'] . "<br>";
                                    echo "<Strong>Share:</Strong>" . $row['share'] . "<br>";
                                    echo "<Strong>Permission:</Strong> " . $row['permission'] . "<br><br>";
                                }

                                returnBack();
                            } else {
                                echo "Status not found. Please try a different keyword.";
                                returnBack();
                            }
                        }

                    } else {
                        echo "<p>Error: The search string is empty. Please enter a keyword to search.</p>"; // Empty search string on request
                        returnBack();
                    }
                }
            }
        }
        ?>
    </div>
</body>

</html>
