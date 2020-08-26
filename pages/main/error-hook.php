<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Ошибочные хуки</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">

  <!-- Bootstrap core CSS -->
    <link href="../../assets/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>

      @import url('https://fonts.googleapis.com/css?family=Rubik:700&display=swap');
      #reload_btn {
          position: relative;
          display: inline-block;
          cursor: pointer;
          outline: none;
          border: 0;
          vertical-align: middle;
          text-decoration: none;
          font-size: inherit;
          font-family: inherit;
      }
      button#reload_btn {
          font-weight: 600;
          color: #382b22;
          text-transform: uppercase;
          padding: 1.25em 2em;
          background: #fff0f0;
          border: 2px solid #b18597;
          border-radius: 0.75em;
          transform-style: preserve-3d;
          transition: transform 150ms cubic-bezier(0, 0, 0.58, 1), background 150ms cubic-bezier(0, 0, 0.58, 1);
      }
      button#reload_btn::before {
          position: absolute;
          content: '';
          width: 100%;
          height: 100%;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: #f9c4d2;
          border-radius: inherit;
          box-shadow: 0 0 0 2px #b18597, 0 0.625em 0 0 #ffe3e2;
          transform: translate3d(0, 0.75em, -1em);
          transition: transform 150ms cubic-bezier(0, 0, 0.58, 1), box-shadow 150ms cubic-bezier(0, 0, 0.58, 1);
      }
      button#reload_btn:hover {
          background: #ffe9e9;
          transform: translate(0, 0.25em);
      }
      button#reload_btn:hover::before {
          box-shadow: 0 0 0 2px #b18597, 0 0.5em 0 0 #ffe3e2;
          transform: translate3d(0, 0.5em, -1em);
      }
      button#reload_btn:active {
          background: #ffe9e9;
          transform: translate(0em, 0.75em);
      }
      button#reload_btn:active::before {
          box-shadow: 0 0 0 2px #b18597, 0 0 #ffe3e2;
          transform: translate3d(0, 0, -1em);
      }

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
                <th scope="col">hook_txnId</th>
                <th scope="col">Ошибка dkcp</th>
                <th scope="col"><button id="reload_btn">Перезагрузка</button></th>
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

    document.getElementById("reload_btn").addEventListener('click', function() {
        document.getElementById('table_body').innerHTML = "";
        generateTableRows();
    });

    function generateTableRows() {
        getAjaxRequest("get_income_webhooks", function(response) {

            try {

                let parsedResponse = JSON.parse(response);

                console.log(parsedResponse)
                if (parsedResponse != null && parsedResponse.length > 0) {

                    parsedResponse.reverse().forEach(arr => {

                        createTableRow(arr['inc'], arr['hook_date'],
                            arr['hook_sum'], arr['hook_personId'],
                            arr['hook_sum'], arr['dkcp_result'],
                            arr['hook_txnId'], arr['dkcp_result_text'])
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

    function createTableRow(code, date, income, wallet, balance, codeDkcp, hookTxnId, errorDkcp) {
        let tr = document.createElement('tr');
        let tableDataCode = document.createElement('th');
        let tableDataDate = document.createElement('td');
        let tableDataIncome = document.createElement('td');
        let tableDataWallet = document.createElement('td');
        let tableDataBalance = document.createElement('td');
        let tableDataCodeDkcp = document.createElement('td');
        let tableDataHookTxnId = document.createElement('td');
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
        tableDataHookTxnId.innerText = hookTxnId;
        tableDataErorDkcp.innerText = errorDkcp;
        tableDataButtons.appendChild(btnRepeat);
        tableDataButtons.appendChild(btnArchive);

        btnRepeat.innerText = "Повторить";
        btnArchive.innerText = "В архив";

        btnRepeat.classList = "btn btn-primary mr-2";
        btnArchive.classList = "btn btn-warning";

        btnRepeat.type = "button";
        btnArchive.type = "button";

        tableDataButtons.style.width = '225px';

        btnRepeat.addEventListener('click', function() {

            getAjaxRequest("repeat_operation&id=" + code, function (response) {

                try {

                    let parsedResponse = JSON.parse(response);


                    if (parsedResponse != null) {

                        let responseCode = parsedResponse['response'];
                        console.log(responseCode)
                        if (responseCode == '200') {

                            tr.remove();
                            alert("Запрос отпрален успешно, данные сохранены")
                        } else if (responseCode == '1') {

                            alert("Не найден hook_txnId по данному коду")
                        } else if (responseCode == '2') {

                            alert("Отправка запроса на скрипт прошла успешно, но требуется изменить запись в базе данных вручную");
                        }  else {

                            alert("Неизвестный ответ")
                        }
                    }

                } catch (e) {

                    alert("Неизвестная ошибка от сервера")
                }
            })

        });

        btnArchive.addEventListener('click', function () {

            getAjaxRequest("archive&id=" + code, function (response) {

                try {

                    let parsedResponse = JSON.parse(response);

                    if (parsedResponse != null) {

                        let responseCode = parsedResponse['response'];
                        console.log(responseCode)
                        if (responseCode == '200') {

                            tr.remove();
                            alert("Перенос в архив успешно завершён")
                        } else if (responseCode == '1') {

                            alert("Не удалось вставить запись в архив")
                        } else if (responseCode == '2') {

                            alert("Вставка в архив прошла успешно, но удаление из income_webhooks не произведено");
                        }  else {

                            alert("Неизвестный ответ")
                        }
                    }

                } catch (e) {

                    alert("Неизвестная ошибка от сервера")
                }
            })

        });

        tr.appendChild(tableDataCode);
        tr.appendChild(tableDataDate);
        tr.appendChild(tableDataIncome);
        tr.appendChild(tableDataWallet);
        tr.appendChild(tableDataBalance);
        tr.appendChild(tableDataCodeDkcp);
        tr.appendChild(tableDataHookTxnId);
        tr.appendChild(tableDataErorDkcp);
        tr.appendChild(tableDataButtons);

        document.getElementById('table_body').appendChild(tr);

    }
</script>