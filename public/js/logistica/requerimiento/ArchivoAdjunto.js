class ArchivoAdjunto {

    constructor(file) {
        this.file = file[0];
    }

    getType() {
        return this.file.type;
    }

    getSize() {
        return this.file.size;
    }

    getName() {
        return this.file.name;
    }

    isAllowedFile() {
        let extension = this.getName().match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
        if (extension === 'dwg'
            || extension === 'dwt'
            || extension === 'cdr'
            || extension === 'back'
            || extension === 'backup'
            || extension === 'psd'
            || extension === 'sql'
            || extension === 'exe'
            || extension === 'html'
            || extension === 'js'
            || extension === 'php'
            || extension === 'ai'
            || extension === 'mp4'
            || extension === 'mp3'
            || extension === 'avi'
            || extension === 'mkv'
            || extension === 'flv'
            || extension === 'mov'
            || extension === 'wmv'
        ) {
            return false;
        } else {
            return true;
        }
    }

    addToTablaArchivosRequerimiento(id, nameFile) {

        requerimientoCtrl.getcategoriaAdjunto().then((res) => {
            this.construirRegistroEnTablaAdjuntosRequerimiento(id, nameFile, res);

        }).catch(function (err) {
            console.log(err)
        })

    }

    construirRegistroEnTablaAdjuntosRequerimiento(id, nameFile, data) {
        let html = '';
        html = `<tr id="${id}" style="text-align:center">
        <td style="text-align:left;">${nameFile}</td>
        <td>
            <select class="form-control" name="categoriaAdjunto" onChange="ArchivoAdjunto.changeCategoriaAdjunto(this)">
        `;
        data.forEach(element => {
            html += `<option value="${element.id_categoria_adjunto}">${element.descripcion}</option>`
        });
        html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoRequerimiento" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoRequerimiento(this,'${id}');" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html);

    }

    static changeCategoriaAdjunto(obj) {
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoList.findIndex(elemnt => elemnt.id === obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoList[indice].category = obj.value;
        } else {
            alert("Hubo un error inesperado en la lista de adjuntos por requerimiento, la cantidad de adjuntos es cero");
        }
    }


    addToTablaArchivosItem(id, nameFile) {

        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', `<tr id="${id}" style="text-align:center">
        <td  style="text-align:left;">${nameFile}</td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoItem" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoItem(this,'${id}');" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>
        `);
    }

    static descargarArchivoRequerimiento(id) {
        console.log('descargarArchivoRequerimiento');
        console.log(id);
        console.log(tempArchivoAdjuntoRequerimientoList);
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            tempArchivoAdjuntoRequerimientoList.forEach(element => {
                if (element.id == id) {
                    console.log("/files/logistica/requerimiento/" + element.nameFile);
                    window.open("/files/logistica/requerimiento/" + element.nameFile);
                }
            });
        }
    }
    static eliminarArchivoRequerimiento(obj, id) {
        // console.log('eliminar archivo ' + idRegister + nameFile);
        obj.closest("tr").remove();
        tempArchivoAdjuntoRequerimientoList = tempArchivoAdjuntoRequerimientoList.filter((element, i) => element.id != id);
        ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();
    }

    static eliminarArchivoItem(obj, id) {
        obj.closest("tr").remove();
        tempArchivoAdjuntoItemList = tempArchivoAdjuntoItemList.filter((element, i) => element.id != id);
        ArchivoAdjunto.updateContadorTotalAdjuntosPorItem();
    }

    static descargarArchivoItem(id) {
        console.log('descargarArchivoItem');
        if (tempArchivoAdjuntoItemList.length > 0) {
            tempArchivoAdjuntoItemList.forEach(element => {
                if (element.id == id) {
                    window.open("/files/logistica/detalle_requerimiento/" + element.nameFile);
                }
            });
        }
    }

    static updateContadorTotalAdjuntosRequerimiento() {

        document.querySelector("span[name='cantidadAdjuntosRequerimiento']").textContent = tempArchivoAdjuntoRequerimientoList.length;
    }

    static updateContadorTotalAdjuntosPorItem() {
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let i = 0; i < tbodyChildren.length; i++) {
            if (tempArchivoAdjuntoItemList.length > 0) {
                for (let j = 0; j < tempArchivoAdjuntoItemList.length; j++) {
                    // if(tbodyChildren[i].querySelector("input[class~='idRegister']").value == tempArchivoAdjuntoItemList.idRegister){
                    const cantidad = tempArchivoAdjuntoItemList.filter(function (element) { return element.idRegister == tbodyChildren[i].querySelector("input[class~='idRegister']").value; }).length;
                    tbodyChildren[i].querySelector("span[name='cantidadAdjuntosItem']").textContent = cantidad;
                    // } 

                }

            } else {
                tbodyChildren[i].querySelector("span[name='cantidadAdjuntosItem']").textContent = 0;
            }

        }

    }


    addFileLevelRequerimiento() {
        if (this.isAllowedFile() == true) {

            const nameFile = this.getName();
            const typeFile = this.getType();
            const sizeFile = this.getSize();
            const id = requerimientoView.makeId();
            tempArchivoAdjuntoRequerimientoList.push({
                id: id,
                category: 1, //default
                nameFile: nameFile,
                typeFile: typeFile,
                sizeFile: sizeFile,
                file: this.file
            });

            ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();
            this.addToTablaArchivosRequerimiento(id, nameFile);

        } else {
            alert(`La extensión del archivo .${typeFile} no esta permitido`);
        }
        return false;
    }

    addFileLevelItem() {
        if (this.isAllowedFile() == true) {

            const nameFile = this.getName();
            const typeFile = this.getType();
            const sizeFile = this.getSize();
            const id = requerimientoView.makeId();

            tempArchivoAdjuntoItemList.push({
                id: id,
                idRegister: tempIdRegisterActive,
                nameFile: nameFile,
                typeFile: typeFile,
                sizeFile: sizeFile,
                file: this.file
            });

            ArchivoAdjunto.updateContadorTotalAdjuntosPorItem();
            this.addToTablaArchivosItem(id, nameFile)

        } else {
            alert(`La extensión del archivo .${typeFile} no esta permitido`);
        }
        return false;
    }
    // doUpload(){
    //     let formData = new FormData();
    //     formData.append("file", this.file, this.getName());

    // }
}
