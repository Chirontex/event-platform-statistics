async function epsPresenceConfirmationSend(message_position, message_class, message_style, button_id, list_name)
{
    const button = document.getElementById(button_id)

    let button_text = button.innerHTML

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

        switch (await answer.code) {
            case -9999:
                message_text = answer.message
                break
        
            case 0:
                message_text = 'Подтверждение присутствия успешно отправлено!'
                break
            
            default:
                message_text = 'Ошибка обработки подтверждения на сервере. Пожалуйста, обратитесь в техподдержку.'
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

        button.removeAttribute('disabled')
        button.innerHTML = button_text

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
