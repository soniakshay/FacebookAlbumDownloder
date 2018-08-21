//open new window for slideshow image
            function openNewWindow(accesstoken,id)
			{
				str="imageslideshow.php?id="+id;
				window.open(str, "_blank","height=340px width=600px");

			}
            //create zip for one album
			function createzip(id) {
                document.getElementById("lodingdiv").style.display="block";
                document.getElementById("sts").style.display="block";
                document.getElementById("demo").style.display="none";
                    
                var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
                    if(this.responseText!="false")
                    {
                        document.getElementById("sts").style.display="none";
                        document.getElementById("demo").style.display="block";
                        document.getElementById("demo").href=this.responseText; 
                    }
                    else
                    {
                            document.getElementById("sts").innerHTML="Something Error";
                    
                    }
                }
			  };
				xhttp.open("GET", "createalbumzip.php?albumid=" + id, true);
				xhttp.send();
			}
            
            //checkitem contain collect all chcked album albumid
            var checkitem = new Array();
            function checkuncheck(a) 
            {

                if(a.checked==false)
                {
                        const index = checkitem.indexOf(a.value);
                        checkitem.splice(index, 1);
	           }
                else
                {	
		
                        checkitem.push(a.value);
	           }

            }
            //this function create for selected album
            function createzipwithseletedalbum()
            {
                document.getElementById("lodingdiv").style.display="block";
                document.getElementById("sts").style.display="block";
                document.getElementById("demo").style.display="none";
                    
                var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
                        if(this.responseText!="false")
                        {
                            document.getElementById("sts").style.display="none";
                            document.getElementById("demo").style.display="block";
                            document.getElementById("demo").href=this.responseText; 
                        }
                        else
                        {
                                document.getElementById("sts").innerHTML="Something Error";

                        }
                    }
                };
				xhttp.open("GET", "createalbumzip.php?albumid=" + checkitem.toString(), true);
				xhttp.send();
                    
            }
            // this function create for downloud all album 
            function createzipwithallalbum()
            {
                allalbumid= '<?php echo $allalbumid ;?>';
                
                document.getElementById("lodingdiv").style.display="block";
                document.getElementById("sts").style.display="block";
                document.getElementById("demo").style.display="none";
                    
                var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
                        if(this.responseText!="false")
                        {
                            document.getElementById("sts").style.display="none";
                            document.getElementById("demo").style.display="block";
                            document.getElementById("demo").href=this.responseText; 
                        }
                        else
                        {
                                document.getElementById("sts").innerHTML="Something Error";

                        }
                    }
                };
				xhttp.open("GET", "createalbumzip.php?albumid=" + allalbumid, true);
				xhttp.send();
                  
            }
            //drive uplode alertbox hide 
            function alertboxoff()	
            {	
               document.getElementById("uplodestatus").style.display="none";

            }
          setInterval(alertboxoff,5000);
