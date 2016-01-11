window.onload = function(){
    if(document.getElementById('smartideo_tips')){
        var tips = document.getElementById("smartideo_tips");
        tips.innerHTML = tips.getAttribute("smartideo-title");
    }
}