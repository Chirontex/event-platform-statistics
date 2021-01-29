function epsPresenceConfirmationSend(message_position, message_class, message_style, button_id, list_name)
{
    const button = document.getElementById(button_id);

    let button_text = button.innerHTML;

    button.setAttribute('disabled', 'true');
    button.innerHTML = 'Отправка...';

    let request = $.ajax({
        url: "/wp-json/event-platform-statistics/v1/presence-time/add",
        method: "POST",
        data: {list: list_name},
        dataType: "json"
    });

    request.done(function(answer) {
        console.log('epsPresenceConfirmationSend(), answer:');
        console.log('   code: '+answer['code']);
        console.log('   message: '+answer['message']);

        let message_text;

        if (answer['code'] == 0) message_text = 'Подтверждение присутствия успешно отправлено!';
        else message_text = 'Ошибка обработки на подтверждения на сервере. Пожалуйста, обратитесь в техподдержку.';

        epsPresenceConfirmationMessage({
            text: message_text,
            class: message_class,
            style: message_style,
            position: message_position,
            timeout: 3000,
            button: button_id
        });

        button.removeAttribute('disabled');
        button.innerHTML = button_text;
    });

    request.fail(function(jqXHR, textStatus) {
        console.log('epsPresenceConfirmationSend() error:');
        console.log('   text status: '+textStatus);
        console.log('   jqXHR:');
        console.log(jqXHR);

        epsPresenceConfirmationMessage({
            text: 'Ошибка отправки. Пожалуйста, проверьте ваше интернет-подключение и обратитесь в техподдержку.',
            class: message_class,
            style: message_style,
            position: message_position,
            timeout: 3000,
            button: button_id
        });
    });
}

function epsPresenceConfirmationMessage(atts)
{
    const button = document.getElementById(atts['button']);

    const message = document.createElement('p');

    message.setAttribute('class', atts['class']);
    message.setAttribute('style', atts['style']);

    if (atts['position'] == 'before') button.parentNode.insertBefore(message, button);
    else button.parentNode.insertBefore(message, button.nextSibling);

    message.innerHTML = atts['text'];

    setTimeout(epsPresenceConfirmationTimerHandler, atts['timeout'], button, message);
}

function epsPresenceConfirmationTimerHandler(button_node, message_node)
{
    button_node.parentNode.removeChild(message_node);
}
