
    var modelBG = document.getElementById("model-background");
    var model = document.getElementById("model");
    var modelSlider = document.getElementById("model-slider");

    // close Full Screen slider

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
    
    // End - close Full Screen slider


    // ajax call for get slider's images

    function fullScreen(element) {
        Model();
        var x = element.parentElement;
        var node = "";
        for (var i = 0; i < x.childNodes.length; i++) {
            if (x.childNodes[i].className == "albumID") {
                node = x.childNodes[i];
            }
        }

        var y = document.getElementById("slideshow");
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4){
                if(this.status == 200) {
                    if(this.responseText.search('true') != -1) {
                        var result = xmlhttp.responseText;
                        var resultArray = result.split("~");
                        if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            // alert(resultArray[1]);
                            y.innerHTML = resultArray[1];
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            ModelSlier();
                            var slider = new airSlider({
                                autoPlay: true,
                                width: '100%',
                                height: '100%'
                            });
                            // varTimeLine.style.display = "block";
                        }
                        else {
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            alert("Sorry, Couldn't fetch Images! Please try again after sometime.");
                        }
                    }
                    else {
                        modelBG.style.display = "none";
                        model.style.display = "none";
                        alert("Sorry, Couldn't fetch Images! Please try again after sometime.");
                    }
                }else{
                    modelBG.style.display = "none";
                    model.style.display = "none";
                    alert("Sorry, Couldn't fetch Images! Please try again after sometime.");
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?slidealbumid="+node.value.trim(), true);
        xmlhttp.send();
    }

    // End - ajax call for get slider's images


    // ajax call for Download zip of Single Album images

    function DownloadSingle(element){
        ModelDownload();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4){ 
                if(this.status == 200) {
                    if(this.responseText.search('true') != -1) {
                        var result = xmlhttp.responseText;
                        var resultArray = result.split("~");
                        if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            window.open(resultArray[1], '_blank');
                        }
                        else {
                            alert("Couldn't download albums! Please try again after sometime.");
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                    }
                    else {
                        alert("Couldn't download albums! Please try again after sometime.");
                        modelBG.style.display = "none";
                        model.style.display = "none";
                    }
                } else {
                    alert("Couldn't download albums! Please try again after sometime.");
                    modelBG.style.display = "none";
                    model.style.display = "none";
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadsingle="+element, true);
        xmlhttp.send();
    }

    // End - ajax call for Download zip of Single Album images


    // ajax call for Download zip of All Album images

    function DownloadAll(){
        ModelDownload();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4){ 
                if(this.status == 200) {
                    if(this.responseText.search('true') != -1) {
                        var result = xmlhttp.responseText;
                        var resultArray = result.split("~");
                        if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                            // for (i = 1; i < resultArray.length; i++) {
                                window.open(resultArray[1], '_blank');
                            // }
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                        else {
                            alert("Couldn't download albums! Please try again after sometime.");
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                    }
                    else {
                        alert("Couldn't download albums! Please try again after sometime.");
                        modelBG.style.display = "none";
                        model.style.display = "none";
                    }
                } else {
                    alert("Couldn't download albums! Please try again after sometime.");
                    modelBG.style.display = "none";
                    model.style.display = "none";
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadall=all", true);
        xmlhttp.send();
    }

    // End - ajax call for Download zip of All Album images

    // ajax call for Download zip of Selected Album images

    function DownloadSelected(){
        ModelDownload();
        var albumID="";
        var check = document.getElementById('row').querySelectorAll('input[type=checkbox]:checked');
        for(var i = 0; i < check.length; i++){
            albumID+=check[i].value+"/";
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4){ 
                if(this.status == 200) {
                    if(this.responseText.search('true') != -1) {
                        var result = xmlhttp.responseText;
                        var resultArray = result.split("~");
                        if(resultArray[0] === 'true' || resultArray[0].search('true') != -1) {
                            // for (i = 1; i < resultArray.length; i++) {
                                window.open(resultArray[1], '_blank');
                            // }
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                        else {
                            alert("Couldn't download albums! Please try again after sometime.");
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                    }
                    else {
                        alert("Couldn't download albums! Please try again after sometime.");
                        modelBG.style.display = "none";
                        model.style.display = "none";
                    }
                } else {
                    alert("Couldn't download albums! Please try again after sometime.");
                    modelBG.style.display = "none";
                    model.style.display = "none";
                }
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadselected="+albumID, true);
        xmlhttp.send();
    }

    // End - ajax call for Download zip of Selected Album images


    // ajax call for Upload Single Album images on Google Drive
    
    function UploadToDriveSingle(albumid, name) {
        ModelUpload();
        if(getCookie('credentials') == "") {
            GoogleModel();
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4) { 
                    if(this.status == 200) {
                        if(xmlhttp.responseText === 'true' || xmlhttp.responseText.search('true') != -1) {
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            alert("Album Successfully Uploaded.");
                        }
                        else {
                            alert("Couldn't upload albums! Please try again after sometime.");
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                    }
                    else {
                        alert("Couldn't upload albums! Please try again after sometime.");
                        modelBG.style.display = "none";
                        model.style.display = "none";
                    }
                }
            }   
            xmlhttp.open("GET", "upload-album.php?uploadAlbum=" + albumid + "&albumName=" + name, true);
            xmlhttp.send(null);
        }
    }

    // End - ajax call for Upload Single Album images on Google Drive


    // ajax call for Upload All Album images on Google Drive

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
                if (this.readyState == 4) { 
                    if(this.status == 200) {
                        if(xmlhttp.responseText === 'true' || xmlhttp.responseText.search('true') != -1) {
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            alert("Albums Successfully Uploaded.");
                        }
                        else {
                            alert("Couldn't upload albums! Please try again after sometime.");
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                    }
                    else {
                        alert("Couldn't upload albums! Please try again after sometime.");
                        modelBG.style.display = "none";
                        model.style.display = "none";
                    }
                }
            }
            xmlhttp.open("GET", "upload-album.php?uploadAlbums=" + albumID, true);
            xmlhttp.send();
        }
    }

    // End - ajax call for Upload All Album images on Google Drive


    // ajax call for Upload Selected Album images on Google Drive

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
                if (this.readyState == 4) { 
                    if(this.status == 200) {
                        if(xmlhttp.responseText === 'true' || xmlhttp.responseText.search('true') != -1) {
                            modelBG.style.display = "none";
                            model.style.display = "none";
                            alert("Albums Successfully Uploaded.");
                        }
                        else {
                            alert("Couldn't upload albums! Please try again after sometime.");
                            modelBG.style.display = "none";
                            model.style.display = "none";
                        }
                    } else {
                        alert("Couldn't upload albums! Please try again after sometime.");
                        modelBG.style.display = "none";
                        model.style.display = "none";
                    }
                }
            }
            xmlhttp.open("GET", "upload-album.php?uploadAlbums=" + albumID, true);
            xmlhttp.send();
        }
    }

    // End - ajax call for Upload Selected Album images on Google Drive


    // get Cookie value of given argument using javascript

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

    // End - get Cookie value of given argument using javascript


    // Open pop-up for google Login

    function GoogleModel(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "none";
        document.getElementById("modelDes").innerHTML = "Google Login";
        document.getElementById("modelMsg").style.display = "none";
        document.getElementById("modelGoogleLogin").style.display = "block";
    }

    // End - Open pop-up for google Login


    // Open pop-up for display Download process

    function ModelDownload(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "block";
        document.getElementById("modelDes").innerHTML = "Please wait while downloading Albums";
        document.getElementById("modelMsg").style.display = "block";
        document.getElementById("modelGoogleLogin").style.display = "none";
    }

    // End - Open pop-up for display Download process


    // Open pop-up for display Upload process

    function ModelUpload(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "block";
        document.getElementById("modelDes").innerHTML = "Please wait while uploading Albums";
        document.getElementById("modelMsg").style.display = "block";
        document.getElementById("modelGoogleLogin").style.display = "none";
    }

    // End - Open pop-up for display Upload process


    // ----

    function ModelSlier(){
        modelBG.style.display = "block";
        modelSlider.style.display = "block";
    }

    // ----


    // Open pop-up for display fa slider

    function Model(){
        modelBG.style.display = "block";
        model.style.display = "block";
        document.getElementById("modelspinner").style.display = "block";
        document.getElementById("modelDes").innerHTML = "Please wait while feaching Albums";
        document.getElementById("modelMsg").style.display = "block";
        document.getElementById("modelGoogleLogin").style.display = "none";
    }

    // End - Open pop-up for display slider process


    // close pop-up

    function CloseSilder(){
        modelBG.style.display = "none";
        modelSlider.style.display = "none";
    }

    // End - close pop-up