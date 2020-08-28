
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
                        arr['account_balance'], arr['next_operation'],
                        arr['hook_txnId'], arr['dkcp_result_text'])
                })
            }
        } catch (e) {

            console.log(e)
        }
    })
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
    tableDataButtons.style.textAlign ="center";

    btnRepeat.addEventListener('click', function() {

        let spinner = createSpinner();
        btnRepeat.hidden = true;
        btnArchive.hidden = true;
        tableDataButtons.appendChild(spinner);

        getAjaxRequest("repeat_operation&id=" + code, function (response) {

            btnRepeat.hidden = false;
            btnArchive.hidden = false
            spinner.remove();

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
    tr.appendChild(tableDataHookTxnId);
    tr.appendChild(tableDataDate);
    tr.appendChild(tableDataIncome);
    tr.appendChild(tableDataWallet);
    tr.appendChild(tableDataBalance);
    tr.appendChild(tableDataCodeDkcp);
    tr.appendChild(tableDataErorDkcp);
    tr.appendChild(tableDataButtons);

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

