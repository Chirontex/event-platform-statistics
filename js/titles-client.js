var eps_title_default = '';

function epsTitleGet(title_id, list_name)
{
    const title = document.getElementById(title_id);

    if (window.eps_title_default == '') window.eps_title_default = title.innerHTML;

    let request = $.ajax({
        url: "/wp-json/event-platform-statistics/v1/titles/get-actual-title",
        method: "POST",
        data: {list: list_name},
        dataType: "json"
    });

    request.done(function(answer) {
        console.log('epsTitleGet(), answer:');
        console.log('   code: '+answer['code']);
        console.log('   message: '+answer['message']);

        if (answer['code'] == 0) title.innerHTML = answer['data'];
        else if (answer['code'] > 0) title.innerHTML = window.eps_title_default;
        else if (answer['code'] < 0) title.innerHTML = 'Ошибка, код '+answer['code']+': "'+answer['message']+'"';

        epsTitleTimeout(title_id, list_name);
    });

    request.fail(function(jqXHR, textStatus) {
        console.log('epsTitleGet(), error:');
        console.log('   text status: '+textStatus);
        console.log('   jqXHR:');
        console.log(jqXHR);

        title.innerHTML = 'Не удалось обновить заголовок. Пожалуйста, проверьте ваше интернет-подключение и обратитесь в техподдержку.';

        epsTitleTimeout(title_id, list_name);
    });
}

function epsTitleTimeout(title_id, list_name)
{
    setTimeout(epsTitleGet, 5000, title_id, list_name);
}
