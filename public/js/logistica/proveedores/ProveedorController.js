class ProveedorCtrl{
    constructor(proveedorModel) {
        this.proveedorModel = proveedorModel;
    }

    getListaProveedores(){
        return this.proveedorModel.getListaProveedores();

    }
    getProveedor(idProveedor){
        return this.proveedorModel.getProveedor(idProveedor);

    }
}