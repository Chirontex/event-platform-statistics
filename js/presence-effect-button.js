if (eps_button_text_default == undefined) var eps_button_text_default = {}

if (eps_detached_buttons == undefined) var eps_detached_buttons = {}

async function epsPresenceConfirmationSend(message_position, message_class, message_style, button_id, list_name)
{
    const button = document.getElementById(button_id)

    button.setAttribute('disabled', 'true')
    button.innerHTML = 'Отправка...'

    const body = new FormData
    body.append('list', list_name)

    await fetch(
        '/wp-json/event-platform-statistics/v1/presence-time/add',
        {
            method: 'POST',
            body: body
        }
    ).then(async (response) => {

        const answer = response.ok ?
            await response.json() :
            {code: -9999, message: 'Ошибка отправки. Пожалуйста, проверьте ваше интернет-подключение и обратитесь в техподдержку.'}
        
        let message_text

        console.clear();

        switch (await answer.code) {
            case -9999:
                message_text = answer.message
                console.error(`epsPresenceConfirmationSend() :\n\tcode: -9999\n\tmessage: `+answer.message)
                break
        
            case 0:
                message_text = 'Подтверждение присутствия успешно отправлено!'
                console.log(`epsPresenceConfirmationSend() :\n\tcode: 0\n\tmessage: `+answer.message)
                break
            
            default:
                message_text = 'Ошибка обработки подтверждения на сервере. Пожалуйста, обратитесь в техподдержку.'
                console.error(`epsPresenceConfirmationSend() :\n\tcode: `+answer.code+`\n\tmessage: `+answer.message)
                break
        }

        epsPresenceConfirmationMessage({
            text: message_text,
            class: message_class,
            style: message_style,
            position: message_position,
            timeout: 3000,
            button: button_id
        })

        if (window.eps_detached_buttons[button_id] == undefined)
        {
            button.removeAttribute('disabled')
            button.innerHTML = window.eps_button_text_default[button_id]
        }

    })
}

function epsPresenceConfirmationMessage(atts)
{
    const button = document.getElementById(atts['button'])

    const message = document.createElement('p')

    message.setAttribute('class', atts['class'])
    message.setAttribute('style', atts['style'])

    if (atts['position'] == 'before') button.parentNode.insertBefore(message, button)
    else button.parentNode.insertBefore(message, button.nextSibling)

    message.innerHTML = atts['text']

    setTimeout(epsPresenceConfirmationTimerHandler, atts['timeout'], button, message)
}

function epsPresenceConfirmationTimerHandler(button_node, message_node)
{
    button_node.parentNode.removeChild(message_node)
}

async function epsPresenceDetachedButtonGet(button_id)
{
    const button = document.getElementById(button_id);

    const body = new FormData;

    body.append('buttonid', button_id);

    await fetch(
        '/wp-json/event-platform-statistics/v1/presence-time/get-detached-button',
        {
            method: 'POST',
            body: body
        }
    ).then(async (response) => {
        const answer = response.ok ?
            await response.json() :
            {code: -9999, message: 'Ошибка отправки.'};

        if (answer.code == 0)
        {
            if (window.eps_detached_buttons[button_id] ==
                undefined ||
                window.eps_detached_buttons[button_id].datetime !=
                answer.datetime) window.eps_detached_buttons[button_id] = {
                sended: false,
                datetime: answer.datetime
            };

            if (window.eps_detached_buttons[button_id].sended == false)
            {
                if (button.hasAttribute('disabled')) button.removeAttribute('disabled');
    
                button.innerHTML = window.eps_button_text_default[button_id];
            }
        }

        const message = `epsPresenceDetachedButtonGet() :\n\tcode: `+answer.code+`\n\tmessage: `+answer.message;

        console.clear();

        if (answer.code < 0) console.error(message);
        else console.log(message);
    });

    setTimeout(epsPresenceDetachedButtonGet, 15000, button_id);
}

function epsPresenceDetachedButtonSended(button_id)
{
    window.eps_detached_buttons[button_id].sended = true;

    const button = document.getElementById(button_id);

    button.innerHTML = 'Подтверждение не требуется';
    button.setAttribute('disabled', 'true');
}
