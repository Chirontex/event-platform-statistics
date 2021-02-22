function epsAdminSubmitCheck()
{
    const button = document.getElementById('eps-admin-form-submit')
    const checks = document.getElementsByClassName('eps-admin-checkbox')

    let allowed = false

    for (let i = 0; i < checks.length; i++)
    {
        if (checks[i].checked)
        {
            allowed = true
            break
        }
    }

    if (allowed)
    {
        if (button.hasAttribute('disabled')) button.removeAttribute('disabled')
    }
    else
    {
        if (!button.hasAttribute('disabled')) button.setAttribute('disabled', 'true')
    }
}

function epsMetadataSubmitCheck()
{
    const button = document.getElementById('eps-metadata-add-submit')
    const name = document.getElementById('eps-metadata-add-name')
    const key = document.getElementById('eps-metadata-add-key')

    if (name.value != '' && key.value != '')
    {
        if (button.hasAttribute('disabled')) button.removeAttribute('disabled')
    }
    else
    {
        if (!button.hasAttribute('disabled')) button.setAttribute('disabled', 'true')
    }
}

function epsMetadataMatchUpdate(id)
{
    const name_cell = document.getElementById('eps-metadata-match-name-'+id)
    const key_cell = document.getElementById('eps-metadata-match-key-'+id)
    const button_cell = document.getElementById('eps-metadata-match-update-'+id)

    let elem = document.createElement('input')
    elem.setAttribute('type', 'text')
    elem.setAttribute('id', 'eps-metadata-match-update-name-'+id)
    elem.setAttribute('name', 'eps-metadata-match-update-name-'+id)
    elem.setAttribute('placeholder', 'Введите название')
    elem.setAttribute('value', name_cell.innerHTML)
    elem.setAttribute('oninput', 'epsMetadataMatchUpdateSubmitCheck('+id+')')

    name_cell.innerHTML = ''
    name_cell.appendChild(elem)

    elem = document.createElement('input')
    elem.setAttribute('type', 'text')
    elem.setAttribute('id', 'eps-metadata-match-update-key-'+id)
    elem.setAttribute('name', 'eps-metadata-match-update-key-'+id)
    elem.setAttribute('list', 'eps-metadata-datalist')
    elem.setAttribute('placeholder', 'Введите ключ')
    elem.setAttribute('value', key_cell.innerHTML)
    elem.setAttribute('oninput', 'epsMetadataMatchUpdateSubmitCheck('+id+')')

    key_cell.innerHTML = ''
    key_cell.appendChild(elem)

    elem = document.createElement('a')
    elem.setAttribute('href', 'javascript:void(0)')
    elem.setAttribute('id', 'eps-metadata-match-update-submit-'+id)
    elem.setAttribute('onclick', 'epsMetadataMatchSave('+id+');')
    elem.innerHTML = 'Сохранить'

    button_cell.innerHTML = ''
    button_cell.appendChild(elem)
}

function epsMetadataMatchSave(id)
{
    const button = document.getElementById('eps-metadata-update-submit')
    const update_trigger = document.getElementById('eps-metadata-update')
    const form = update_trigger.parentNode

    const name = document.getElementById('eps-metadata-match-update-name-'+id).value
    const key = document.getElementById('eps-metadata-match-update-key-'+id).value

    update_trigger.setAttribute('value', id)

    let input = document.createElement('input')
    input.setAttribute('type', 'hidden')
    input.setAttribute('name', 'eps-metadata-update-name')
    input.setAttribute('value', name)
    
    form.appendChild(input)

    input = document.createElement('input')
    input.setAttribute('type', 'hidden')
    input.setAttribute('name', 'eps-metadata-update-key')
    input.setAttribute('value', key)

    form.appendChild(input)

    button.click()
}

function epsMetadataMatchUpdateSubmitCheck(id)
{
    const name = document.getElementById('eps-metadata-match-update-name-'+id)
    const key = document.getElementById('eps-metadata-match-update-key-'+id)
    const button = document.getElementById('eps-metadata-match-update-submit-'+id)

    if (name.value !== '' && key.value !== '')
    {
        if (button.hasAttribute('hidden')) button.removeAttribute('hidden')
    }
    else
    {
        if (!button.hasAttribute('hidden')) button.setAttribute('hidden', 'true')
    }
}

function epsMetadataMatchDelete(id)
{
    const button = document.getElementById('eps-metadata-delete-submit')
    const delete_trigger = document.getElementById('eps-metadata-delete')

    delete_trigger.setAttribute('value', id)

    button.click()
}
