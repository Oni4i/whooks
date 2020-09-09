<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "header.php";
?>
  <title>Кошельки</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">
  <link href="../../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../assets/dist/js/jquery-1.11.2.min.js"></script>
    <script src="../../assets/dist/js/jquery.mask.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
  <link href="index.css" rel="stylesheet">
</head>

<body>
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавление кошелька</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">

                    <div class="form-group">
                        <label for="exampleInputPhone">Номер телефона</label>
                        <input type="phone" id="inputPhone" class="form-control" placeholder="+7 (999) 999 23 23"
                               data-isValid="0" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputToken">Токен кошелька</label>
                        <input type="token" id="inputToken" class="form-control" placeholder="" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputToken">Срок действия токена</label>
                        <input type="date" id="inputDate" class="form-control" placeholder="дд.мм.гггг"
                               data-isValid="0" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Аккаунт процессинга</label>
                        <select class="form-control" id="exampleFormControlSelect1"></select>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Токенизированная карта</label>
                        <select class="form-control" id="exampleFormControlSelect2"></select>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <!-- <button type="button" class="btn btn-primary">Сохранить</button> -->
                <button class="btn btn-primary" id="submit" type="submit"
                        onsubmit="return false;">Отправить</button>
            </div>
        </div>
    </div>
</div>

  <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Gate Dev</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse"
      data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <a class="nav-link" id="exitBtn" href="index.php">Выйти</a>
      </li>
    </ul>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="sidebar-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="index.php">
                Кошельки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="success/">
                Успешно обработанные вэб хуки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="error/">
                Ошибочно обработанные вэб хуки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="accounts/">
                Аккаунты процессинга
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div
          class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h2>Кошельки</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm">
            <thead>
              <tr id="trHead">
                <th scope="col">#</th>
                <th scope="col">Номер телефона</th>
                <th scope="col">Токен кошелька</th>
                <th scope="col">Срок действия токена</th>
                <th scope="col">Аккаунт процессинга</th>
                <th scope="col">Токенизированная карта</th>
                <th scope="col"><button type="button" data-toggle="modal" data-target="#addModal" class="btn btn-success d-block mx-auto">Добавить</button></th>
              </tr>
            </thead>
            <tbody id="table_body">

            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  <script src="../../assets/dist/js/ajaxRequest.js?2"></script>
  <script src="script.js?5"></script>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "footer.php";
?>