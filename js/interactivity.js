;(function() {
    let interactivityModule = {};

    interactivityModule.textareaChanges = function(event) {
        let height = this.offsetHeight;
        let kf = height/20;
        let newHeightUp = height + 20;
        let newHeightDown = height - 20;
        if(this.value.length >= kf*66) this.style.height = `${newHeightUp}px`;
        else if(this.value.length < (kf - 1)*66 && kf !== 1) this.style.height = `${newHeightDown}px`;
    }

    interactivityModule.createMessage = function(messagesWrapper, id, message, time) {
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
        textWrapper.append(text);

        let timeBlock = document.createElement('DIV');
        timeBlock.classList.add('item');
        timeBlock.classList.add('time');
        timeBlock.textContent = convertTime(time);

        newMessage.append(hiddenInput);

        if(lastUserId != id) {
            let imgWrapper = document.createElement('DIV');
            imgWrapper.classList.add('item');
            imgWrapper.classList.add('img-wrapper');

            newMessage.append(imgWrapper);
            newMessage.classList.remove('same-user');
        }

        newMessage.append(textWrapper);
        newMessage.append(timeBlock);

        messagesWrapper.append(newMessage);
    }


    interactivityModule.createAuthForm = function(action, form) {
        deleteFormItems(form);
        let username = document.createElement('INPUT');
        username.placeholder = 'username';
        let password = document.createElement('INPUT');
        password.placeholder = 'password';
        password.type = 'password';
        let btn = document.createElement('BUTTON');
        btn.id = 'auth-btn';
        btn.type = 'button';
        btn.textContent = 'Войти';

        form.append(username);
        form.append(password);

        if(action === 'register') {
            let confirm = document.createElement('INPUT');
            confirm.placeholder = 'confirm password';
            confirm.type = 'password';
            form.append(confirm);
            btn.textContent = 'Зарегистрироваться';
        }

        form.append(btn);
    }

     function deleteFormItems(form) {
        let formChildren = Array.from(form.children);
        if(formChildren.length !== 0) {
            for(let item of formChildren) {
                form.removeChild(item);
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

    window.interactivity = interactivityModule;
})();