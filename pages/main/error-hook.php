<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Ошибочные хуки</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">

  <!-- Bootstrap core CSS -->
    <link href="../../assets/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }
  </style>
  <!-- Custom styles for this template -->
  <link href="index.css" rel="stylesheet">
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
              <a class="nav-link" href="index.php">
                Кошельки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                Успешно обработанные вэб хуки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="error-hook.php">
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
          <table class="table table-hover table-bordered">
            <thead>
              <tr id="trHead">
                <th scope="col">Код</th>
                <th scope="col">Дата</th>
                <th scope="col">Сумма поступления</th>
                <th scope="col">Кошелек</th>
                <th scope="col">Баланс кошелька</th>
                <th scope="col">Код dkcp</th>
                <th scope="col">Ошибка dkcp</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody id="table_body">

            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

<script>

    generateTableRows()

    function generateTableRows() {
        getAjaxRequest("get_income_webhooks", function(response) {

            try {

                let parsedResponse = JSON.parse(response);

                console.log(parsedResponse)
                if (parsedResponse != null && parsedResponse.length > 0) {

                    parsedResponse.forEach(arr => {

                        createTableRow(arr['inc'], arr['hook_date'],
                            arr['hook_sum'], arr['hook_personId'],
                            arr['hook_sum'], arr['dkcp_result'],
                            arr['hook_errorCode'])
                    })
                }
            } catch (e) {

                console.log(e)
            }
        })
    }

    function getAjaxRequest(request, callback) {

        return fetch("ajax.php?" + request,
            {
                method: "GET",
                headers:{"content-type":"application/json"}
            })
            .then(response => {
                if (response.status !== 200) {
                    return Promise.reject();
                }
                return response.text()
            })
            .then(response => {
                if (callback)
                    callback(response);
            })
            .catch((e) => console.log(e))
    }

    function createTableRow(code, date, income, wallet, balance, codeDkcp, errorDkcp) {
        let tr = document.createElement('tr');
        let tableDataCode = document.createElement('th');
        let tableDataDate = document.createElement('td');
        let tableDataIncome = document.createElement('td');
        let tableDataWallet = document.createElement('td');
        let tableDataBalance = document.createElement('td');
        let tableDataCodeDkcp = document.createElement('td');
        let tableDataErorDkcp = document.createElement('td');
        let tableDataButtons = document.createElement('td');
        let btnRepeat = document.createElement('button');
        let btnArchive = document.createElement('button');

        tableDataCode.innerText = code;
        tableDataDate.innerText = date;
        tableDataIncome.innerText = income;
        tableDataWallet.innerText = wallet;
        tableDataBalance.innerText = balance;
        tableDataCodeDkcp.innerText = codeDkcp;
        tableDataErorDkcp.innerText = errorDkcp;
        tableDataButtons.appendChild(btnRepeat);
        tableDataButtons.appendChild(btnArchive);

        btnRepeat.innerText = "Повторить";
        btnArchive.innerText = "В архив";

        btnRepeat.classList = "btn btn-primary mr-2";
        btnArchive.classList = "btn btn-warning";

        btnRepeat.type = "button";
        btnArchive.type = "button";

        tr.appendChild(tableDataCode);
        tr.appendChild(tableDataDate);
        tr.appendChild(tableDataIncome);
        tr.appendChild(tableDataWallet);
        tr.appendChild(tableDataBalance);
        tr.appendChild(tableDataCodeDkcp);
        tr.appendChild(tableDataErorDkcp);
        tr.appendChild(tableDataButtons);

        document.getElementById('table_body').appendChild(tr);

    }
</script>