let socket = null;
let modal = document.getElementById('modal');
let chat = document.getElementById('chat');
let inputMessage = document.getElementById('input-message');
let messagesWrapper = document.getElementById('messages-wrapper');
let userSettings = document.getElementById('userSettings');
let userDialogues = document.getElementById('userDialogues');

let dialogueWindow = document.getElementById('dialogueWindow');
let emptyDialogue = document.getElementById('emptyDialogue');
let generalDialogue = document.getElementById('generalDialogue');

/*ACTIONS ELEMS*/
let userSettingsToggle = document.getElementById('userSettingsToggle');
let userDialoguesToggle = document.getElementById('userDialoguesToggle');
let logout = document.getElementById('logout');
let sUsername = document.getElementById('sUsername');

/*CHECK USER TOKEN AND CURRENT DIALOGUE*/
let userData = connectionModule.getData('user');
let currentDialogue = connectionModule.getData('currentDialogue');
if(!userData) {
    modal.classList.remove('hide');
    chat.classList.add('hide');
} else {
    interactivityModule.setSettings(userData, sUsername);
    if(currentDialogue != null) {
        if(dialogueWindow.classList.contains('hide')) {
            dialogueWindow.classList.remove('hide');
            emptyDialogue.classList.add('hide');
        }
    }
}

/*SWITCH DIALOGUE*/
generalDialogue.onclick = function(event) {
    if(dialogueWindow.classList.contains('hide')) {
        dialogueWindow.classList.remove('hide');
        emptyDialogue.classList.add('hide');
        connectionModule.saveData('currentDialogue', JSON.stringify(1));
        currentDialogue = connectionModule.getData('currentDialogue');


        socket = connectionModule.startWebsocket('ws://127.0.0.1:4545', {
            type: 'connection',
            userHash: userData['userHash'],
            dialogueId: currentDialogue
        });

        inputMessage.addEventListener('keypress', function(event) {
            if(event.code === 'Enter') {
                let message = this.value;
                if(message !== '') {
                    connectionModule.sendMessage(socket,{
                        type: 'message',
                        userHash: userData['userHash'],
                        dialogueId: currentDialogue,
                        message: message
                    });
                    this.value = '';
                }
            }
        });

        socket.onmessage = function(event) {
            let data = JSON.parse(event.data);

            if(data['type'] === 'connection') {
                interactivityModule.createServerMessage(messagesWrapper, data['message']);
            } else if(data['type'] === 'message') {
                interactivityModule.createMessage(messagesWrapper,
                    data['id'],
                    data['username'],
                    data['message'],
                    new Date().getTime()
                )
                console.log(data);
            }
        }
    }
}

/*ACTIONS LEFT TOP*/
userSettingsToggle.onclick = function(event) {
    if(userSettings.classList.contains('hide')) {
        userSettings.classList.remove('hide');
        userDialogues.classList.add('hide');
    }
}

userDialoguesToggle.onclick = function(event) {
    if(userDialogues.classList.contains('hide')) {
        userDialogues.classList.remove('hide');
        userSettings.classList.add('hide');
    }
}

logout.onclick = function(event) {
    connectionModule.saveData('user', null);
    connectionModule.saveData('currentDialogue', null);
    chat.classList.add('hide');
    modal.classList.remove('hide');
    // так же будет диссконнект с вебсокета
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
                    userData = connectionModule.getData('user');
                    modal.classList.add('hide');
                    chat.classList.remove('hide');

                    emptyDialogue.classList.remove('hide');
                    dialogueWindow.classList.add('hide');
                    interactivityModule.setSettings(userData, sUsername);
                } else if(response.status >= 400) {
                    console.log(JSON.parse(data)); // вывод ошибок для форм
                }
            })
        });
    });
}