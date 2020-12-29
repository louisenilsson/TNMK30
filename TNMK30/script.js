function themeswitch() {
	var activated = document.getElementById("themetoggle").checked;	
    //Om man vill ha mörkt
	if(activated) {
		document.body.style.backgroundColor = "#2c3a51";
		document.body.style.color = "white";
		
		var menu = document.getElementsByClassName("menu");
    		for (var i = 0; i < menu.length; i++) {
        		menu[i].style.backgroundColor="#333333";
        	}

        var tablediv = document.getElementsByClassName("tablediv");
    		for (var i = 0; i < tablediv.length; i++) {
        		tablediv[i].style.backgroundColor="#333333";
        		tablediv[i].style.color="white";
        	}

        var legotable = document.getElementsByClassName("legotable");
            for (var i = 0; i < legotable.length; i++) {
                legotable[i].style.color="white";
            }

        var a = document.getElementsByTagName("a");
    		for (var i = 0; i < a.length; i++) {
        		a[i].style.color="white";
        	}

        var th = document.getElementsByTagName("th");
    		for (var i = 0; i < th.length; i++) {
        		th[i].style.backgroundColor="#333333";
                th[i].style.boxShadow="0 -.4vw 0 0 #006bb7 inset";
        	}

        var pagebutton = document.getElementsByClassName("pagebutton");
            for (var i = 0; i < pagebutton.length; i++) {
                pagebutton[i].style.backgroundColor="#5b5c60";
                pagebutton[i].style.borderColor="#6e6f72";
            }
    //Om man vill ha det ljust
	} else {
		document.body.style.backgroundColor = "#fffccc";
		document.body.style.color = "black";
		
		var menu = document.getElementsByClassName("menu");
    		for (var i = 0; i < menu.length; i++) {
        		menu[i].style.backgroundColor="#E04F3C";
        	}

        var tablediv = document.getElementsByClassName("tablediv");
    		for (var i = 0; i < tablediv.length; i++) {
        		tablediv[i].style.backgroundColor="white";
        		tablediv[i].style.color="black";
        	}

        var legotable = document.getElementsByClassName("legotable");
            for (var i = 0; i < legotable.length; i++) {
                legotable[i].style.color="black";
            }

        var a = document.getElementsByTagName("a");
    		for (var i = 0; i < a.length; i++) {
        		a[i].style.color="black";
        	}

                var th = document.getElementsByTagName("th");
    		for (var i = 0; i < th.length; i++) {
        		th[i].style.backgroundColor="white";
                th[i].style.boxShadow="0 -.4vw 0 0 #E04F3C inset";
        	}

        var pagebutton = document.getElementsByClassName("pagebutton");
        for (var i = 0; i < pagebutton.length; i++) {
            pagebutton[i].style.backgroundColor="#d2d4d8";
            pagebutton[i].style.borderColor="#b5b8bf";
            }
	}

}