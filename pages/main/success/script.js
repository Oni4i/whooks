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
let currentPage = {
    page: 1,

    get Page() {
        return this.page;
    },
    set Page(value) {

        let arrayOfPages = document.getElementsByClassName('page-item');
        arrayOfPages[this.page].classList = 'page-item';

        this.page = value;

        arrayOfPages[this.page].classList = 'page-item active';
    }
};

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
    document.getElementsByClassName('page-item')[1].classList.add('active');
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

        let lastPage = document.getElementsByClassName('page-link')[currentPage.Page];
        lastPage.disabled = false;

        currentPage.Page = number + 1;

        buttonPage.disabled = true;

        generateTableRows(false, number + 1)

        let arrayPages = document.getElementsByClassName('page-link')

        arrayPages[0].disabled = currentPage.Page == 1 ?
            true : false;
        arrayPages[arrayPages.length - 1].disabled = currentPage.Page == arrayPages.length - 1 ?
            true : false

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


            if (currentPage.Page == 1) {
                return false
            }

            if (arrayOfAllPages[arrayOfAllPages.length-2].disabled)
                arrayOfAllPages[arrayOfAllPages.length-2].disabled = false;

            document.getElementById('table_body').innerHTML = "";
            generateTableRows(false, currentPage.Page)
            arrayOfAllPages[currentPage.Page].disabled = false;
            currentPage.Page = currentPage.Page - 1;
            arrayOfAllPages[currentPage.Page].disabled = true;
        })

    } else {

        buttonPage.innerHTML = "Вперёд"

        buttonPage.addEventListener('click', function() {

            let arrayOfAllPages = document.getElementsByClassName('page-link');

            if (currentPage.Page == arrayOfAllPages.length-2) {
                return false
            }

            if (arrayOfAllPages[0].disabled)
                arrayOfAllPages[0].disabled = false;

            document.getElementById('table_body').innerHTML = "";
            generateTableRows(false, currentPage.Page)
            arrayOfAllPages[currentPage.Page].disabled = false;
            currentPage.Page = currentPage.Page + 1;
            arrayOfAllPages[currentPage.Page].disabled = true;
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
document.getElementById('exitBtn').addEventListener('click', function() {

    let pathArray = window.location.pathname.split('/');
    let indexCabinet = pathArray.indexOf('cabinet');

    pathArray = pathArray.slice(0, indexCabinet + 1);
    let pathExit = pathArray.join('/') + '/templates/exit.php';

    fetch(pathExit, {
        method: "GET",
        headers:{"content-type":"application/json"}
    }).then(response => console.log(response));

})