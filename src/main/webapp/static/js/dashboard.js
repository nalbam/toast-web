var count = 0;

var gauge = [];

var opts = {
    angle: 0,
    pointer: {
        length: 0.75,
        strokeWidth: 0.03,
        color: '#222222'
    },
    colorStart: '#1ABC9C',
    colorStop: '#1ABC9C',
    strokeColor: '#E74C3C',
    limitMax: false,
    limitMin: false,
    generateGradient: true,
    highDpiSupport: true
};

var hash = '';
var down = '';

$(function () {
    // dashboard
    _dashboard();
    setInterval(function () {
        _dashboard();
    }, 30000);
});

function _dashboard() {
    var url = '/dashboard/servers';
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('_servers () : ' + status);

            if (res) {
                if (hash !== '' && hash !== res.hash) {
                    location.reload();
                    return;
                }

                hash = res.hash;
                count = res.count;

                _gauge('ping', res.ping_up);
                _gauge('pong', res.pong_up);

                res.phases.forEach(_progress);
                $('.progress .bar').progressbar();

                if (down !== res.down) {
                    $('#server_list').empty();
                    res.servers.forEach(_servers);
                    down = res.down;
                }
            }
        }
    });
}

function _progress(item) {
    $('#pb_cnt_' + item.phase).text(item.servers);
    $('#pb_bar_' + item.phase).attr('data-transitiongoal', item.rate);
}

function _servers(item) {
    var html = '';
    html += '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
    html += '    <div class="x_panel">';
    html += '        <div class="x_title">';
    html += '            <h2><a href="/server/item/' + item.no + '">' + item.name + '</a></h2>';
    html += '            <ul class="nav navbar-right ">';

    if (item.h.ping.s < 66) {
        html += '                    <li><i class="fa fa-check success"></i></li>';
    } else {
        html += '                    <li><i class="fa fa-exclamation danger"></i></li>';
    }
    if (item.h.pong.s < 66) {
        html += '                    <li><i class="fa fa-check success"></i></li>';
    } else {
        html += '                    <li><i class="fa fa-exclamation danger"></i></li>';
    }

    html += '            </ul>';
    html += '            <div class="clearfix"></div>';
    html += '        </div>';
    html += '        <div class="x_content">' + item.phase + ' > <a href="/fleet/item/' + item.f_no + '">' + item.fleet + '</a></div>';
    html += '    </div>';
    html += '</div>';

    $("#server_list").append(html);
}

function _gauge(name, val) {
    $('#' + name + '_text').text(val + ' / ' + count);

    if (gauge[name] === undefined) {
        gauge[name] = new Gauge(document.getElementById(name)).setOptions(opts);
    }

    gauge[name].maxValue = count;
    gauge[name].set(val);
}
