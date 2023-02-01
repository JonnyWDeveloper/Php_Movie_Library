<?php // query.php
//Helpfull error reporting while developing.
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db_config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login * PHP Session * Prepared Statements</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="display">
        <?php
        $error = ''; //Variable to store error message
        $counter = 1;
        /* Always show this table: STORED MOVIES.
         PREPARED STATEMENT - SELECT - Prepare an SQL statement for execution */
        $stmt = $mysqli->prepare(
            "SELECT movie.id,category.name,title,director,year FROM category, movie WHERE category.id = movie.category_id"
        );
        $stmt->execute(); //Executes a prepared Query.
        $result = $stmt->get_result(); //Gets a result set from a prepared statement.
        if (!$result) die("Fatal Error");

        $update = isset($_REQUEST['update']) ? 1 : '';
        $delete = isset($_REQUEST['delete']) ? 1 : '';
        $id = isset($_REQUEST['id']) ?   $_REQUEST['id'] : '';

        //Prints part of the HTML table.
        echo "<table align='center'><caption class='blueShadow' style='padding:20px;font-size:x-large;font-weight:'>Stored movies</caption>";
        echo "<tr>
        <th>&nbsp;</th>
        <th class='greenShadow'>Title</th>
        <th class='greenShadow'>Director</th>
        <th class='greenShadow'>Year</th>
        <th class='greenShadow'>Genre</th>
        <th class='greenShadow'>Update</th>
        <th class='greenShadow'>Delete</th>
        </tr>
        <tr>";
        //Print the field content for each row as long as there are rows.
        while ($row = $result->fetch_assoc()) { //Fetch a result row as an associative array
            echo '<th>' . $counter++ . '</th>';
            echo '<th>' . htmlspecialchars($row['title']) . '</th>';  //Convert special characters to HTML entities
            echo '<th>' . htmlspecialchars($row['director']) . '</th>';
            echo '<th>' . htmlspecialchars($row['year']) . '</th>';
            echo '<th>' . htmlspecialchars($row['name']) . '</th>';
            echo '<th><a href=query.php?id=' . htmlspecialchars($row['id']) . '&update=1>Edit</a></th>';
            echo '<th><a href=query.php?id=' . htmlspecialchars($row['id']) . '&delete=1>Delete</a></th>';
            echo "</tr>";
        }
        echo "</table><br>";
        /* End of table: STORED MOVIES.*/
        /* PREPARED STATEMENT - INSERT INTO - Prepare an SQL statement for execution */
        if (isset($_POST['Insert'])) //Pressed Submit button to insert a movie.
        {
            if (empty($_POST['title']) || empty($_POST['director']) || empty($_POST['year']) || empty($_POST['category_id'])) {
                $error = "You have to fill in all the details to store the movie";
            } else {
                $stmt = $mysqli->prepare("INSERT INTO movie (title, director, year, category_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssii", $_POST['title'], $_POST['director'],  $_POST['year'], $_POST['category_id']);
                $stmt->execute();
                $stmt->close();
                header("Location:query.php");
            }
        }
        if (isset($_POST['Update'])) {

            $title = $_POST['title'];
            $director = $_POST['director'];
            $year = $_POST['year'];
            $category_id = $_POST['category_id'];
            $hidden = $_POST['hidden'];
            $sql = "UPDATE movie SET title = ?, director = ?, year = ?, category_id = ? WHERE id =?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssiii', $title, $director, $year, $category_id, $hidden);
            $stmt->execute();
            $stmt->close();
            header("Location:query.php");

            /* THIS ALSO WORKS! However not a prepared statement.
            $title = $_POST['title'];
            $director = $_POST['director'];
            $year = $_POST['year'];
            $category_id = $_POST['category_id'];
            $hidden = $_POST['hidden'];
            $sql = "UPDATE movie SET title='" . $title . "',
            director='" . $director . "', year='" . $year . "',
            category_id='" . $category_id . "' WHERE id='" . $hidden . "'";
            $result = $mysqli -> query($sql); 
            header("Location:query.php");*/
        }
        /* PREPARED STATEMENT - DELETE - Prepare an SQL statement for execution */
        if (isset($_REQUEST['delete'])) {
            $stmt = $mysqli->prepare("DELETE FROM movie WHERE id = ?");
            $stmt->bind_param('i', $_REQUEST['id']);
            $stmt->execute();
            $stmt->close();
            header("Location:query.php");
        }
        ?>
        <h2>Movies</h2>
        <span><?php echo $error; ?></span>
        <form style="text-align: left;" action="query.php" method="post">
            <h4 style="margin-left: -7px">Choose a genre:</h4>
            <?php
            //This is for a single movie to be shown and maybe edited and updated.
            if ($update && $id) {
                /* PREPARED STATEMENT - SELECT - Prepare an SQL statement for execution */
                $stmt = $mysqli->prepare("SELECT * FROM movie WHERE id =?");
                $stmt->bind_param('i', $_REQUEST['id']);
                $stmt->execute(); //Executes a prepared Query.
                $result_update = $stmt->get_result(); //Gets a result set from a prepared statement.
                if (!$result_update) die("Fatal Error");
                while ($row_update = $result_update->fetch_assoc()) { //Fetch a result row as an associative array.  
            ?>
                    <br>
                    <input type="radio" id="action" name="category_id" <?php echo ($row_update['category_id'] == 1) ? 'checked' : ''; ?> value="1" required>
                    <label for="action">Action</label><br>
                    <input type="radio" id="adventure" name="category_id" <?php echo ($row_update['category_id'] == 2) ? 'checked' : ''; ?> value="2">
                    <label for="adventure">Adventure</label><br>
                    <input type="radio" id="animation" name="category_id" <?php echo ($row_update['category_id'] == 3) ? 'checked' : ''; ?> value="3">
                    <label for="animation">Animation</label><br>
                    <input type="radio" id="comedy" name="category_id" <?php echo ($row_update['category_id'] == 4) ? 'checked' : ''; ?> value="4">
                    <label for="comedy">Comedy</label><br>
                    <input type="radio" id="crime" name="category_id" <?php echo ($row_update['category_id'] == 5) ? 'checked' : ''; ?> value="5">
                    <label for="crime">Crime</label><br>
                    <input type="radio" id="documentary" name="category_id" <?php echo ($row_update['category_id'] == 6) ? 'checked' : ''; ?> value="6">
                    <label for="documentary">Documentary</label><br>
                    <input type="radio" id="drama" name="category_id" <?php echo ($row_update['category_id'] == 7) ? 'checked' : ''; ?> value="7">
                    <label for="drama">Drama</label><br>
                    <input type="radio" id="horror" name="category_id" <?php echo ($row_update['category_id'] == 8) ? 'checked' : ''; ?> value="8">
                    <label for="horror">Horror</label><br>
                    <input type="radio" id="fantasy" name="category_id" <?php echo ($row_update['category_id'] == 9) ? 'checked' : ''; ?> value="9">
                    <label for="fantasy">Fantasy</label><br>
                    <input type="radio" id="romance" name="category_id" <?php echo ($row_update['category_id'] == 10) ? 'checked' : ''; ?> value="10">
                    <label for="romance">Romance</label><br>
                    <input type="radio" id="musical" name="category_id" <?php echo ($row_update['category_id'] == 11) ? 'checked' : ''; ?> value="11">
                    <label for="musical">Musical</label><br>
                    <input type="radio" id="scifi" name="category_id" <?php echo ($row_update['category_id'] == 12) ? 'checked' : ''; ?> value="12">
                    <label for="scifi">Science Fiction</label><br>
                    <input type="radio" id="thriller" name="category_id" <?php echo ($row_update['category_id'] == 13) ? 'checked' : ''; ?> value="13">
                    <label for="thriller">Thriller</label><br>
                    <input type="radio" id="western" name="category_id" <?php echo ($row_update['category_id'] == 14) ? 'checked' : ''; ?> value="14">
                    <label for="western">Western</label>
                    <br><br><label for="title">Title</label>
                    <input id="title" name="title" placeholder="Title" type="text" value="<?php echo $row_update['title']; ?>" required><br><br>
                    <label for="director">Director</label>
                    <input id="director" name="director" placeholder="Director" type="text" value="<?php echo $row_update['director']; ?>" required><br><br>
                    <label for="year">Year</label>
                    <input id="year" name="year" placeholder="Year" type="text" value="<?php echo $row_update['year']; ?>" required><br><br><br>
                    <input name="Update" type="submit" value="Save">
                <?php }
            } else { //This is only for to INSERT a movie.
                ?>
                <br>
                <input type="radio" id="action" name="category_id" value="1" required>
                <label for="action">Action</label><br>
                <input type="radio" id="adventure" name="category_id" value="2">
                <label for="adventure">Adventure</label><br>
                <input type="radio" id="animation" name="category_id" value="3">
                <label for="animation">Animation</label><br>
                <input type="radio" id="comedy" name="category_id" value="4">
                <label for="comedy">Comedy</label><br>
                <input type="radio" id="crime" name="category_id" value="5">
                <label for="crime">Crime</label><br>
                <input type="radio" id="documentary" name="category_id" value="6">
                <label for="documentary">Documentary</label><br>
                <input type="radio" id="drama" name="category_id" value="7">
                <label for="drama">Drama</label><br>
                <input type="radio" id="horror" name="category_id" value="8">
                <label for="horror">Horror</label><br>
                <input type="radio" id="fantasy" name="category_id" value="9">
                <label for="fantasy">Fantasy</label><br>
                <input type="radio" id="romance" name="category_id" value="10">
                <label for="romance">Romance</label><br>
                <input type="radio" id="musical" name="category_id" value="11">
                <label for="musical">Musical</label><br>
                <input type="radio" id="scifi" name="category_id" value="12">
                <label for="scifi">Science Fiction</label><br>
                <input type="radio" id="thriller" name="category_id" value="13">
                <label for="thriller">Thriller</label><br>
                <input type="radio" id="western" name="category_id" value="14">
                <label for="western">Western</label>
                <br><br><label for="title">Title</label>
                <input id="title" name="title" placeholder="Title" type="text" required><br><br>
                <label for="director">Director</label>
                <input id="director" name="director" placeholder="Director" type="text" required><br><br>
                <label for="year">Year</label>
                <input id="year" name="year" placeholder="Year" type="text" required><br><br><br>
                <input name="Insert" type="submit" value="Save">
            <?php } ?>
            <input name="hidden" type="hidden" value="<?php echo $_REQUEST['id'] ?>">

        </form>
    </div>
</body>

</html>