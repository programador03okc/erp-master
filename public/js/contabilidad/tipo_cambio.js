// var loIE=createobject("InternetExplorer.Application") 
// loIE.visible=.T. 
// loIE.navigate("http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias") 
// do while loIE.readystate<>4 
//         wait window "Esperando respuesta..." timeout 1 
// enddo 
// do while type("loIE.document.body.innerhtml")<>"C" 
//         *loop till it's a character... sometimes it's just not quite ready 
// enddo 
// *lcHTML=loIE.document.body.innerhtml 
// lcHTML=loIE.document.body.innerText 
// strtofile(lcHTML,"temp.txt") 
// modify command temp.txt 