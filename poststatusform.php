<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Status Posting System</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1>Status Posting System</h1>
<form class="form-container" action="poststatusprocess.php" method="post">
    <label>Status Code: <input type="text" name="stcode"></label><br><br>
    <label>Status: <input type="text" name="st"></label><br><br>

    <label>Share: 
        <input type="radio" name="share" value="university">University
        <input type="radio" name="share" value="class">Class
        <input type="radio" name="share" value="private">Private
    </label>
    <br><br>

    <label>Date: <input type="text" name="date"> in DD/MM/YYYY format</label>

    <label>Permission: 
        <input type="checkbox" name="permission[]" value="like">Allow Like
        <input type="checkbox" name="permission[]" value="comment">Allow Comments
        <input type="checkbox" name="permission[]" value="share">Allow Share
    </label>
    <br><br>

    <input class="btn" type="submit" name="submit" value="Submit Status">
</form>
<a href="index.html">Return to home page</a>
</body>
</html>
