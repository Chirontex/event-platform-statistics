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
    },
    updateOpen: (id) => {
        const button_id = document
            .getElementById('eps-detached-button-entry-'+id+'-button-id');

        const enable_datetime = document
            .getElementById('eps-detached-button-entry-'+id+'-enable-datetime');

        const datetime = enable_datetime.innerHTML.split(' ');

        datetime[0] = datetime[0].split('.');
        datetime[0] = datetime[0][2]+'-'+datetime[0][1]+'-'+datetime[0][0];

        const row = document.createElement('div');

        row.setAttribute('class', 'row');

        const date_container = document.createElement('div');

        date_container.setAttribute('class', 'col-xs-12 col-sm-6 col-md-6 col-lg-6');

        row.appendChild(date_container);

        const time_container = document.createElement('div');

        time_container.setAttribute('class', 'col-xs-12 col-sm-6 col-md-6 col-lg-6');

        row.appendChild(time_container);

        enable_datetime.innerHTML = '';
        enable_datetime.appendChild(row);

        const update = document.
            getElementById('eps-detached-button-entry-'+id+'-update');

        let input = document.createElement('input');

        input.setAttribute('type', 'text');
        input.setAttribute('class', 'form-control form-control-sm');
        input.setAttribute(
            'id',
            'eps-detached-button-update-'+id+'-button-id'
        );
        input.setAttribute('value', button_id.innerHTML);
        input.setAttribute('oninput', 'EPSDetachedButtons.updateCheckSubmit('+id+');');

        button_id.innerHTML = '';

        button_id.appendChild(input);

        input = document.createElement('input');

        input.setAttribute('type', 'date');
        input.setAttribute('class', 'form-control form-control-sm');
        input.setAttribute(
            'id',
            'eps-detached-button-update-'+id+'-date'
        );
        input.setAttribute('value', datetime[0]);
        input.setAttribute('oninput', 'EPSDetachedButtons.updateCheckSubmit('+id+');');

        date_container.appendChild(input);

        input = document.createElement('input');

        input.setAttribute('type', 'time');
        input.setAttribute('class', 'form-control form-control-sm');
        input.setAttribute(
            'id',
            'eps-detached-button-update-'+id+'-time'
        );
        input.setAttribute('value', datetime[1]);
        input.setAttribute('oninput', 'EPSDetachedButtons.updateCheckSubmit('+id+');');

        time_container.appendChild(input);

        update.innerHTML = '';

        const a = document.createElement('a');

        a.setAttribute('href', 'javascript:void(0)');
        a.setAttribute('id', 'eps-detached-button-update-'+id+'-submit');
        a.setAttribute('onclick', 'EPSDetachedButtons.updateSave('+id+');');

        a.innerHTML = 'Сохранить';

        update.appendChild(a);
    },
    updateCheckSubmit: (id) => {
        const button_id = document
            .getElementById('eps-detached-button-update-'+id+'-button-id');

        const date = document
            .getElementById('eps-detached-button-update-'+id+'-date');

        const time = document
            .getElementById('eps-detached-button-update-'+id+'-time');

        const submit = document
            .getElementById('eps-detached-button-update-'+id+'-submit');

        if (button_id.value != '' &&
            date.value != '' &&
            time.value != '')
        {
            if (submit.hasAttribute('hidden')) submit.removeAttribute('hidden');
        }
        else
        {
            if (!submit.hasAttribute('hidden')) submit.setAttribute('hidden', 'true');
        }
    },
    updateSave: (id) => {
        const form = document
            .getElementById('eps-detached-button-entry-update-form');

        const button_id = document
            .getElementById('eps-detached-button-update-'+id+'-button-id');

        const date = document
            .getElementById('eps-detached-button-update-'+id+'-date');

        const time = document
            .getElementById('eps-detached-button-update-'+id+'-time');

        let input = document.createElement('input');

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'eps-detached-button-update-button-id');
        input.setAttribute('value', button_id.value);
        input.setAttribute('required', 'true');

        form.appendChild(input);

        input = document.createElement('input');

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'eps-detached-button-update-date');
        input.setAttribute('value', date.value);
        input.setAttribute('required', 'true');

        form.appendChild(input);

        input = document.createElement('input');

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'eps-detached-button-update-time');
        input.setAttribute('value', time.value);
        input.setAttribute('required', 'true');

        form.appendChild(input);

        input = document.createElement('input');

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'eps-detached-button-update-entry-id');
        input.setAttribute('value', id);
        input.setAttribute('required', 'true');

        form.appendChild(input);

        form.submit();
    },
    delete: (id) => {
        const form = document
            .getElementById('eps-detached-button-entry-delete-form');

        const input = document.createElement('input');

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'eps-detached-button-delete-entry-id');
        input.setAttribute('value', id);
        input.setAttribute('required', 'true');

        form.appendChild(input);

        form.submit();
    }
}
