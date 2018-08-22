i=0;
		function onloadimage()
		{
			document.getElementById("img").src = link[i];
		}
		len=link.length-1;
		function  next()	
		{	
			if(i>=len)
			{
				i=0;
			}
			else
			{
				i++;
			}	
			document.getElementById("img").src = link[i];
        
		}
      //  setInterval(next,3000);
		function previous()
		{
			if(i <= 0)
			{
				i=len;
			}
			else
			{
				i--;
			}
			document.getElementById("img").src = link[i];
		}