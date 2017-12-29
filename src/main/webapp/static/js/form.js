$(function () {
    $('.btn-form').button().click(function () {
        btn_form(this.id);
    });

    $('.btn-call').button().click(function () {
        btn_call(this.id);
    });
});

function btn_form(id) {
    let form = $('#form_' + id);

    let url = form.attr('action');
    if (url === undefined) {
        return;
    }

    let data = form.serialize();

    ajax_call(url, data, id);
}

function btn_call(id) {
    let url = '/' + id.replace(/_/g, '/');

    ajax_call(url, null, id);
}

function ajax_call(url, data, id) {
    let button = $('#' + id);
    let output = $('#output');

    button.html("<i class='fa fa-refresh fa-spin primary'></i>").attr("disabled", true);
    output.html("");

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (res, status) {
            console.log('ajax_call (' + id + ') : ' + status);

            // login
            if (!res.icon) {
                if (res.result === 'SUCCESS') {
                    if (res.data && res.data.token) {
                        location.replace('/home/callback?token=' + res.data.token);
                        return;
                    }
                } else if (res.result === 'FAILED') {
                    button.html("<i class='fa fa-sign-in danger'></i>").removeAttr("disabled");
                    $.alert(res.message, {type: 'danger'});
                    return;
                }
            }

            // action
            if (res.action && res.action !== '') {
                if (res.action === 'reload') {
                    location.reload();
                } else if (res.action === 'script') {
                    eval(res.message);
                } else if (res.action === 'remove') {
                    remove_item(res.message);
                } else if (res.action === 'redirect') {
                    location.replace(res.message);
                } else if (res.action === 'output') {
                    output.html("<pre>" + res.message + "</pre>");
                } else if (res.action === 'none') {
                    // do nothing
                } else {
                    $.alert(res.message, {type: res.action});
                }
            }

            // icon
            let icon;
            if (res.action === 'warning') {
                icon = 'warning';
            } else {
                if (res.result === true) {
                    icon = 'primary';
                } else {
                    icon = 'danger';
                }
            }
            button.html("<i class='fa " + res.icon + " " + icon + "'></i>").removeAttr("disabled");
        }
    });
}

function empty_value(v) {
    if (v === null) {
        return '';
    }
    return v;
}

function empty_href(v) {
    if (v === null) {
        return '';
    }
    return '<a href="http://' + v + '" target="_blank">' + v + '</a>';
}

function empty_href_ssl(v) {
    if (v === null) {
        return '';
    }
    return '<a href="https://' + v + '" target="_blank">' + v + '</a>';
}

function search_phase(v) {
    location.href = '?phase=' + v;
}

function remove_item(id) {
    if (id === undefined || id === '') {
        return;
    }
    $('#' + id).remove();
}

function remove_row(id, no) {
    if (id === undefined || id === '') {
        return;
    }
    if (no === undefined || no === '') {
        return;
    }
    remove_item(id + '-' + no);
}

function remove_fleet(no) {
    remove_row('fleet', no);
}

function remove_server(no) {
    remove_row('server', no);
}

function remove_project(no) {
    remove_row('project', no);
}

function remove_version(no) {
    remove_row('version', no);
}

function remove_target(no) {
    remove_row('target', no);
}

function remove_ip(no) {
    remove_row('ip', no);
}
