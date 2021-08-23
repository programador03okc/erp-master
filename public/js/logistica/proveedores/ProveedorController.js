class ProveedorCtrl{
    constructor(proveedorModel) {
        this.proveedorModel = proveedorModel;
    }

    getListaProveedores(){
        return this.proveedorModel.getListaProveedores();

    }
}