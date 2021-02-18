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

        switch (await answer.code) {
            case -9999:
                console.error(`epsTitleGet() :\n\tcode: -9999\n\tmessage: `+answer.message)
                title.innerHTML = 'Не удалось обновить заголовок. Пожалуйста, проверьте ваше интернет-подключение и обратитесь в техподдержку.'
                break

            case 0:
                console.log(`epsTitleGet() :\n\tcode: 0\n\tmessage: `+answer.message)
                title.innerHTML = answer.data
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
