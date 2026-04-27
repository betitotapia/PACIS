$(document).ready(function () {
    load_traspasos();
});

function load_traspasos() {
    $("#traspasos_ajax").load("../../ajax/buscar_traspasos.php?action=ajax");
}
