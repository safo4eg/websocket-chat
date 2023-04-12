let inputMessage = document.getElementById('input-message');
let messagesWrapper = document.getElementById('messages-wrapper');
inputMessage.addEventListener('input', interactivityModule.textareaChanges);


/*MODULE*/
if(document.getElementById('modal') !== null) {
    /*TABS*/
    let authForm = document.getElementById('auth-form');
    let loginTab = document.getElementById('login-tab');
    let registerTab = document.getElementById('register-tab');
    loginTab.addEventListener('click', function(event) {
        if(!this.classList.contains('active')) {
            interactivityModule.createAuthForm('login', authForm);
            this.classList.add('active');
            registerTab.classList.remove('active');
        }
    });

    registerTab.addEventListener('click', function(event) {
        if(!this.classList.contains('active')) {
            interactivityModule.createAuthForm('register', authForm);
            this.classList.add('active');
            loginTab.classList.remove('active');
        }
    });

    /*AUTH*/
    let authBtn = document.getElementById('auth-btn');
    authBtn.addEventListener('click', function(event) {
        event.preventDefault();
        let action = authForm.querySelector('#auth-action').value;
        let formData = new FormData(authForm);
        connectionModule.sendAuth(formData, 'handlers/auth.php').then(response => {
            response.text().then(data => {
                if(response.status === 200) {
                    console.log(JSON.parse(data));
                } else if(response.status >= 400) {
                    console.log(JSON.parse(data));
                }
            })
        });
    });
}

// inputMessage.addEventListener('keypress', function(event) {
//     if(event.code === 'Enter') {
//         let message = this.value;
//         if(message !== '') {
//             socket.send(message);
//             this.value = '';
//         }
//     }
// });


// let socket = new WebSocket("ws://127.0.0.1:4545");
//
// socket.onopen = function(event) {
//     console.log('Соединение установлено');
// };
//
// socket.onerror = function(event) {
//     console.log('ошибочка');
// }
//
// socket.onmessage = function(event) {
//     let message = event.data;
//     interactivityModule.createMessage(messagesWrapper, 2, message, (new Date()).getTime());
// };