;(function() {
    let connectionModule = {};

    connectionModule.sendAuth = async function(payload, url) {
        let response = await fetch(url, {
           method: "POST",
           body: payload
        });
        return response;
    }

    connectionModule.saveData = function($key, $dataJson) {
        localStorage.setItem($key, $dataJson);
    }

    connectionModule.getData = function($key) {
        return JSON.parse(localStorage.getItem($key));
    }

    window.connectionModule = connectionModule;
})()