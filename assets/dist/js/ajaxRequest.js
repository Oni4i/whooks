function getAjaxRequest(request, callback) {

    return fetch("ajax.php?" + request,
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