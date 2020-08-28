<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Успешные хуки</title>

  <!-- Bootstrap core CSS -->
  <!-- <link href="../../assets/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

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


    button[disabled] {
        background-color: #e9ecef;
    }
  </style>
  <!-- Custom styles for this template -->
  <link href="index.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />

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
              <a class="nav-link active" href="success-hook.php">
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
  <script>
    $('#datepicker, #datepicker1').datepicker({
      language: 'ru',
      format: 'dd.mm.yyyy',
      weekStart: 1,
      daysOfWeekHighlighted: "6,0",
      autoclose: true,
      todayHighlight: true,
    });
    $('#datepicker, #datepicker1').datepicker("setDate", new Date());

    
    generateTableRows(true, 1, true)
    let currentPage = 1;


    function generateTableRows(isFirstGenerate, pageNumber, isRequiredReCreatePages) {

        let formControl = document.getElementsByClassName('form-control');

        let dateStart = formControl[0].value;
        let dateEnd = formControl[1].value;
        let wallet = formControl[2].value;
        let sumStart = formControl[3].value;
        let sumEnd = formControl[4].value;

        dateStart = convertDate(dateStart);
        dateEnd = convertDate(dateEnd);

        let ajaxRequest;
        if (isFirstGenerate)
            ajaxRequest = "generate_suc_page";
        else
            ajaxRequest = `get_suc_webhooks&type=certain&page=${pageNumber}&date_start=${dateStart}&date_end=${dateEnd}&wallet=${wallet}&sum_start=${sumStart}&sum_end=${sumEnd}`;

        getAjaxRequest(ajaxRequest, function(response) {

            try {

                let parsedResponse = JSON.parse(response);
                console.log(parsedResponse)
                let rows = parsedResponse['rows'];

                rows.forEach(row => {
                    createTableRow(row['inc'], row['hook_txnId'],
                        row['hook_date'], row['hook_sum'],
                        row['hook_personId'], row['dkcp_sum'],
                        row['dkcp_transact'])
                })

                if (isFirstGenerate) {

                    let wallets = parsedResponse['wallets'];

                    wallets.forEach(wallet => {

                        let option = document.createElement('option');
                        option.innerText = wallet['hook_personId'];
                        option.value = wallet['hook_personId'];

                        formControl[2].appendChild(option)


                    })

                }

                if (isRequiredReCreatePages) {
                    let count = parsedResponse['count'];
                    let pages = document.getElementById('pages-container');

                    generatePages(pages, count);
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

    function createTableRow(code, transact, date, income, wallet, transfer, transactDKCP) {
        let tr = document.createElement('tr');

        let tableDataCode = document.createElement('th');
        let tableDataTransaction = document.createElement('td');
        let tableDataDate = document.createElement('td');
        let tableDataIncome = document.createElement('td');
        let tableDataWallet = document.createElement('td');
        let tableDataTransfer = document.createElement('td');
        let tableDataTransactionDKCP = document.createElement('td');

        tableDataCode.innerText = code;
        tableDataTransaction.innerText = transact;
        tableDataDate.innerText = date;
        tableDataIncome.innerText = income;
        tableDataWallet.innerText = wallet;
        tableDataTransfer.innerText = transfer;
        tableDataTransactionDKCP.innerText = transactDKCP;

        tr.appendChild(tableDataCode);
        tr.appendChild(tableDataTransaction);
        tr.appendChild(tableDataDate);
        tr.appendChild(tableDataIncome);
        tr.appendChild(tableDataWallet);
        tr.appendChild(tableDataTransfer);
        tr.appendChild(tableDataTransactionDKCP);

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

    function generatePages(container, count) {

        let numberOfPage = Math.ceil(count / 50);

        let moveBack = generateMovePage(true);
        let moveForward = generateMovePage(false);

        container.appendChild(moveBack)

        for (let pageNumber = 0; pageNumber < numberOfPage; pageNumber++) {

            let page = generatePage(pageNumber);
            container.appendChild(page);
        }

        container.appendChild(moveForward)

        console.log(count)
    }

    function generatePage(number) {

        let page = document.createElement('li');
        let buttonPage = document.createElement('button');


        page.classList = "page-item";
        buttonPage.classList = "page-link";

        buttonPage.innerText = number + 1;

        let arrayPages = document.getElementsByClassName('page-link')

        arrayPages[0].disabled = true;
        buttonPage.disabled = number + 1 == 1 ? true : false;

        buttonPage.addEventListener('click', function() {

            document.getElementById('table_body').innerHTML = "";

            let lastPage = document.getElementsByClassName('page-link')[currentPage];
            lastPage.disabled = false;

            currentPage = number + 1;

            buttonPage.disabled = true;

            generateTableRows(false, number + 1)

            let arrayPages = document.getElementsByClassName('page-link')

            arrayPages[0].disabled = currentPage == 1 ?
                true : false;
            arrayPages[arrayPages.length - 1].disabled = currentPage == arrayPages.length - 1 ?
                true : false


            console.log(currentPage)
        })

        page.appendChild(buttonPage);

        return page;
    }

    function generateMovePage(isBack) {

        let page = document.createElement('li');
        let buttonPage = document.createElement('button');

        page.classList = "page-item";
        buttonPage.classList = "page-link";

        if (isBack) {

            buttonPage.innerHTML = "Назад"

            buttonPage.addEventListener('click', function() {

                let arrayOfAllPages = document.getElementsByClassName('page-link');

                console.log(currentPage)
                if (currentPage == 1) {
                    return false
                }

                if (arrayOfAllPages[arrayOfAllPages.length-2].disabled)
                    arrayOfAllPages[arrayOfAllPages.length-2].disabled = false;


                document.getElementById('table_body').innerHTML = "";
                generateTableRows(false, currentPage)
                arrayOfAllPages[currentPage].disabled = false;
                currentPage--;
                arrayOfAllPages[currentPage].disabled = true;

            })

        } else {

            buttonPage.innerHTML = "Вперёд"

            buttonPage.addEventListener('click', function() {

                let arrayOfAllPages = document.getElementsByClassName('page-link');

                if (currentPage == arrayOfAllPages.length-2) {
                    return false
                }

                if (arrayOfAllPages[0].disabled)
                    arrayOfAllPages[0].disabled = false;


                document.getElementById('table_body').innerHTML = "";
                generateTableRows(false, currentPage)
                arrayOfAllPages[currentPage].disabled = false;
                currentPage++;
                arrayOfAllPages[currentPage].disabled = true;


            })
        }

        page.appendChild(buttonPage);
        return page;
    }

    function convertDate(date) {
        let arrayDate = date.split('.');

        return `${arrayDate[2]}-${arrayDate[1]}-${arrayDate[0]}`;
    }

    document.getElementById("reload_btn").addEventListener('click', function() {

        document.getElementById('table_body').innerHTML = "";
        document.getElementById('pages-container').innerHTML = "";
        generateTableRows(false, 1, true);

    })

  </script>
</body>

</html>