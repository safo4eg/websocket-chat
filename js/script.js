let modal = document.getElementById('modal');
let chat = document.getElementById('chat');
let inputMessage = document.getElementById('input-message');
let messagesWrapper = document.getElementById('messages-wrapper');

if(!connectionModule.getData('user')) {
    modal.classList.remove('hide');
    chat.classList.add('hide');
} else {
    connectionModule.startWebsocket('ws://127.0.0.1:4545', inputMessage);
}

/* INTERACTIVITY TEXTAREA */
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
                    connectionModule.saveData('user', data);
                    modal.classList.add('hide');
                    chat.classList.remove('hide');
                    connectionModule.startWebsocket('ws://127.0.0.1:4545', inputMessage);
                    // коннектимся
                } else if(response.status >= 400) {
                    console.log(JSON.parse(data)); // вывод ошибок для форм
                }
            })
        });
    });
}