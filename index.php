<!DOCTYPE html>
<html>
<head>
    <title>Fishpond Azizi</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main">        
        <input type="checkbox" id="chk" aria-hidden="true">
        <div class="login">
            <form method="post" action="newphplogin.php">
                <label for="chk" aria-hidden="true">Login</label>
                <input type="text" name="frm[username]" placeholder="User name" required="">
                <input type="password" name="frm[password]" placeholder="Password" required="">
                <button>Login</button>
                <?php if (isset($_GET['error'])) { ?>
                    <p style="
    display: flex;
    justify-content: center;
    align-items: center;
    color: red;
"><?php echo $_GET['error']; ?></p>
                <?php } ?>
            </form>
        </div>
    </div>
</body>
</html>
