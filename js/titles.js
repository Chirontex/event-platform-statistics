function epsTitlesSubmitCheck()
{
    const button = document.getElementById('eps-titles-form-submit');
    const start_date = document.getElementById('eps-titles-start-date');
    const start_time = document.getElementById('eps-titles-start-time');
    const end_date = document.getElementById('eps-titles-end-date');
    const end_time = document.getElementById('eps-titles-end-time');
    const header = document.getElementById('eps-titles-header');
    const list = document.getElementById('eps-titles-list');

    if (start_date.value !== '' && start_time.value !== '' && end_date.value !== '' && end_time.value !== '' && header.value !== '' && list.value !== '')
    {
        if (button.hasAttribute('disabled')) button.removeAttribute('disabled');
    }
    else
    {
        if (!button.hasAttribute('disabled')) button.setAttribute('disabled', 'true');
    }
}

function epsTitlesDelete(id)
{
    document.getElementById('eps-titles-title-delete').value = id;
    document.getElementById('eps-titles-delete-submit').click();
}

function epsTitlesUpdate(id)
{
    const title_cell = document.getElementById('eps-title-title-'+id);
    const list_name_cell = document.getElementById('eps-title-list-name-'+id);
    const datetime_start_cell = document.getElementById('eps-title-datetime-start-'+id);
    const datetime_end_cell = document.getElementById('eps-title-datetime-end-'+id);
    const nmo_cell = document.getElementById('eps-title-nmo-'+id);
    const button_cell = document.getElementById('eps-title-update-button-'+id);

    let element = document.createElement('input');
    element.setAttribute('type', 'text');
    element.setAttribute('id', 'eps-titles-title-update-title-'+id);
    element.setAttribute('class', 'form-control');
    element.setAttribute('placeholder', 'Введите заголовок');
    element.setAttribute('value', title_cell.innerHTML);
    element.setAttribute('oninput', 'epsTitlesUpdateCheck('+id+');');

    title_cell.innerHTML = '';
    title_cell.appendChild(element);

    element = document.createElement('input');
    element.setAttribute('type', 'text');
    element.setAttribute('id', 'eps-titles-title-update-list-name-'+id);
    element.setAttribute('class', 'form-control');
    element.setAttribute('placeholder', 'Укажите обозначение зала');
    element.setAttribute('value', list_name_cell.innerHTML);
    element.setAttribute('oninput', 'epsTitlesUpdateCheck('+id+');');

    list_name_cell.innerHTML = '';
    list_name_cell.appendChild(element);

    let datetime_start = datetime_start_cell.innerHTML.split(' ');
    let datetime_end = datetime_end_cell.innerHTML.split(' ');

    let row = document.createElement('div');
    row.setAttribute('class', 'row');

    datetime_start_cell.innerHTML = '';
    datetime_start_cell.appendChild(row);

    let col_1 = document.createElement('div');
    col_1.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

    row.appendChild(col_1);

    let col_2 = document.createElement('div');
    col_2.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

    row.appendChild(col_2);

    element = document.createElement('input');
    element.setAttribute('type', 'date');
    element.setAttribute('id', 'eps-titles-title-update-date-start-'+id);
    element.setAttribute('class', 'form-control');
    element.setAttribute('value', datetime_start[0]);
    element.setAttribute('oninput', 'epsTitlesUpdateCheck('+id+');');

    col_1.appendChild(element);

    element = document.createElement('input');
    element.setAttribute('type', 'time');
    element.setAttribute('id', 'eps-titles-title-update-time-start-'+id);
    element.setAttribute('class', 'form-control');
    element.setAttribute('value', datetime_start[1]);
    element.setAttribute('oninput', 'epsTitlesUpdateCheck('+id+');');

    col_2.appendChild(element);

    row = document.createElement('div');
    row.setAttribute('class', 'row');

    datetime_end_cell.innerHTML = '';
    datetime_end_cell.appendChild(row);

    col_1 = document.createElement('div');
    col_1.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

    row.appendChild(col_1);

    col_2 = document.createElement('div');
    col_2.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

    row.appendChild(col_2);

    element = document.createElement('input');
    element.setAttribute('type', 'date');
    element.setAttribute('id', 'eps-titles-title-update-date-end-'+id);
    element.setAttribute('class', 'form-control');
    element.setAttribute('value', datetime_end[0]);
    element.setAttribute('oninput', 'epsTitlesUpdateCheck('+id+');');

    col_1.appendChild(element);

    element = document.createElement('input');
    element.setAttribute('type', 'time');
    element.setAttribute('id', 'eps-titles-title-update-time-end-'+id);
    element.setAttribute('class', 'form-control');
    element.setAttribute('value', datetime_end[1]);
    element.setAttribute('oninput', 'epsTitlesUpdateCheck('+id+');');

    col_2.appendChild(element);

    let nmo = nmo_cell.innerHTML;

    element = document.createElement('select');
    element.setAttribute('id', 'eps-titles-title-update-nmo-'+id);
    element.setAttribute('class', 'form-select');

    nmo_cell.innerHTML = '';
    nmo_cell.appendChild(element);

    let option = document.createElement('option');
    option.setAttribute('value', '1');

    if (nmo == 'Да') option.setAttribute('selected', 'true');

    element.appendChild(option);

    option.innerHTML = 'Да';

    option = document.createElement('option');
    option.setAttribute('value', '0');

    if (nmo == 'Нет') option.setAttribute('selected', 'true');

    element.appendChild(option);

    option.innerHTML = 'Нет';

    element = document.createElement('a');
    element.setAttribute('href', 'javascript:void(0)');
    element.setAttribute('id', 'eps-titles-title-update-save-button-'+id);
    element.setAttribute('onclick', 'epsTitleUpdateInit('+id+');');

    button_cell.innerHTML= '';
    button_cell.appendChild(element);

    element.innerHTML = 'Сохранить';
}

