class CustomTabla {
    constructor (identificador) {
        this.identificador = identificador;
    }
    // Getter
    get limpiarTabla() {
        return this.limpiar();
    }
    // Método
    limpiar () {
        var table = document.getElementById(this.identificador);
        for(var i = table.rows.length - 1; i > 0; i--)
        {
            table.deleteRow(i);
        }
    }
}