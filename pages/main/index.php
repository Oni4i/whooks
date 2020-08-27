
  <title>Кошельки</title>

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
              <a class="nav-link active" href="index.php">
                Кошельки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="successfull-hook.php">
                Успешно обработанные вэб хуки
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="error-hook.php">
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
                <th scope="col"><a href="add/index.php"><button type="button" class="btn btn-success d-block mx-auto">Добавить</button></a></th>
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

          getAjaxRequest("get_wallets", function(response) {

              try {

                  let parsedResponse = JSON.parse(response);

                  if (parsedResponse != null && parsedResponse.length > 0) {

                      parsedResponse.forEach(arr => {

                          createTableRow(arr['code'], arr['wallet_phone'],
                              arr['wallet_token'], arr['wallet_token_valid_date'],
                              arr['login'], arr['card_token'])
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

      function createTableRow(id, phone, wallet, date, login, card) {
          let tr = document.createElement('tr');
          let tableDataId = document.createElement('td');
          let tableDataPhone = document.createElement('td');
          let tableDataWallet = document.createElement('td');
          let tableDataDate = document.createElement('td');
          let tableDataLogin = document.createElement('td');
          let tableDataCard = document.createElement('td');
          let tableDataDelete = document.createElement('td');
          let buttonDelete = document.createElement('button');

          tableDataId.innerText = id;
          tableDataPhone.innerText = phone;
          tableDataWallet.innerText = wallet;
          tableDataDate.innerText = date;
          tableDataLogin.innerText = login;
          tableDataCard.innerText = card;
          buttonDelete.innerText = 'Удалить';
          tableDataDelete.appendChild(buttonDelete);

          tableDataId.scope = 'row';

          buttonDelete.type = 'button';
          buttonDelete.classList = 'btn btn-danger d-block mx-auto';
          buttonDelete.dataset.account = id;
          buttonDelete.addEventListener('click', function() {

              if (buttonDelete.dataset.account) {

                  getAjaxRequest('delete_wallet&id=' + id + '&wallet=' + encode_utf8(wallet), function(response) {

                      try {

                          let parsedResponse = JSON.parse(response);
                          console.log(response)

                          if (parsedResponse != null) {

                              let responseCode = parsedResponse['response'];

                              if (responseCode == '200') {

                                  tr.remove();
                                  alert("Удаление хука прошло успешно")
                              } else if (responseCode == '1') {

                                  alert("Удаление хука не удалось")
                              } else if (responseCode == '2') {

                                  alert("Удаление хука прошло успешно, но требуется удалить запись из базы данных вручную");
                              }  else {

                                  alert("Неизвестный ответ")
                              }
                          }



                      } catch (e) {

                          alert("Неизвестная ошибка от сервера")
                      }
                  })
              }
          })

          tr.appendChild(tableDataId);
          tr.appendChild(tableDataPhone);
          tr.appendChild(tableDataWallet);
          tr.appendChild(tableDataDate);
          tr.appendChild(tableDataLogin);
          tr.appendChild(tableDataCard);
          tr.appendChild(tableDataDelete);

          document.getElementById('table_body').appendChild(tr);
      }

      function encode_utf8(s) {
          return unescape(encodeURIComponent(s));
      }
  </script>