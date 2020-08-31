generateTableRows()
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


let submit = document.getElementById('submit');
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