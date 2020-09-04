function getAjaxRequest(request, callback) {
    return fetch("ajax.php?" + request + `&user=${getCookie('user')}`,
        {
            method: "GET",
            headers:{"content-type":"application/json"},
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

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}