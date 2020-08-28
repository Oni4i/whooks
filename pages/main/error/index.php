<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";
?>
    <title>Ошибочные хуки</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">
    <link href="../../../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>

<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Gate Dev</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse"
            data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="../index.html">Выйти</a>
        </li>
    </ul>
</nav>

<div class="container-fluid">

    <div class="row">
        <div class="spinners">
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
        </div>
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            Кошельки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../success/">
                            Успешно обработанные вэб хуки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Ошибочно обработанные вэб хуки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Аккаунты процессинга
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Ошибочно обработанные вэб хуки</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm">
                    <thead>
                    <tr id="trHead">

                        <th scope="col">Код</th>
                        <th scope="col">Номер транзакции</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Сумма поступления</th>
                        <th scope="col">Кошелек</th>
                        <th scope="col">Баланс кошелька</th>
                        <th scope="col">Статус</th>
                        <th scope="col">Ошибка dkcp</th>
                        <th scope="col"><button id="reload_btn" type="button" class="btn btn-secondary">Перезагрузка</button></th>
                    </tr>
                    </thead>
                    <tbody id="table_body">
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script src="../../../assets/dist/js/ajaxRequest.js"></script>
<script src="script.js"></script>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/footer.php";
