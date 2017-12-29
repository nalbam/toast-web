function fleet_remove(id) {
    $('#md-fleet-remove-btn').html('<button type="button" onclick="fleet_remove_confirm(\'' + id + '\')" class="btn btn-danger">Remove</button>');
    $('#md-fleet-remove').modal('show');
}

function fleet_remove_confirm(id) {
    btn_call(id);
    $('#md-fleet-remove').modal('hide');
}