function epsTitlesUpdateCheck(id)
{
    const title = document.getElementById('eps-titles-title-update-title-'+id);
    const list_name = document.getElementById('eps-titles-title-update-list-name-'+id);
    const date_start = document.getElementById('eps-titles-title-update-date-start-'+id);
    const time_start = document.getElementById('eps-titles-title-update-time-start-'+id);
    const date_end = document.getElementById('eps-titles-title-update-date-end-'+id);
    const time_end = document.getElementById('eps-titles-title-update-time-end-'+id);
    
    let button = document.getElementById('eps-titles-title-update-save-button-'+id);

    const button_parent = button.parentNode;

    if (title.value !== '' && list_name.value !== '' && date_start.value !== '' && time_start.value !== '' && date_end.value !== '' && time_end.value !== '')
    {
        button_parent.removeChild(button);

        button = document.createElement('a');
        button.setAttribute('href', 'javascript:void(0)');
        button.setAttribute('id', 'eps-titles-title-update-save-button-'+id);
        button.setAttribute('onclick', 'epsTitleUpdateInit('+id+');');

        button_parent.appendChild(button);

        button.innerHTML = 'Сохранить';
    }
    else
    {
        button_parent.removeChild(button);

        button = document.createElement('p');
        button.setAttribute('id', 'eps-titles-title-update-save-button-'+id);

        button_parent.appendChild(button);
    }
}

function epsTitleUpdateInit(id)
{
    const submit = document.getElementById('eps-titles-update-submit');
    const update_trigger = document.getElementById('eps-titles-title-update');
    const form = update_trigger.parentNode;

    update_trigger.setAttribute('value', id);

    let input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-title');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-title-'+id).value);

    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-list-name');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-list-name-'+id).value);

    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-date-start');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-date-start-'+id).value);

    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-time-start');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-time-start-'+id).value);

    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-date-end');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-date-end-'+id).value);

    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-time-end');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-time-end-'+id).value);

    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'eps-titles-title-update-nmo');
    input.setAttribute('value', document.getElementById('eps-titles-title-update-nmo-'+id).value);

    form.appendChild(input);

    submit.click();
}
