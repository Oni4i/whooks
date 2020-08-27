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
        <div class="spinners">
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
        </div>
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            Кошельки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="successfull-hook.php">
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
                <h2>Успешно обработанные вэб хуки</h2>
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

<script>

    generateTableRows()

    document.getElementById("reload_btn").addEventListener('click', function() {
        document.getElementById('table_body').innerHTML = "";
        generateTableRows();
    });

    function generateTableRows() {
        getAjaxRequest("get_suc_webhooks&page=1", function(response) {

            try {

                let parsedResponse = JSON.parse(response);

                console.log(parsedResponse)
                if (parsedResponse != null && parsedResponse.length > 0) {

                    parsedResponse.reverse().forEach(arr => {

                        createTableRow(arr['inc'], arr['hook_date'],
                            arr['hook_sum'], arr['hook_personId'],
                            arr['account_balance'], arr['next_operation'],
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

        tableDataCode.innerText = code;
        tableDataDate.innerText = date;
        tableDataIncome.innerText = income;
        tableDataWallet.innerText = wallet;
        tableDataBalance.innerText = balance;
        tableDataCodeDkcp.innerText = codeDkcp;
        tableDataHookTxnId.innerText = hookTxnId;
        tableDataErorDkcp.innerText = errorDkcp;

        tr.appendChild(tableDataCode);
        tr.appendChild(tableDataHookTxnId);
        tr.appendChild(tableDataDate);
        tr.appendChild(tableDataIncome);
        tr.appendChild(tableDataWallet);
        tr.appendChild(tableDataBalance);
        tr.appendChild(tableDataCodeDkcp);
        tr.appendChild(tableDataErorDkcp);

        document.getElementById('table_body').appendChild(tr);

    }


    function createSpinner() {
        let spinner = document.createElement('div');
        let spinnerSpan = document.createElement('span');

        spinner.role = "status";

        spinner.classList = "spinner-border text-dark";
        spinnerSpan.classList = "sr-only";

        spinnerSpan.innerText = "Loading...";
        spinner.appendChild(spinnerSpan);

        spinner.style.textAlign ="center";

        return spinner;
    }


</script>