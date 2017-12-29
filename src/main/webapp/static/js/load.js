let hash_server = '';
let hash_target = '';
let hash_ip = '';

function load_server(method, no) {
    if (no === undefined || no === '') {
        return;
    }

    let button = $('#server_' + method + '_' + no);
    button.html("<i class='fa fa-refresh fa-spin primary'></i>").attr("disabled", true);

    let url = '/server/' + method + '/' + no;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('load_server (' + method + ', ' + no + ') : ' + status);

            if (res) {
                if (res.hash !== hash_server) {
                    hash_server = res.hash;
                    $('#tb_server').empty();
                    res.list.forEach(append_server);

                    // reload
                    if (method === 'fleet') {
                        load_target(method, no);
                        load_ip(method, no);
                    }
                }
            }

            button.html("<i class='fa fa-refresh primary'></i>").removeAttr("disabled");
        }
    });
}

function load_target(method, no) {
    if (method === 'one') {
        return;
    }
    if (no === undefined || no === '') {
        return;
    }

    let button = $('#target_' + method + '_' + no);
    button.html("<i class='fa fa-refresh fa-spin primary'></i>").attr("disabled", true);

    let url = '/target/' + method + '/' + no;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('load_target (' + method + ', ' + no + ') : ' + status);

            if (res) {
                if (res.hash !== hash_target) {
                    hash_target = res.hash;
                    $('#tb_target').empty();
                    res.list.forEach(append_target);
                }
            }

            button.html("<i class='fa fa-refresh primary'></i>").removeAttr("disabled");
        }
    });
}

function load_ip(method, no) {
    if (method === 'one') {
        return;
    }
    if (no === undefined || no === '') {
        return;
    }

    let button = $('#ip_' + method + '_' + no);
    button.html("<i class='fa fa-refresh fa-spin primary'></i>").attr("disabled", true);

    let url = '/ip/' + method + '/' + no;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('load_ip (' + method + ', ' + no + ') : ' + status);

            if (res) {
                if (res.hash !== hash_ip) {
                    hash_ip = res.hash;
                    $('#tb_ip').empty();
                    res.list.forEach(append_ip);
                }
            }

            button.html("<i class='fa fa-refresh primary'></i>").removeAttr("disabled");
        }
    });
}

function append_server(item) {
    let html = '';
    html += '<tr id="server-' + item.no + '">';
    html += ' <td><input type="text" name="name" value="' + empty_value(item.name) + '" onchange="server_save(this, ' + item.no + ')" class="form-control input-sm" placeholder="name"/></td>';
    html += ' <td><a href="/server/item/' + item.no + '" title="' + get_instance(item.instance) + '"><i class="fa fa-server"></i> ' + empty_value(item.id) + '</a></td>';
    html += ' <td>' + empty_href(item.ip) + '</td>';
    html += ' <td>' + empty_href(item.host) + '</td>';
    html += ' <td>' + empty_value(item.port) + '</td>';
    html += ' <td>' + empty_value(item.user) + '</td>';
    html += ' <td>';

    if (item.host !== '') {
        if (item.h.ping.s < 66) {
            html += ' <i class="fa fa-check success"></i> ';
        } else {
            html += ' <i class="fa fa-exclamation danger"></i> (' + item.h.ping.d + ') ';
        }
    } else {
        html += ' <i class="fa fa-exclamation danger"></i> ';
    }

    if (item.host !== '') {
        if (item.h.pong.s < 66) {
            html += ' <i class="fa fa-check success"></i> ';
        } else {
            html += ' <i class="fa fa-exclamation danger"></i> (' + item.h.pong.d + ') ';
        }
    } else {
        html += ' <i class="fa fa-exclamation danger"></i> ';
    }

    html += ' </td>';
    html += ' <td>';

    html += '<div class="btn-group">';

    if (item.host !== '') {
        if (item.plugYN === 'Y') {
            html += ' <button id="server_toggle_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-toggle-on primary"></i></button>';
        } else {
            html += ' <button id="server_toggle_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-toggle-off danger"></i></button>';
        }
        if (item.phase === 'lb') {
            html += ' <button id="deploy_lb_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-pied-piper primary"></i></button>';
        } else {
            html += ' <button id="deploy_server_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-download primary"></i></button>';
            html += ' <button id="deploy_vhost_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-pied-piper primary"></i></button>';
        }
    }

    html += ' <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog primary"></i> <span class="caret primary"></span></button>';

    html += ' <div class="dropdown-menu">';

    if (item.host !== '') {
        html += ' <button id="server_update_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-anchor primary"></i></button>';
    }

    if (item.locked === 'Y') {
        html += ' <button id="server_protect_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-lock warning"></i></button>';
    } else {
        html += ' <button id="server_protect_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-unlock primary"></i></button>';
    }

    if (item.power === 'Y') {
        html += ' <button id="server_stop_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-power-off primary"></i></button>';
    } else if (item.power === 'N') {
        html += ' <button id="server_start_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-power-off danger"></i></button>';
    } else {
        html += ' <button class="btn btn-default btn-sm"><i class="fa fa-refresh fa-spin warning"></i></button>';
    }

    if (item.locked !== 'Y') {
        html += ' <button id="server_remove_' + item.no + '" onclick="server_remove(this.id)" class="btn btn-default btn-sm"><i class="fa fa-trash primary"></i></button>';
    }

    html += ' </div>';
    html += '</div>';

    html += ' </td>';
    html += '</tr>';

    $("#tb_server").append(html);
}

