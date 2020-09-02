
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

let account_list = document.getElementById("exampleFormControlSelect1");
let card_list = document.getElementById("exampleFormControlSelect2");


let parsedAccounts;

let lastSelectedCode;
//let lastSelectedCardToken;
let hookId;
let secretKey;

getAjaxResponse("get_accounts", insertAccounts, account_list, ["uid", "name"], function (response) {
    try {
        parsedAccounts = JSON.parse(response);
    } catch (e) {
        parsedAccounts = []
    }
})
    .then(() => {
        let uid = String(account_list.value).split(" ")[0]
        let requiredArray = parsedAccounts.filter((arr) => arr['uid'] == uid)[0]
        let login = requiredArray['login'];
        let password = requiredArray['password'];
        lastSelectedCode = requiredArray['code'];

        getAjaxResponse(`get_cards&login=${login}&password=${password}`, processingCards, card_list, "ITSCOUNTER", function (response) {
            //lastSelectedCardToken = card_list.value
        })


        account_list.addEventListener("change", function() {
            let uid = String(account_list.value).split(" ")[0];
            let requiredArray = parsedAccounts.filter((arr) => arr['uid'] == uid)[0];
            let login = requiredArray['login'];
            let password = requiredArray['password'];
            lastSelectedCode = requiredArray['code'];

            getAjaxResponse(`get_cards&login=${login}&password=${password}`, processingCards, card_list, "ITSCOUNTER", function (response) {
                //lastSelectedCardToken = card_list.value
            })
        })
    })

function getAjaxResponse(request, fillHtmlField = null, element = null, fieldForOut = null, callback = null) {
    //Get response
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
            if (fillHtmlField != null)
                fillHtmlField(element, response, fieldForOut)
            return response;
        })
        .then(response => {
            if (callback != null) {
                callback(response);
            }
        })
        .catch((e) => console.log(e))
}

function insertAccounts(element, json, fieldForOut) {
    element.innerHTML = "";
    console.log(JSON.parse(json))

    if (JSON.parse(json) != null && JSON.parse(json).length > 0) {
        //fill all required fields
        try {
            JSON.parse(json).forEach((arr) => {

                let selectTemplate = document.createElement("option");
                let htmlText = "";

                if (Array.isArray(fieldForOut) || fieldForOut != "ITSCOUNTER") {

                    if (Array.isArray(fieldForOut)) {

                        fieldForOut.forEach((field) => {

                            htmlText += arr[field] + " ";

                        })
                    } else {

                        htmlText = arr[fieldForOut];

                    }

                } else {

                    if (fieldForOut == "ITSCOUNTER") {

                        htmlText = arr

                    } else {

                    }
                }
                selectTemplate.innerHTML = htmlText
                element.appendChild(selectTemplate);

            });
        } catch (e) {

            let selectTemplate = document.createElement("option");
            selectTemplate.innerHTML = "Пусто";
            element.appendChild(selectTemplate);
        }

    } else {
        //if response count less than 1, then option will be empty
        let selectTemplate = document.createElement("option");
        selectTemplate.innerHTML = "Пусто";
        element.appendChild(selectTemplate);
    }
}

function processingCards(element, json) {

    element.innerHTML = ""

    try {
        let parsedJson = JSON.parse(json);

        let cardsArray = parsedJson['cards']
        let tokensArray = parsedJson['tokens']

        if (cardsArray[0] == "" || tokensArray[0] == "") {
            throw "Отсутвуют карты"
        }

        insertCards(element, cardsArray, tokensArray);
    } catch (e) {
        let template = document.createElement('option')
        template.innerHTML = "Пусто"
        element.appendChild(template)
    }

}

function insertCards(element, arrOfCards, arrOfTokens) {
    if (arrOfCards.length > 0) {

        for (let i = 0; i < arrOfCards.length; i++) {

            let template = document.createElement('option')
            template.innerHTML = arrOfCards[i]
            template.value = arrOfTokens[i]
            element.appendChild(template)

        }

    }
}




document.getElementById('submit').addEventListener('click', function (event) {

    let phone = document.getElementById('inputPhone').value
    let token = document.getElementById('inputToken').value
    let date = document.getElementById('inputDate').value
    let lastSelectedCardToken = card_list.value

    if (!isValidForm(document.getElementById('inputPhone'), document.getElementById('inputToken'), document.getElementById('inputDate'), document.getElementById('exampleFormControlSelect2'))) {
        return
    }

    getAjaxResponse(`create_web_hook&token=${token}`, null, null, null, function (response) {


        console.log(response)
        let parsedResponse = JSON.parse(response)


        if (parsedResponse == null || !parsedResponse) {

            alert("QIWI вернул пустой ответ. Возможные причины:\n-Неверный токен")

        } else if (parsedResponse["errorCode"] == "hook.already.exists") {

            alert("Хук уже существует")

        } else if (parsedResponse["hookId"] != "") {

            hookId = parsedResponse['hookId']
            console.log(hookId)

            getSecretKey(`get_secret_key&token=${token}&hook_id=${encodeURI(hookId)}`, response => {
                try {
                    secretKey = JSON.parse(response)['key']
                } catch (e) {}

            })
                .then(() => {

                    let phoneNumber = phone.split('').filter(x => Number(x) || x === '0').join('');
                    secretKey = encodeURIComponent(secretKey.toString());
                    saveWebHook(`save_web_hook&code=${lastSelectedCode}&phone=${encodeURIComponent(phoneNumber)}&wallet_token=${encodeURIComponent(token)}&date=${date}&account=${encodeURIComponent(lastSelectedCode)}&card_token=${encodeURIComponent(lastSelectedCardToken)}&hook_id=${encodeURIComponent(parsedResponse['hookId'])}&secret_key=${secretKey}`, function(response) {

                        document.getElementById('inputPhone').value = ""
                        document.getElementById('inputToken').value = ""
                        document.getElementById('inputDate').value = ""


                        alert("Веб хук создан")
                    });
                })
        }
    })
    event.preventDefault()
    return false
})


document.getElementById('submit').addEventListener('submit', function (event) {
    event.preventDefault();
    return false
})

function getSecretKey(request, callback) {
    return getAjaxResponse(request, null, null, null, callback)
}

function saveWebHook(request, callback) {
    getAjaxResponse(request, null, null, null, callback);
}

function isValidForm(phone, token, date, card) {
    if (phone.dataset.isValid == "0") {
        alert("Невалидный номер телефона")
        return false
    } else if (token.value.toString().length == 0) {
        alert("Невалидный токен")
        return false
    } else if (date.dataset.isValid == "0") {
        alert("Невалидная дата")
        return false
    } else if (card.value.toString() == "Пусто") {
        alert("Невалидная карта")
        return false
    }
    return true
}

$(document).ready(function(){
    $("#inputPhone").mask("+7 (999) 999-99-99");
});

function encode_utf8(s) {
    return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
    return decodeURIComponent(escape(s));
}