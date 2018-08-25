
    var modelBG = document.getElementById("model-background");
    var model = document.getElementById("model");
    function cancelFullScreen() {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        } else if (document.msCancelFullScreen) {
            document.msCancelFullScreen();
        }
        var varTimeLine = document.getElementById("TimeLine");
        varTimeLine.style.display = "none";
    }
    
    

    function fullScreen(element) {
        Model();
        var x = element.parentElement;
        var node = "";
        for (var i = 0; i < x.childNodes.length; i++) {
            if (x.childNodes[i].className == "albumID") {
                node = x.childNodes[i];
            }
        }

        var elementcontainer = document.getElementById("container");
        if (elementcontainer.requestFullScreen) {
            elementcontainer.requestFullScreen();
        } else if (elementcontainer.webkitRequestFullScreen) {
            elementcontainer.webkitRequestFullScreen();
        } else if (elementcontainer.mozRequestFullScreen) {
            elementcontainer.mozRequestFullScreen();
        }
    
        elementcontainer.setAttribute("onclick", "cancelFullScreen()");
        var varTimeLine = document.getElementById("TimeLine");
        var y = document.getElementById("slideshow");
        // y.innerHTML = "";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if(xmlhttp.responseText=="true"){
                    // if(this.responseText){
                        // modelBG.style.display = "none";
                        // model.style.display = "none";
                        alert(this.responseText);
                    // }
                    // y.innerHTML = this.responseText;
                    // modelBG.style.display = "none";
                    // model.style.display = "none";
                    // varTimeLine.style.display = "block";
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?slidealbumid="+node.value.trim(), true);
        xmlhttp.send();
    }

    function DownloadSingle(element){
        ModelDownload();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var result = xmlhttp.responseText;
                var resultArray = result.split("_");
                if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                    modelBG.style.display = "none";
                    model.style.display = "none";
                    window.open(resultArray[1], '_blank');
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadsingle="+element, true);
        xmlhttp.send();
    }

    function DownloadAll(){
        ModelDownload();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var result = xmlhttp.responseText;
                var resultArray = result.split("_");
                if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                    for (i = 1; i < resultArray.length; i++) {
                        window.open(resultArray[i], '_blank');
                    }
                    modelBG.style.display = "none";
                    model.style.display = "none";
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadall=all", true);
        xmlhttp.send();
    }

    function DownloadSelected(){
        ModelDownload();
        var albumID="";
        var check = document.getElementById('row').querySelectorAll('input[type=checkbox]:checked');
        for(var i = 0; i < check.length; i++){
            albumID+=check[i].value+"/";
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var result = xmlhttp.responseText;
                var resultArray = result.split("_");
                if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                    for (i = 1; i < resultArray.length; i++) {
                        window.open(resultArray[i], '_blank');
                    }
                    modelBG.style.display = "none";
                    model.style.display = "none";
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadselected="+albumID, true);
        xmlhttp.send();
    }
    
    function UploadToDriveSingle(albumid, name) {
        ModelUpload();
        if(getCookie('credentials') == "") {
            GoogleModel();
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if(xmlhttp.responseText=="true"){
                        modelBG.style.display = "none";
                        model.style.display = "none";
                        alert("Album Successfully Uploaded.");
                    }
                    
                }
            }   
            xmlhttp.open("GET", "upload-album.php?uploadAlbum=" + albumid + "&albumName=" + name, true);
            xmlhttp.send(null);
        }
    }

    function UploadAll(){
        ModelUpload();
        var albumID="";
        var check = document.getElementById('row').querySelectorAll('input[type=checkbox]');
        for(var i = 0; i < check.length; i++){
            albumID+=check[i].value+"/";
        }
        if(getCookie('credentials') == "") {
            GoogleModel();
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if(xmlhttp.responseText=="true"){
                        modelBG.style.display = "none";
                        model.style.display = "none";
                        alert("Albums Successfully Uploaded.");
                    }
                }
            }
            xmlhttp.open("GET", "upload-album.php?uploadAlbums=" + albumID, true);
            xmlhttp.send();
        }
    }

    function UploadSelected(){
        ModelUpload();
        var albumID="";
        var check = document.getElementById('row').querySelectorAll('input[type=checkbox]:checked');
        for(var i = 0; i < check.length; i++){
            albumID+=check[i].value+"/";
        }
        if(getCookie('credentials') == "") {
            GoogleModel();
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if(xmlhttp.responseText=="true"){
                        modelBG.style.display = "none";
                        model.style.display = "none";
                        alert("Albums Successfully Uploaded.");
                    }
                }
            }
            xmlhttp.open("GET", "upload-album.php?uploadAlbums=" + albumID, true);
            xmlhttp.send();
        }
    }

    function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

    window.onload = function() {
      imgs = document.getElementById('slideshow').children;
      interval = 8000;
      currentPic = 0;
      imgs[currentPic].style.webkitAnimation = 'fadey '+interval+'ms';
      imgs[currentPic].style.animation = 'fadey '+interval+'ms';
      var infiniteLoop = setInterval(function(){
        imgs[currentPic].removeAttribute('style');
        if ( currentPic == imgs.length - 1) { currentPic = 0; } else { currentPic++; }
        imgs[currentPic].style.webkitAnimation = 'fadey '+interval+'ms';
        imgs[currentPic].style.animation = 'fadey '+interval+'ms';
      }, interval);
    }

    function GoogleModel(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "none";
        document.getElementById("modelDes").innerHTML = "Google Login";
        document.getElementById("modelMsg").style.display = "none";
        document.getElementById("modelGoogleLogin").style.display = "block";
    }

    function ModelDownload(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "block";
        document.getElementById("modelDes").innerHTML = "Please wait while downloading Albums";
        document.getElementById("modelMsg").style.display = "block";
        document.getElementById("modelGoogleLogin").style.display = "none";
    }

    function ModelUpload(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "block";
        document.getElementById("modelDes").innerHTML = "Please wait while uploading Albums";
        document.getElementById("modelMsg").style.display = "block";
        document.getElementById("modelGoogleLogin").style.display = "none";
    }

    function Model(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "block";
        document.getElementById("modelDes").innerHTML = "Please wait while feaching Albums";
        document.getElementById("modelMsg").style.display = "block";
        document.getElementById("modelGoogleLogin").style.display = "none";
    }