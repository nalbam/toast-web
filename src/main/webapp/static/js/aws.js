function aws_load(id) {
    if (id === '') {
        return;
    }
    let arr;
    let vpc = '';
    if (id === 'vpc') {
        arr = arr_vpc;
    } else if (id === 'image') {
        arr = arr_image;
    } else {
        let v = document.getElementById('aws_vpc');
        vpc = v.options[v.selectedIndex].value;

        if (id === 'subnet') {
            arr = arr_subnet;
        } else if (id === 'security') {
            arr = arr_security;
        }
    }

    let x = document.getElementById('aws_' + id);
    if (x === undefined) {
        return;
    }

    //aws_reset(x, 'loading');

    let url = '/aws/' + id;
    if (vpc !== '') {
        url += url + '?vpc=' + vpc;
    }
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('aws_load (' + id + ') : ' + status);

            aws_reset(x, 'none');

            if (res) {
                res.forEach(function (item) {
                    let c = document.createElement('option');
                    c.value = item.value;
                    if (id === 'vpc' || id === 'subnet') {
                        c.text = item.value + ' (' + item.text + ')';
                    } else {
                        c.text = item.text;
                    }
                    if ($.inArray(item.value, arr) > -1) {
                        c.selected = true;
                    }
                    x.options.add(c);
                }, this);
            }

            if (id === 'vpc' && arr_vpc.length > 0) {
                arr_vpc.length = 0;
                aws_load('subnet');
                aws_load('security');
            }

            arr.length = 0;
        }
    });
}

function aws_reset(x, v) {
    if (x !== undefined) {
        x.length = 0;
        if (v !== '') {
            let c = document.createElement('option');
            c.value = '';
            c.text = '- ' + v + ' -';
            x.options.add(c);
        }
    }
}
