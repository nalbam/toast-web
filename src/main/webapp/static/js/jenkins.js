let project = '';
let branch_num = 0;

$(function () {
    jenkins_jobs();
});

function jenkins_jobs() {
    let url = "/jenkins/job/" + name;
    $.ajax({
        url: url,
        type: "GET",
        dataType: 'json',
        success: function (data, status) {
            console.log('jenkins_jobs (' + name + ') : ' + status);

            if (!data || data === null || data.name === null) {
                return;
            }

            project = data.name;

            //data.jobs.sort(function (a, b) {
            //    var alc = a.name.toLowerCase();
            //    var blc = b.name.toLowerCase();
            //    return alc > blc ? 1 : alc < blc ? -1 : 0;
            //});

            branch_num = 100;
            data.jobs.forEach(jenkins_item);
        }
    });
}

function jenkins_item(item) {
    // if (item.name !== 'master' && item.name !== 'dev') {
    //     return;
    // }

    let num = branch_num++;

    let html = "<tr>";
    html += "<td id='br-" + num + "-name'><a href='" + item.url + "' target='_blank'>" + item.name + "</a></td>";
    html += "<td><span id='br-" + num + "-build' class='build' onclick='build_check(\"" + item.name + "\", " + num + ")'><img src='/static/jenkins/clock.gif'></span></td>";
    html += "<td id='br-" + num + "-status'><img src='/static/jenkins/notbuilt.gif'></td>";
    html += "<td id='br-" + num + "-health'>-</td>";
    html += "<td id='br-" + num + "-last-build'>-</td>";
    html += "<td id='br-" + num + "-last-success'>-</td>";
    html += "<td id='br-" + num + "-last-failed'>-</td>";
    html += "</tr>";

    $("#jobs").append(html);

    jenkins_job(item.name, num);

    setInterval(function () {
        jenkins_job(item.name, num);
    }, 10000);
}

function jenkins_job(branch, num) {
    let url = "/jenkins/job/" + project + "/" + branch;
    $.ajax({
        url: url,
        type: "GET",
        dataType: 'json',
        success: function (data, status) {
            console.log('jenkins_job (' + branch + ') : ' + status);

            if (!data || data === null || data.name === null) {
                return;
            }

            $("#br-" + num + "-status").html("<img src='/static/jenkins/" + data.color + ".gif'>");

            $("#br-" + num + "-build").html("<img src='/static/jenkins/clock.gif'>");

            if (data.healthReport.length > 0) {
                $("#br-" + num + "-health").html("<img src='/static/jenkins/" + data.healthReport[0].iconUrl + "'>");
            } else {
                $("#br-" + num + "-health").html("-");
            }

            let html;

            if (data.lastBuild !== null) {
                html = "";
                html += "<a href='" + data.lastBuild.url + "' target='_blank'>#" + data.lastBuild.number + "</a> ";
                html += "<a href='" + data.lastBuild.url + "console' target='_blank'><img src='/static/jenkins/terminal.png'></a> ";
            } else {
                html = "-";
            }
            $("#br-" + num + "-last-build").html(html);

            if (data.lastSuccessfulBuild !== null) {
                html = "";
                html += "<a href='" + data.lastSuccessfulBuild.url + "' target='_blank'>#" + data.lastSuccessfulBuild.number + "</a> ";
                html += "<a href='" + data.lastSuccessfulBuild.url + "console' target='_blank'><img src='/static/jenkins/terminal.png'></a> ";
            } else {
                html = "-";
            }
            $("#br-" + num + "-last-success").html(html);

            if (data.lastFailedBuild !== null) {
                html = "";
                html += "<a href='" + data.lastFailedBuild.url + "' target='_blank'>#" + data.lastFailedBuild.number + "</a> ";
                html += "<a href='" + data.lastFailedBuild.url + "console' target='_blank'><img src='/static/jenkins/terminal.png'></a> ";
            } else {
                html = "-";
            }
            $("#br-" + num + "-last-failed").html(html);
        }
    });
}

function build_check(branch, num) {
    let url = "/jenkins/job/" + project + "/" + branch;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data, status) {
            console.log('build_check (' + branch + ') : ' + status);

            if (!data || data === null || !data.buildable) {
                $.alert('빌드를 시작 할수 없습니다. 잠시후 다시 시도해 주세요.', {type: 'danger'});
            } else if (data.color.indexOf('anime') > 0) {
                $.alert('빌드가 진행중 입니다. 잠시후 다시 시도해 주세요.', {type: 'warning'});
            } else if (data.inQueue) {
                $.alert('이미 빌드 대기큐에 등록 되었습니다.', {type: 'warning'});
            } else {
                build_start(branch, num);
            }
        },
        error: function (xhr, status) {
            console.log('build_check (' + branch + ') : ' + status);

            $("#br-" + num + "-build").html("<img src='/static/jenkins/clock.gif'>");

            $.alert('빌드를 시작 할수 없습니다. 관리자에게 문의해 주세요.', {type: 'danger'});
        }
    });
}

function build_start(branch, num) {
    let url = "/jenkins/build/" + project + "/" + branch;
    $.ajax({
        url: url,
        type: 'GET',
        success: function (data, status) {
            console.log('build_start (' + branch + ') : ' + status);
            console.log('build_start done result : ' + JSON.stringify(data));

            $.alert('빌드를 시작 합니다. [' + project + ' : ' + branch + ']', {type: 'info'});

            $("#br-" + num + "-build").html("<img src='/static/jenkins/clock_anime.gif'>");
        },
        error: function (xhr, status) {
            console.log('build_start (' + branch + ') : ' + status);

            $("#br-" + num + "-build").html("<img src='/static/jenkins/clock.gif'>");

            $.alert('빌드를 시작 할수 없습니다. Jenkins 에서 직접 실행해 주세요.', {type: 'danger'});
        }
    });
}
