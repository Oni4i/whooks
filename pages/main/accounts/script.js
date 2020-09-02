generateTableRows()


let submit = document.getElementById('submit');
let loginField = document.getElementById('inputLogin');
let passwordField = document.getElementById('inputPassword');
let keytsField = document.getElementById('inputKeyt');

function generateTableRows() {

    getAjaxRequest("get_accounts_processing", function(response) {

        try {

            let parsedResponse = JSON.parse(response);

            if (parsedResponse != null && parsedResponse.length > 0) {

                parsedResponse.forEach(arr => {

                    createTableRow(
                        arr['code'], arr['uid'],
                        arr['name'], arr['login'],
                        arr['keyt']
                    )
                })
            }
        } catch (e) {

            console.log(e)
        }
    })
}


function createTableRow(code, uid, name, login, keyt) {
    let tr = document.createElement('tr');
    let tableDataCode = document.createElement('td');
    let tableDataUId = document.createElement('td');
    let tableDataName = document.createElement('td');
    let tableDataLogin = document.createElement('td');
    let tableDataKeyt = document.createElement('td');

    tableDataCode.innerText = code;
    tableDataUId.innerText = uid;
    tableDataName.innerText = name;
    tableDataLogin.innerText = login;
    tableDataKeyt.innerText = keyt;

    tableDataCode.scope = 'row';

    tr.appendChild(tableDataCode);
    tr.appendChild(tableDataUId);
    tr.appendChild(tableDataName);
    tr.appendChild(tableDataLogin);
    tr.appendChild(tableDataKeyt);

    document.getElementById('table_body').appendChild(tr);
}



function getKeyt(login, password) {

    getAjaxRequest(`get_keyt&login=${login}&password=${password}`, function(response) {
        generateKeyts(response, document.getElementById('inputKeyt'));
        unlockButton();
    })
}

function generateKeyts(data, element) {

    element.innerHTML = "";

    let resultArray = parseKeyt(data);

    if (resultArray['error'] === 0) {
        for (let row = 0; row < resultArray['count']; row++) {
            let option = createOptionKeyt(resultArray['keyt'][row], resultArray['name'][row]);
            element.appendChild(option);
        }
    } else {
        let option = document.createElement('option');
        option.innerText = 'Пусто';
        option.value = '0';
        element.appendChild(option);
    }

}

function parseKeyt(data) {

    let resultData = {};

    try {
        let parsedJson = JSON.parse(data);

        let keytArray = parsedJson['keyt'].map(keyt => keyt['0']);
        let nameArray = parsedJson['name'].map(name => name['0']);

        if (keytArray[0] == "")
            throw "Отсутствуют ключи"

        submit.disabled = false;

        resultData['keyt'] = keytArray;
        resultData['name'] = nameArray;
        resultData['count'] = keytArray.length;
        resultData['error'] = 0;
    }
    catch (e) {
        console.log(e.toString());
        resultData['error'] = 1;
    }

    return resultData;
}

function createOptionKeyt(keyt, name) {

    let option = document.createElement('option');
    option.innerText = `${keyt}`;

    if (name)
        option.innerText += ` ${name}`;

    option.value = keyt;

    return option;
}



function unlockButton() {
    if (keytsField.value != '0')
        submit.disabled = false;
    else
        submit.disabled = true;
}



submit.addEventListener('click', function () {

    let uid = document.getElementById('inputUId').value;
    let name = document.getElementById('inputName').value;
    let login = document.getElementById('inputLogin').value;
    let password = document.getElementById('inputPassword').value;
    let keyt = document.getElementById('inputKeyt').value;

    uid = encodeURIComponent(uid);
    name = encodeURIComponent(name);
    login = encodeURIComponent(login);
    password = encodeURIComponent(password);
    keyt = encodeURIComponent(keyt);

    getAjaxRequest(`save_account&uid=${uid}&name=${name}&login=${login}&password=${password}&keyt=${keyt}`, function(response) {

        try {

            let parsedResponse = JSON.parse(response);

            if (parsedResponse != null) {
                console.log(parsedResponse)
                if (parsedResponse['response'] == '200')
                    alert('Успешная запись');
                else if (parsedResponse['response'] == '1')
                    alert('Неудачная запись');
                else
                    alert('Неизвестная ошибка');
            }
        } catch (e) {

            console.log(e)
        }

    })
})

loginField.addEventListener('change', function() {

    if (loginField.value && passwordField.value){
        let login = decodeURIComponent(loginField.value);
        let password = decodeURIComponent(passwordField.value);
        getKeyt(login, password);
    }

})

passwordField.addEventListener('change', function() {

    if (loginField.value && passwordField.value){
        let login = decodeURIComponent(loginField.value);
        let password = decodeURIComponent(passwordField.value);
        getKeyt(login, password);
    }

})

keytsField.addEventListener('change', function() {

    if (keytsField.value != '0')
        submit.disabled = false;
    else
        submit.disabled = true;
})