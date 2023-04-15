;(function() {
    let interactivityModule = {};

    interactivityModule.setSettings = function (userData, sUsername) {
        sUsername.textContent = userData['username'];
    }

    interactivityModule.textareaChanges = function(event) {
        let height = this.offsetHeight;
        let kf = height/20;
        let newHeightUp = height + 20;
        let newHeightDown = height - 20;
        if(this.value.length >= kf*66) this.style.height = `${newHeightUp}px`;
        else if(this.value.length < (kf - 1)*66 && kf !== 1) this.style.height = `${newHeightDown}px`;
    }

    interactivityModule.createServerMessage = function(messagesWrapper, message, id= -1) {
        let serverMessage = document.createElement('DIV');
        serverMessage.classList.add('server-message');

        let span = document.createElement('SPAN');
        span.textContent = message;

        let hiddenInput = document.createElement('INPUT');
        hiddenInput.type = 'hidden';
        hiddenInput.value = id;

        serverMessage.append(hiddenInput);
        serverMessage.append(span);
        messagesWrapper.append(serverMessage);
    }

    interactivityModule.createMessage = function(messagesWrapper,id,  username, message, time) {
        let lastMessage = messagesWrapper.lastElementChild;
        let lastUserId = +lastMessage.querySelector("input[type='hidden']").value;
        let newMessage = document.createElement('DIV');
        newMessage.classList.add('message');
        newMessage.classList.add('same-user');

        let hiddenInput = document.createElement('INPUT');
        hiddenInput.type = 'hidden';
        hiddenInput.value = id;

        let textWrapper = document.createElement('DIV');
        textWrapper.classList.add('item');
        textWrapper.classList.add('text-wrapper');

        let text = document.createElement('DIV');
        text.classList.add('text');
        text.textContent = message;

        let timeBlock = document.createElement('DIV');
        timeBlock.classList.add('item');
        timeBlock.classList.add('time');
        timeBlock.textContent = convertTime(time);

        newMessage.append(hiddenInput);

        if(lastUserId != id) {
            let imgWrapper = document.createElement('DIV');
            imgWrapper.classList.add('item');
            imgWrapper.classList.add('img-wrapper');

            let mUsername = document.createElement('DIV');
            mUsername.textContent = username;
            mUsername.id = 'mUsername';
            textWrapper.append(mUsername);
            let div = document.createElement('DIV');
            textWrapper.append(div);

            newMessage.append(imgWrapper);
            newMessage.classList.remove('same-user');
        }

        textWrapper.append(text);
        newMessage.append(textWrapper);
        newMessage.append(timeBlock);

        messagesWrapper.append(newMessage);
    }


    interactivityModule.createAuthForm = function(action, form) {
        deleteFormItems(form);
        let actionInput = document.createElement('INPUT');
        actionInput.id = 'auth-action';
        actionInput.type = 'hidden';
        actionInput.value = 'login';
        actionInput.name = 'action';
        let username = document.createElement('INPUT');
        username.placeholder = 'username';
        username.name = 'username';
        let password = document.createElement('INPUT');
        password.placeholder = 'password';
        password.type = 'password';
        password.name = 'password';

        let btn = form.querySelector('#auth-btn');
        btn.textContent = 'Вход';

        if(action === 'register') {
            let confirm = document.createElement('INPUT');
            confirm.placeholder = 'confirm password';
            confirm.type = 'password';
            confirm.name = 'confirm';
            form.prepend(confirm);
            actionInput.value = 'register';
            btn.textContent = 'Регистрация';
        }

        form.prepend(password);
        form.prepend(username);
        form.prepend(actionInput);
    }

     function deleteFormItems(form) {
        let formChildren = Array.from(form.children);
        if(formChildren.length !== 0) {
            for(let item of formChildren) {
                if(item.id !== 'auth-btn') form.removeChild(item);
            }
        }
    }

    function convertTime(timestamp) {
        function addZero(num) {
            if(num >= 0 && num <= 9) return '0' + num;
            else return num;
        }

        let date = new Date(timestamp);

        let day = addZero(date.getDate());
        let month = addZero(date.getMonth());
        let year = date.getFullYear();

        let hours = date.getHours();
        let minutes = date.getMinutes();

        return `${day}.${month}.${year} ${hours}:${minutes}`;
    }

    window.interactivityModule = interactivityModule;
})();