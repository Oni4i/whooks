<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";

if (isset($_POST) && isset($_POST["auth"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];

    $isAuthRight = false;
    if ($login == LOGIN && $password == PASSWORDCABINET) {
        $_SESSION['auth'] = 'ok';
        header("Location: /cabinet/pages/main");
    }
}

?>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Gate</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/floating-labels/">
    <!-- Bootstrap core CSS -->
    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="index.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <div class="card formCard">
        <form class="form-signin" action="./index.php" method="post">
            <?php
            if (isset($isAuthRight) && !$isAuthRight) { ?>
                <div class="form-group">
                    <h4 align="center" style="color: red">Неверный логин/пароль</h4>
                </div>
            <?}
            ?>
            <div class="blockTextAuth">
                <h1 class="text-center">Авторизация</h1>
            </div>

            <div class="form-group">
                <label for="exampleInputlogin">Логин</label>
                <input type="login" id="inputLogin" class="form-control" placeholder="Login" name="login" value="<?= isset($isAuthRight) ? $_POST['login'] : ''?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword">Пароль</label>
                <input type="password" id="inputpassword" class="form-control" placeholder="Password" name="password" required autofocus>
            </div>

            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> Запомнить меня
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="auth">Войти></a></button>
            <p class="mt-5 mb-3 text-muted text-center">&copy; 2020</p>
        </form>
    </div>
</div>





<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/footer.php";
?>


