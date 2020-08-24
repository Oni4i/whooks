<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/pages/main/add/ajax.php";
?>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/floating-labels/">
    <!-- Bootstrap core CSS -->
    <link href="./../../../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="index.css" rel="stylesheet">
    </head>
    <body>
    <div class="container">
        <div class="card formCard">
            <form class="form-signin">

                <div class="form-group">
                    <label for="exampleInputPhone">Номер телефона</label>
                    <input type="phone" id="inputPhone" class="form-control" placeholder="+7 (999) 999 23 23" data-isValid="0" value="79999276993" required autofocus>
                </div>

                <div class="form-group">
                    <label for="exampleInputToken">Токен кошелька</label>
                    <input type="token" id="inputToken" class="form-control" placeholder="" required autofocus>
                </div>

                <div class="form-group">
                    <label for="exampleInputToken">Срок действия токена</label>
                    <input type="date" id="inputDate" class="form-control" placeholder="дд.мм.гггг" data-isValid="0" required autofocus>
                </div>

                <div class="form-group">
                    <label for="exampleFormControlSelect1">Аккаунт процессинга</label>
                    <select class="form-control" id="exampleFormControlSelect1">
                    </select>
                </div>

                <div class="form-group">
                    <label for="exampleFormControlSelect2">Токенизированная карта</label>
                    <select class="form-control" id="exampleFormControlSelect2">
                    </select>
                </div>

                <button class="btn btn-lg btn-primary btn-block" id="submit" type="submit" onsubmit="return false;">Отправить</button>
                <p class="mt-5 mb-3 text-muted text-center">&copy; 2020</p>
            </form>
        </div>
    </div>

        <?php
/*
        $ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks?hookType=1&txnType=2&param=https%3A%2F%2Fgate-dev.paypoint.pro%2Fsystems%2Fqiwi_web_hook%2Fcallback.php");

        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //4b8e4a4c1d95da3236c3ea5ffb113e36 token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
            'ContentType: application/json; charset=UTF-8'));
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);


        $ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks/1407c17f-565c-4e2b-9490-ff03618778bc");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
            'ContentType: application/json; charset=UTF-8'));

        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);


        $ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks/755e56e4-f53d-42b5-a23f-7936729906b6/key");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
            'ContentType: application/json; charset=UTF-8'));
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        */


        ?>



<script>
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

            getSecretKey(`get_secret_key&token=${token}&hook_id=${unescape(encodeURIComponent(hookId))}`, response => {
                try {
                    secretKey = JSON.parse(response)['key']
                } catch (e) {}

                console.log(response)
            })
                .then(() => {
                let phoneNumber = phone[0] == "+" ? phone.substring(1) : phone;
                saveWebHook(`save_web_hook&code=${lastSelectedCode}&phone=${phoneNumber}&wallet_token=${token}&date=${date}&account=${lastSelectedCode}&card_token=${lastSelectedCardToken}&hook_id=${parsedResponse['hookId']}&secret_key=${secretKey}`, function(response) {

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


//PHONE PROPERTY
document.getElementById('inputPhone').addEventListener('focus', _ => {
    // Если там ничего нет или есть, но левое
    if(!/^\+\d*$/.test(document.getElementById('inputPhone').value))
        // То вставляем знак плюса как значение
        document.getElementById('inputPhone').value = '+';
});
document.getElementById('inputPhone').addEventListener('keypress', e => {
    // Отменяем ввод не цифр
    if(!/\d/.test(e.key))
        e.preventDefault();
});

document.getElementById('inputPhone').addEventListener('blur', e => {
    let val = document.getElementById('inputPhone').value.toString()
    let len = val.length

    if (val[0] == '+' && len == 12 || val[0] != '+' && len == 11) {
        document.getElementById('inputPhone').style.border = "1px solid #ced4da";
        document.getElementById('inputPhone').dataset.isValid = "1"
    } else {
        document.getElementById('inputPhone').style.borderColor = "red";
        document.getElementById('inputPhone').dataset.isValid = "0"
    }
})





</script>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/footer.php";
