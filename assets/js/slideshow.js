var imagelink;
		var imagecount=0;
		function loadslideshow(id) {
			document.getElementById("imagepopup").style.display="block";
			document.getElementById("lodingicon").style.display="block";
							 
			 var xhttp = new XMLHttpRequest();
			  xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					imagelink=null;
					document.getElementById("lodingicon").style.display="none";
					imagelink=JSON.parse(this.responseText);
					imagecount=imagelink.length-1;
					if(imagelink!=null)
					{
						document.getElementById("imageslideshow").src=imagelink[0];
						document.getElementById("imageslideshow").style.display="block";
					}
				}
			  };

			  xhttp.open("GET", "imageslideshow.php?id=" + id , true);
			  xhttp.send();
		}
		function onloadslideshowimage()
		{
				

		}
		var i=0;
		function  next()	
		{	
			if(i>=imagecount)
			{
				i=0;
				document.getElementById("imageslideshow").src=imagelink[i];
	
			}
			else
			{
				i++;
				document.getElementById("imageslideshow").src=imagelink[i];
	
			}	
			
		}
		function previous()
		{
			if(i <= 0)
			{
				i=imagecount;
				document.getElementById("imageslideshow").src=imagelink[i];
	
			}
			else
			{
				i--;
				document.getElementById("imageslideshow").src=imagelink[i];
	
			}
		}
		function autoslideshow()
		{
			setInterval(next,3000);
			
		}
		
		function closepopup()
		{
					imagelink=null;
					document.getElementById("imageslideshow").style.display="none";
					document.getElementById("imagepopup").style.display="none";
		}
