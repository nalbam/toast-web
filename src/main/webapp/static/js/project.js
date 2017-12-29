function project_remove(id) {
    $('#md-project-remove-btn').html('<button type="button" onclick="project_remove_confirm(\'' + id + '\')" class="btn btn-danger">Remove</button>');
    $('#md-project-remove').modal('show');
}

function project_remove_confirm(id) {
    btn_call(id);
    $('#md-project-remove').modal('hide');
}
