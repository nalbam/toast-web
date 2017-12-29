$(function () {
    $(".version").button().click(function () {
        version_plus(this.id);
    });

    version_check();

    setInterval(function () {
        version_check();
    }, 10000);
});

function version_plus(name) {
    let v = 0;
    let major = $("#major"), minor = $("#minor"), build = $("#build");
    if (name === "major_plus") {
        v = eval(major.val()) + 1;
        major.val(v);
        minor.val("0");
        build.val("0");
    } else if (name === "minor_plus") {
        v = eval(minor.val()) + 1;
        minor.val(v);
        build.val("0");
    } else if (name === "build_plus") {
        v = eval(build.val()) + 1;
        build.val(v);
    }
}

function version_check() {
    let url = "/version/check/" + no;
    $.ajax({
        url: url,
        type: "GET",
        dataType: 'json',
        success: function (data, status) {
            console.log('version_check (' + no + ') : ' + status);

            if (data && data.result) {
                if (last !== data.mod_date) {
                    let r;
                    if (data.version === '0.0.0') {
                        r = confirm("새 버전이 등록 되었습니다. [" + data.version + "]\n" + "리로드 하시겠습니까?");
                        if (r === true) {
                            location.reload();
                        }
                    } else {
                        r = confirm("새 버전이 등록 되었습니다. [" + data.version + "]\n" + "Release Note 를 작성 하시겠습니까?");
                        if (r === true) {
                            location.href = "/version/item/" + data.no;
                        } else {
                            location.reload();
                        }
                    }
                    last = data.mod_date;
                }
            }
        }
    });
}

function version_remove(id) {
    $('#md-version-remove-btn').html('<button type="button" onclick="version_remove_confirm(\'' + id + '\')" class="btn btn-danger">Remove</button>');
    $('#md-version-remove').modal('show');
}

function version_remove_confirm(id) {
    btn_call(id);
    $('#md-version-remove').modal('hide');
}
