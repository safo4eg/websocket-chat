;(function() {
    let connectionModule = {};

    connectionModule.startWebsocket = function(socketServer, inputMessage) {
        let socket = new WebSocket(socketServer);

        inputMessage.addEventListener('keypress', function(event) {
            if(event.code === 'Enter') {
                let message = this.value;
                if(message !== '') {
                    socket.send(message);
                    this.value = '';
                }
            }
        });

        socket.onopen = function(event) {
            console.log('Соединение установлено');
        };

        socket.onerror = function(event) {
            console.log('ошибочка');
        }

        socket.onmessage = function(event) {
            let message = event.data;
            interactivityModule.createMessage(messagesWrapper, 2, message, (new Date()).getTime());
        };

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

    window.connectionModule = connectionModule;
})()