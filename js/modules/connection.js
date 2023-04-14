;(function() {
    let connectionModule = {};

    connectionModule.sendMessage = function(socket, payload) {
        let frameText = createStructureFrame(
            payload['type'],
            payload['userHash'],
            payload['message'],
            payload['dialogue']
        )
        socket.send(frameText);
    }

    connectionModule.startWebsocket = function(socketServer, payload) {
        let socket = new WebSocket(socketServer);

        socket.onopen = function(event) {
            socket.send(createStructureFrame(
                payload['type'],
                payload['userHash'],
                null,
                payload['dialogue']
                )
            );
        };

        socket.onerror = function(event) {
            console.log('ошибочка');
        }

        return socket;
    }

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

    function createStructureFrame(type, userHash, message=null, dialogue=null) {
        let obj = {
            type: type,
            userHash: userHash
        }
        if(message) obj.message = message;
        if(dialogue !== null) obj.dialogue = dialogue;
        return JSON.stringify(obj);
    }

    window.connectionModule = connectionModule;
})()