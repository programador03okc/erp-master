// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () { scrollFunction() };

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("btnVolverArriba").style.display = "block";
    } else {
        document.getElementById("btnVolverArriba").style.display = "none";
    }

}

// When the user clicks on the button, scroll to the top of the document
function scrollToTheTopOfDocument() {

    $('html, body').animate({ scrollTop: 0 }, 'slow');
}