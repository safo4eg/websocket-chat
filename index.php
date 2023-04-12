<?php include_once('includes/start.php')?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <script defer src="js/modules/connection.js"></script>
    <script defer src="js/modules/interactivity.js"></script>
    <script defer src="js/script.js"></script>
    <title>Chat</title>
</head>
<body>
<div class="viewport">
    <div class="container">
        <div class="chat">
            <div class="left">
                <div class="title">Чаты</div>
                <div class="dialogues-wrapper">

                    <div class="dialogue">
                        <input type="hidden" value="all">
                        <div class="img-wrapper"></div>
                        <div class="info">
                            <div class="name">Общий</div>
                            <div class="message">Последнее сообщение</div>
                        </div>
                    </div>

                </div>
            </div> <!-- chat left -->

            <div class="right">
                <div class="top">
                    <div class="title">Общий</div>
                </div>

                <div class="messages-wrapper" id="messages-wrapper">
                    <div class="message">
                        <input type="hidden" value="0"> <!-- уникальный ид который не будет ни у какого пользователя -->
                    </div>


                </div> <!-- message-wrapper -->

                <div class="pen">
                        <textarea
                            name="input-message"
                            id="input-message"
                            placeholder="Введите сообщение..."
                            style="height: 20px"></textarea>
                </div>
            </div> <!-- chat right -->
        </div> <!-- chat -->
    </div>

    <?php if(!isset($_SESSION['auth'])) { ?>
        <div id="modal" class="modal">
            <div class="modal-content">
                <div class="title">Для доступа к чату нужно авторизироваться</div>
                <div class="tabs">
                    <button id="login-tab" class="active">Вход</button>
                    <button id="register-tab" type="button">Регистрация</button>
                </div>
                <form id="auth-form" action="/">
                    <input type="text" name="username" placeholder="username">
                    <input type="text" name="password" placeholder="password">
                    <input id="auth-action" name="action" type="hidden" value="login">
                    <button id="auth-btn" type="submit">Войти</button>
                </form>
            </div>
        </div> <!-- modal -->
    <?php } ?>
</div>
</body>
</html>

<?php unset($_SESSION['auth']) ?>