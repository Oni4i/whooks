<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/pages/main/ajax.php";
?>

  <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/floating-labels/">
  <!-- Bootstrap core CSS -->
  <link href="./../../assets/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="index.css" rel="stylesheet">
</head>

<body>
  <table class="table table-hover table-bordered">
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

    function decode_utf8(s) {
        return decodeURIComponent(escape(s));
    }

</script>