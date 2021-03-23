if (eps_title_default == undefined) var eps_title_default = {}

async function epsTitleGet(title_id, list_name)
{
    const title = document.getElementById(title_id)

    if (window.eps_title_default[title_id] == '' ||
        window.eps_title_default[title_id] == null ||
        window.eps_title_default[title_id] == undefined) window.eps_title_default[title_id] = title.innerHTML

    const body = new FormData
    body.append('list', list_name)

    await fetch(
        '/wp-json/event-platform-statistics/v1/titles/get-actual-title',
        {
            method: 'POST',
            body: body
        }
    ).then(async (response) => {

        const answer = response.ok ?
            await response.json() :
            {code: -9999, message: 'Не удалось обновить заголовок. Пожалуйста, проверьте ваше интернет-подключение и обратитесь в техподдержку.'}

        const placeholders = [
            '!!%EPS_PH_BR1%!!', '!!%EPS_PH_BR2%!!', '!!%EPS_PH_BR_3%!!',
            '!!%EPS_B_OPEN%!!', '!!%EPS_B_CLOSE%!!',
            '!!%EPS_I_OPEN%!!', '!!%EPS_I_CLOSE%!!',
            '!!%EPS_U_OPEN%!!', '!!%EPS_U_CLOSE%!!'
        ]

        const tags = [
            '<br>', '<br />', '<br/>',
            '<b>', '</b>',
            '<i>', '</i>',
            '<u>', '</u>'
        ]

        let actualTitle

        switch (await answer.code) {
            case -9999:
                console.error(`epsTitleGet() :\n\tcode: -9999\n\tmessage: `+answer.message)
                title.innerHTML = 'Не удалось обновить заголовок. Пожалуйста, проверьте ваше интернет-подключение и обратитесь в техподдержку.'
                break

            case 0:
                console.log(`epsTitleGet() :\n\tcode: 0\n\tmessage: `+answer.message)
                
                actualTitle = answer.data.title

                for (let i = 0; i < placeholders.length; i++)
                {
                    actualTitle = actualTitle.split(placeholders[i])
                    actualTitle = actualTitle.join(tags[i])
                }

                title.innerHTML = actualTitle

                if (answer.data.nmo == 0) epsButtonHide(list_name)
                else epsButtonOpen(list_name)

                break
        
            default:
                if (answer.code < 0)
                {
                    console.error(`epsTitleGet() :\n\tcode: `+answer.code+`\n\tmessage: `+answer.message)
                    title.innerHTML = 'Ошибка, код '+answer.code+': "'+answer.message+'"'
                }
                else if (answer.code > 0)
                {
                    console.log(`epsTitleGet() :\n\tcode: `+answer.code+`\n\tmessage: `+answer.message)
                    title.innerHTML = window.eps_title_default[title_id]

                    epsButtonOpen(list_name)
                }
                break
        }

        epsTitleTimeout(title_id, list_name)

    })
}

function epsTitleTimeout(title_id, list_name)
{
    setTimeout(epsTitleGet, 5000, title_id, list_name)
}

function epsButtonHide(list_name)
{
    const buttons = document.getElementsByName('eps-presence-effect-button')

    for (let i = 0; i < buttons.length; i++)
    {
        if (buttons[i].getAttribute('eps-peb-list') == list_name)
        {
            if (!buttons[i].hasAttribute('hidden')) buttons[i].setAttribute('hidden', 'true')
        }
    }
}

function epsButtonOpen(list_name)
{
    const buttons = document.getElementsByName('eps-presence-effect-button')

    for (let i = 0; i < buttons.length; i++)
    {
        if (buttons[i].getAttribute('eps-peb-list') == list_name)
        {
            if (buttons[i].hasAttribute('hidden')) buttons[i].removeAttribute('hidden')
        }
    }
}
