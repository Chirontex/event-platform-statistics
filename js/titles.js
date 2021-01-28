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
