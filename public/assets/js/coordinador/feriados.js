const FeriadosManager = {
    init() {
        this.modal = $('#modalFeriado');
        this.form = document.getElementById('formFeriado');
    },

    openCreate(fechaPredeterminada = '') {
        this.form.reset();
        document.getElementById('in_fecha').value = fechaPredeterminada;
        this.modal.modal('show');
    },

    confirmDelete(id) {
        if(confirm('¿Desea eliminar este día feriado? Esto afectará la planificación.')) {
            document.getElementById('form-delete-' + id).submit();
        }
    }
};

document.addEventListener('DOMContentLoaded', () => FeriadosManager.init());
