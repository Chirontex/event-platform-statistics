const EPSDetachedButtons = {
    checkSubmit: () => {
        const button_id = document.getElementById('eps-detached-buttons-add-button-id');
        const date = document.getElementById('eps-detached-buttons-add-date');
        const time = document.getElementById('eps-detached-buttons-add-time');
        const button = document.getElementById('eps-detached-buttons-add-submit');

        if (button_id.value != '' &&
            date.value != '' &&
            time.value != '')
        {
            if (button.hasAttribute('disabled')) button.removeAttribute('disabled');
        }
        else
        {
            if (!button.hasAttribute('disabled')) button.setAttribute('disabled', 'true');
        }
    }
}
