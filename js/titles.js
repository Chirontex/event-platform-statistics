function epsTitlesSubmitCheck()
{
    const button = document.getElementById('eps-titles-form-submit');
    const start_date = document.getElementById('eps-titles-start-date');
    const start_time = document.getElementById('eps-titles-start-time');
    const end_date = document.getElementById('eps-titles-end-date');
    const end_time = document.getElementById('eps-titles-end-time');
    const header = document.getElementById('eps-titles-header');

    if (start_date.value !== '' && start_time !== '' && end_date !== '' && end_time !== '' && header !== '')
    {
        if (button.hasAttribute('disabled')) button.removeAttribute('disabled');
    }
    else
    {
        if (!button.hasAttribute('disabled')) button.setAttribute('disabled', 'true');
    }
}
