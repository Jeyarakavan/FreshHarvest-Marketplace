
window.addEventListener("load", function () {
    var loader = document.getElementById("preloader");

   
    setTimeout(function () {
        
        loader.style.opacity = "0";

       
        setTimeout(function () {
            loader.style.display = "none";
        }, 500); 
    }, 1000);
});



