;(function() {
    let connectionModule = {};

    connectionModule.sendAuth = async function(payload, url) {
        let response = await fetch(url, {
           method: "POST",
           body: payload
        });
        return response;
    }

    window.connectionModule = connectionModule;
})()