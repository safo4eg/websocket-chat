let inputMessage = document.getElementById('input-message');
inputMessage.addEventListener('input', interactivity.textareaChanges);


let socket = new WebSocket("ws://127.0.0.1:4545");

socket.onopen = function(event) {
    console.log('Соединение установлено');
};

socket.onerror = function(event) {
    console.log('ошибочка');
}

socket.onmessage = function(event) {
    console.log(event.data);
};