function append_target(item) {
    let html = '';

    html += '<tr id="target-' + item.no + '">';
    html += ' <td>' + empty_value(item.groupId) + '</td>';
    html += ' <td><a href="/project/item/' + item.p_no + '">' + item.artifactId + '</a></td>';

    if (item.deployed) {
        if (item.version === item.deployed) {
            html += ' <td><span id="deployed_' + item.no + '">' + item.deployed + '</span> <span id="deployed_' + item.no + '_icon"></span></td>';
        } else {
            html += ' <td><span id="deployed_' + item.no + '">' + item.deployed + '</span> <span id="deployed_' + item.no + '_icon"><i class="fa fa-exclamation-triangle warning"></i></span></td>';
        }
    }

    html += ' <td>';
    html += ' <select name="version" onchange="target_version(this, ' + item.no + ')" class="form-control input-sm">';
    html += ' <option value="0.0.0">0.0.0</option>';

    if (item.versions) {
        item.versions.forEach(function (version) {
            if (version.version === item.version) {
                html += ' <option value="' + version.version + '" selected>' + version.version + ' - ' + version.status.name + '</option>';
            } else {
                html += ' <option value="' + version.version + '">' + version.version + ' - ' + version.status.name + '</option>';
            }
        });
    }

    html += ' </select>';
    html += ' </td>';
    html += ' <td>' + empty_value(item.packaging) + ' / ' + empty_value(item.deploy) + '</td>';
    html += ' <td>' + empty_href(item.domain) + ':' + empty_value(item.port) + '</td>';

    if (item.le === 'Y') {
        html += ' <td><input type="checkbox" id="target_le_' + item.no + '" name="le" value="Y" onclick="target_le(this, ' + item.no + ')" checked/></td>';
    } else {
        html += ' <td><input type="checkbox" id="target_le_' + item.no + '" name="le" value="Y" onclick="target_le(this, ' + item.no + ')"/></td>';
    }

    html += ' <td>';

    html += '<div class="btn-group">';

    if (item.deployYN === 'Y') {
        html += ' <button id="target_toggle_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-toggle-on primary"></i></button>';
    } else {
        html += ' <button id="target_toggle_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-toggle-off danger"></i></button>';
    }

    if (item.s_no) {
        html += ' <button id="deploy_server_' + item.s_no + '_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-call btn-default btn-sm"><i class="fa fa-download primary"></i></button>';
    } else {
        html += ' <button id="deploy_fleet_' + item.f_no + '_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-call btn-default btn-sm"><i class="fa fa-download primary"></i></button>';
    }

    html += ' <button id="target_remove_' + item.no + '" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-trash primary"></i></button>';

    html += '</div>';

    html += ' </td>';
    html += '</tr>';

    $("#tb_target").append(html);
}

function append_ip(item) {
    let html = '';

    html += '<tr id="ip-' + item.no + '">';
    html += ' <td>' + empty_value(item.id) + '</td>';
    html += ' <td>' + empty_value(item.ip) + '</td>';
    html += ' <td>' + empty_value(item.s_id) + '</td>';
    html += ' <td>' + empty_value(item.name) + '</td>';
    html += ' <td>';
    html += ' <button id="ip_remove_' + item.no + '" onclick="ip_remove(this.id)" class="btn btn-default btn-sm"><i class="fa fa-trash primary"></i></button>';
    html += ' </td>';
    html += '</tr>';

    $("#tb_ip").append(html);
}

function get_instance(text) {
    if (text) {
        let obj = JSON.parse(text);
        if (obj.type) {
            return obj.type;
        }
    }
    return '';
}

function server_save(e, no) {
    let data = 'key=' + e.name + '&val=' + e.value;
    ajax_call('/server/save/' + no, data, '');
}

function server_remove(id) {
    $('#md-server-remove-btn').html('<button type="button" onclick="server_remove_confirm(\'' + id + '\')" class="btn btn-danger">Terminate</button>');
    $('#md-server-remove').modal('show');
}

function server_remove_confirm(id) {
    btn_call(id);
    $('#md-server-remove').modal('hide');
}

function target_version(e, no) {
    let version = e.value;

    ajax_call('/target/version/' + no, 'version=' + version, '');

    let deployed = $("#deployed_" + no).text();
    if (deployed) {
        if (deployed === version) {
            $("#deployed_" + no + "_icon").html('');
        } else {
            $("#deployed_" + no + "_icon").html('<i class="fa fa-exclamation-triangle warning"></i>');
        }
    }
}

function target_le(e, no) {
    if (e.checked) {
        ajax_call('/target/le/' + no, 'le=Y', '');
    } else {
        ajax_call('/target/le/' + no, 'le=N', '');
    }
}

function ip_remove(id) {
    $('#md-ip-remove-btn').html('<button type="button" onclick="ip_remove_confirm(\'' + id + '\')" class="btn btn-danger">Remove</button>');
    $('#md-ip-remove').modal('show');
}

function ip_remove_confirm(id) {
    btn_call(id);
    $('#md-ip-remove').modal('hide');
}
