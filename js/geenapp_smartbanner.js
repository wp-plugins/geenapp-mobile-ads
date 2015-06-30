var GeenappBanner = function (imageUrl, clickUrl){    
    GeenappBanner.prototype.imageUrl = imageUrl; 
    GeenappBanner.prototype.clickUrl = clickUrl;
	  GeenappBanner.prototype.adDimensions();	  
};
 
/*
   Detect the the device width and height, and  chose a 
   a ad format according to the device dimensions    
*/
GeenappBanner.prototype.adDimensions = function(){ 
		var w         =  screen.width; 	        	
		 
		switch (true) {
		  case ( w  <= 480):
		  GeenappBanner.prototype.iab    = 'ad320x50';	
		  GeenappBanner.prototype.width  = '320';
		  GeenappBanner.prototype.height = '50';
		 break;
		  case ( w  >= 480 &&  w  <= 768):
		  GeenappBanner.prototype.iab    = 'ad480x80';
		  GeenappBanner.prototype.width  = '480';
		  GeenappBanner.prototype.height = '80';
		 break;
		 case ( w  >= 768):
		   GeenappBanner.prototype.iab    = 'ad768x90';
		   GeenappBanner.prototype.width  = '768';
		   GeenappBanner.prototype.height = '90';		   
		 break;
		  default:
		   GeenappBanner.prototype.iab    = 'ad320x50';
		   GeenappBanner.prototype.width  = '320';
		   GeenappBanner.prototype.height = '50';
		  break;
		}
};

/*
  Enabale the aperence of the smart banner
*/
GeenappBanner.prototype.enableAdd = function(position){	      
	  window.onload = function(){       
		   GeenappBanner.prototype.printAd(position, GeenappBanner.prototype.imageUrl, GeenappBanner.prototype.clickUrl, GeenappBanner.prototype.width, GeenappBanner.prototype.height);
	  };  	   
	   
	  var supportsOrientationChange = "onorientationchange" in window,
			orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";

	  window.addEventListener(orientationEvent, function() {
        
       GeenappBanner.prototype.adDimensions();
		    if (document.getElementById("GeenappBanner")){
                document.getElementById("GeenappBanner").style.display = 'none';		 	     
			       GeenappBanner.prototype.printAd(position, GeenappBanner.prototype.imageUrl, GeenappBanner.prototype.clickUrl, GeenappBanner.prototype.width, GeenappBanner.prototype.height);
        }
	  }, false);   
      	  
};

/*
  Printing the ad on the screen
*/
GeenappBanner.prototype.printAd = function (loc, imageUrl, clickUrl, width, height){        
						
            var GeenappBanner  = document.getElementById('GeenappBanner');
            if (!GeenappBanner){
               var GeenappBanner = document.createElement("div");
               GeenappBanner.id = "GeenappBanner";		
            } 
            				 
						 
						GeenappBanner.style.display = 'none';
            	 
            if (loc == 'top') {                                         
               GeenappBanner.style.top     = '0';
						   GeenappBanner.innerHTML = "<a id='close' style='top:5px;' onclick='geenappClose();' href='#' class='sb-close'>&#9733;</a><a href='" + clickUrl  + "'> <img onload='GeenappBanner.prototype.enableImage(&#39;top&#39;," + height  +   ");'   width=" +  width  + " height=" + height + " style='display: block;margin-left: auto;margin-right: auto'   src='" + imageUrl  + "' /></a>";
            } else {               
              GeenappBanner.style.bottom = '0';
              GeenappBanner.innerHTML = "<a id='close' style='bottom:5px;' onclick='geenappClose();' href='#' class='sb-close'>&#9733;</a><a href='" + clickUrl  + "'> <img onload='GeenappBanner.prototype.enableImage(&#39;bottom&#39;," + height  +   ");'   width=" +  width  + " height=" + height + " style='display: block;margin-left: auto;margin-right: auto'   src='" + imageUrl  + "' /></a>";
            }
						document.body.appendChild(GeenappBanner);      	
};


/*
  Construct again the url upon the user screen width
*/
GeenappBanner.prototype._imageUrl = function (){             
   var arrImageUrll =  GeenappBanner.prototype.imageUrl.split("/");           
   arrImageUrll[arrImageUrll.length-3] = GeenappBanner.prototype.iab;
   GeenappBanner.prototype.imageUrl =  arrImageUrll.join("/");	 	 
};

GeenappBanner.prototype._printAd = function ( ){
    var imageUrl = GeenappBanner.prototype.imageUrl;
    var height   = GeenappBanner.prototype.height;
    var width    =  GeenappBanner.prototype.width; 
      
	      var img = document.getElementById("imgBanner");           
			 	if (img){
        		 img.src = imageUrl;              
             img.height =  height;
             img.width  =  width;                         
         }   
       
};

/*
  GeenappBanner.prototype function is called when the image finish to download
*/
GeenappBanner.prototype.enableImage = function (loc, height){
        var body = document.getElementsByTagName("BODY")[0];
       if (loc == 'top'){
           body.style.marginTop = height + 'px'; 
       } else {
           body.style.marginBottom = height + 'px'; 
       }
       var GeenappBanner = document.getElementById("GeenappBanner");
         if (GeenappBanner){  
             GeenappBanner.style.display  = 'inline';	         
       }
};

function geenappClose(){	
  
  document.getElementById('GeenappBanner').style.display = 'none'; 
	    
	 document.body.style.marginTop = '0px';  
	  document.body.style.marginBottom = '0px'; 
	  
     
}



 

 
  

 