function epsAdminSubmitCheck()
{
    const button = document.getElementById('eps-admin-form-submit');
    const checks = document.getElementsByClassName('form-check-input');

    let allowed = false;

    for (let i = 0; i < checks.length; i++)
    {
        if (checks[i].checked)
        {
            allowed = true;
            break;
        }
    }

    if (allowed)
    {
        if (button.hasAttribute('disabled')) button.removeAttribute('disabled');
    }
    else
    {
        if (!button.hasAttribute('disabled')) button.setAttribute('disabled', 'true');
    }
}