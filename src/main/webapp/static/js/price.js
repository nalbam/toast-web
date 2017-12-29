$(function () {
    $('.btn-price').button().click(function () {
        load_price_id(this.id);
    });
});

function load_price_id(id) {
    let arr = id.split("-");
    load_price(arr[1]);
}

function load_price(no) {
    let url = '/server/hours/' + no;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (res, status) {
            console.log('load_price (' + no + ') : ' + status);

            if (res) {
                $("#price-" + res.no + "-hour").html(res.instance.price);
                for (let i in res.hours) {
                    let price = (res.instance.price * res.hours[i].hs).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                    $("#price-" + res.no + "-" + res.hours[i].ym).html(price);
                }
            }
        }
    });
}
