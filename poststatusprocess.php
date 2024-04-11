<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Status Posting System</title>
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>

<body>
    <div class="content">
        <h1>Status Posting System</h1>

        <?php
        function returnBack()
        {
            echo "<p><a href=\"index.html\">Return to Home Page</a> | <a href=\"poststatusform.php\">Return to Post Status Page</a></p>";
        }
        
        $error = false;
        require_once ('../../files/sqlinfo.inc.php');
        $conn = @mysqli_connect($sql_host, $sql_user, $sql_pass);

        // Get an array of SQL commands from the SQL script file    
        $sqlScript = file_get_contents('sqlscript.txt');
        if ($sqlScript === false) {
            echo "<p>Error reading SQL script file</p>"; // Failure to connect to the SQL script file
            $error = true;
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
            $result = mysqli_query($conn, $sqlQueries[1]); // Check if the 'status' table exists, otherwise create it
        
            if (!$result) {
                echo "<p>Error creating table: " . mysqli_error($conn) . "</p>";
                $error = true;
            } else {
                echo "<p>Table created successfully</p>";

                if ($_SERVER["REQUEST_METHOD"] == "POST") { // When request has been made
                    // Check if the user has input in all mandatory fields
        
                    // CHECK STATUS CODE
                    if (isset($_POST["stcode"])) {
                        $stCodeStr = $_POST["stcode"];
                        $stCodePattern = "/^S\d{4}$/";
                        $stCodeDuplicate = false;
                        $stCodeFormatted = true;
                        if (!empty($stCodeStr)) { // Check if the status code string is not empty
                            if (!preg_match($stCodePattern, $stCodeStr)) {
                                echo "<br>ERROR: Wrong format! The status code must start with an S followed by four digits, like S0001";
                                $error = true;
                                $stCodeFormatted = false; // Format is incorrect
                            } else {
                                // Not empty and format is correct so check if there is duplicates
                                $sqlQueries[3] = str_replace(
                                    '{code}',
                                    $_POST['stcode'],
                                    $sqlQueries[3]
                                );

                                $result = mysqli_query($conn, $sqlQueries[3]);

                                if (!$result) {
                                    echo "<p>Error executing SQL query: " . mysqli_error($conn) . "</p>";
                                    $error = true;
                                } else { // If the query returns true there is a duplicate
                                    if (mysqli_num_rows($result) > 0) {
                                        $stCodeDuplicate = true;
                                        echo "<p><br>ERROR: Status code $stCodeStr already exists.</p>";
                                        $error = true;
                                    }
                                }
                            }
                        } else {
                            echo "<br>ERROR empty string: You need to input a status code!";
                            $error = true;
                        }
                    }

                    // CHECK STATUS
                    if (isset($_POST["st"])) {
                        $statusStr = $_POST["st"];
                        $statusPattern = "/^[A-Za-z0-9,.!?]+$/";
                        $statusChars = true;
                        if (!empty($statusStr)) { // Check if the status string is not empty
                            if (!preg_match($statusPattern, $statusStr)) {
                                echo "<p>ERROR: Please enter a status that only contains alphanumerics and/or basic punctuation.</p>";
                                $statusChars = false;
                                $error = true;
                            }
                        } else {
                            echo "<br>ERROR empty string: You need to input a status!";
                            $error = true;
                        }
                    }

                    // Check that 'date' is NOT NULL and valid
                    if (isset($_POST["date"])) {
                        $dateStr = $_POST["date"];
                        $datePattern = "/^\d{2}\/\d{2}\/\d{4}$/";
                        $dateFormatted = false;
                        if (!empty($dateStr)) { // Check if the date string is not empty
                            if (!preg_match($datePattern, $dateStr)) {
                                echo "<p><br>ERROR: Please enter a date in the format dd/mm/yyyy.</p>"; // Updated error message
                                $error = true;
                            } else {
                                $dateFormatted = true;
                                // Validate the date using DateTime class
                                $dateParts = explode('/', $dateStr);
                                $day = (int) $dateParts[0];
                                $month = (int) $dateParts[1];
                                $year = (int) $dateParts[2];

                                if (!checkdate($month, $day, $year)) {
                                    echo "<br>ERROR: The date is invalid.";
                                }
                            }
                        } else {
                            echo "<p>ERROR empty string: You need to input a date.</p>";
                            $error = true;

                            // Fetch the current server date
                            $currentDate = date("d/m/Y"); // Format: dd/mm/yyyy
                        }
                    }
                }

            }
        }


        if (isset($_POST["share"]) && !empty($_POST["share"])) {
            $selectedShare = "'" . $_POST["share"] . "'";
        } else {
            $selectedShare = NULL;
        }

        // Check which checkboxes have been selected
        if (isset($_POST["permission"])) {
            // Implode the array of selected permissions into a single string, separated by commas
            $selectedPermissions = "'" . implode(", ", $_POST["permission"]) . "'";
        }

        if (!empty($stCodeStr) && !empty($statusStr) && !empty($dateStr) && $statusChars) {
            if ($stCodeDuplicate == false) {
                if ($stCodeFormatted == true) {
                    if ($dateFormatted == true) {
                        $sqlQueries[2] = str_replace(
                            array('{stcode}', '{st}', '{date}', '{share}', '{permission}'),
                            array($_POST['stcode'], $_POST['st'], $_POST['date'], $_POST['share'], implode(", ", $_POST['permission'])),
                            $sqlQueries[2]
                        );

                        $result = mysqli_query($conn, $sqlQueries[2]);

                        if (!$result) {
                            echo "<p>Error executing SQL query: " . mysqli_error($conn) . "</p>";
                        } else {
                            echo "<p>Status posted successfully!</p>";
                            echo "<a href='index.html'>Return to Home page</a>";
                        }
                    }
                }
            }
        }
        if ($error == true) {
            returnBack();
        }
        ?>
    </div>
</body>

</html>