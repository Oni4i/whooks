<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";
?>
    <title>Успешные хуки</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css"
          rel="stylesheet" />
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
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            Кошельки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Успешно обработанные вэб хуки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../error">
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
                <h2>Успешно обработанные вэб хуки</h2>
            </div>
            <div>

                <div class="container">
                    <div class="blockDatepicker">
                        <div class=" input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="">С</span>
                            </div>
                            <input data-date-format="dd/mm/yyyy" class="form-control" id="datepicker">
                        </div>
                    </div>

                    <div class="blockDatepicker">
                        <div class=" input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="">По</span>
                            </div>
                            <input data-date-format="dd/mm/yyyy" class="form-control" id="datepicker1">
                        </div>
                    </div>

                    <div class="blockPurse">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="">Кошелек</span>
                            </div>
                            <select class="form-control" id="exampleFormControlSelect1">

                            </select>
                        </div>
                    </div>

                    <div class="blockRange">
                        <div class=" input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="">От</span>
                            </div>
                            <input type="text" id="" class="form-control">
                        </div>
                    </div>

                    <div class="blockRange">
                        <div class=" input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="">До</span>
                            </div>
                            <input type="text" id="" class="form-control">
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" id="reload_btn">Обновить</button>

                </div>
                <div class="table-responsive table-sm">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr id="trHead">
                            <th scope="col">Код</th>
                            <th scope="col">Номер транзакции</th>
                            <th scope="col">Дата</th>
                            <th scope="col">Сумма поступления</th>
                            <th scope="col">Кошелек</th>
                            <th scope="col">Сумма перевода</th>
                            <th scope="col">Транзакция dkcp</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody id="table_body">

                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                        <ul id="pages-container" class="pagination justify-content-end">

                        </ul>
                    </nav>
        </main>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.ru.min.js"
        integrity="sha512-tPXUMumrKam4J6sFLWF/06wvl+Qyn27gMfmynldU730ZwqYkhT2dFUmttn2PuVoVRgzvzDicZ/KgOhWD+KAYQQ=="
        crossorigin="anonymous"></script>
<script src="../../../assets/dist/js/ajaxRequest.js"></script>
<script src="script.js"></script>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/footer.php";