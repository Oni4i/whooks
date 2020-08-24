<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";

if (isset($_POST) && isset($_POST["auth"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];


    $isAuthRight = false;
    if ($login == LOGIN && $password == PASSWORDCABINET) {
        setcookie("auth", "ok", "/");
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
            <div class="blockTextAuth">
                <h1 class="text-center">Авторизация</h1>
            </div>

            <div class="form-group">
                <label for="exampleInputlogin">Логин</label>
                <input type="login" id="inputLogin" class="form-control" placeholder="Login" name="login" required autofocus>
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


<!--


<?php if (isset($isAuthRight) && !$isAuthRight) { ?>
    <p style="color: red; font-size: 40px;">Wrong login/password</p>
<?php } ?>
    <form action="./index.php" method="post">
        <label>
            Логин<input type="text" id="login" name="login"
                        value="<?= isset($_POST) && isset($_POST["login"]) ? $_POST["login"] : "" ?>" required>
        </label>
        <label>
            Пароль<input type="password" id="password" name="password" required>
        </label>
        <input type="submit" name="auth">
    </form>
-->
<!--
<form action="">
    <label>
        Телефон<input type="tel" name="telephone" id="telephone">
    </label>
    <label>
        Токен кошелька<input type="text" name="token_wallet" id="token_wallet">
    </label>
    <label>
        Срок действия токена<input type="date" name="validity" id="validity">
    </label>
    <label>
        Аккаунт процессинга<input type = "text" list = "account">
    </label>
    <datalist id="account">
        <option value="acc1" label="acc1">
        <option value="acc2" label="acc2">
        <option value="acc3" label="acc3">
        <option value="acc4" label="acc4">
    </datalist>
    <label>
        Токенизированная карта:<input type = "text" list = "token_card" id="token_card_input">
    </label>
    <datalist id="token_card">
        <option value="token_card1" label="token_card1">
        <option value="token_card2" label="token_card2">
        <option value="token_card3" label="token_card3">
    </datalist>
</form>
-->


<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/footer.php";
?>


