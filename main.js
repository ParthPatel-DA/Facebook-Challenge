
    var varTimeLine = document.getElementById("TimeLine");
    var varFollower = document.getElementById("Follower");
    var cnt=0;
    var divShareBox = document.getElementById("ShareBox");

    varFollower.style.display = "block";
    
    // apply css design for select & deselect checkbox and visible button of "Upload Selected Album" & "Download Selected Album"

    function checkAlbum(element) {
        
        var a = element;
        if(a.checked) {
            var x = element.closest(".checkbox").closest("#divAlbum");
            x.style.background = "#4267b2";
            x.style.color = "#fff";
            for (var i = 0; i < x.childNodes.length; i++) {
                if (x.childNodes[i].className == "imgCount") {
                    
                    notes = x.childNodes[i];
                    notes.style.backgroundColor = "#fff";
                    notes.style.color = "#4267b2";
                }
                if (x.childNodes[i].className == "imgGD") {
                    
                    notes = x.childNodes[i];
                    notes.style.backgroundColor = "#fff";
                    notes.style.color = "#4267b2";
                }
            }
            element.closest(".checkbox").style.backgroundColor = "#fff";
            element.closest(".checkbox").style.color = "#4267b2";
            cnt+=1;
        }
        else {
            var x = element.closest(".checkbox").closest("#divAlbum");
            x.style.background = "#fff";
            x.style.color = "#4267b2";
            for (var i = 0; i < x.childNodes.length; i++) {
                if (x.childNodes[i].className == "imgCount") {
                    
                    notes = x.childNodes[i];
                    notes.style.backgroundColor = "#657786";
                    notes.style.color = "#fff";
                }    
                if (x.childNodes[i].className == "imgGD") {
                    
                    notes = x.childNodes[i];
                    notes.style.backgroundColor = "#657786";
                    notes.style.color = "#fff";
                }      
            }
            element.closest(".checkbox").style.backgroundColor = "#657786";
            element.closest(".checkbox").style.color = "#657786";
            cnt-=1;
        }

        if(cnt > 0){
            divShareBox.style.display = "block";
        }
        else {
            divShareBox.style.display = "none";
        }
    }
