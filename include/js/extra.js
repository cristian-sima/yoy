var st={};
st["dialog"] = "";

function confirmRequest(msg, address){

	if(confirm(msg))
		document.location = address;

}

function focus(id){

	$(document).ready(function (){
		$("#"+id).focus();
	});

}


/**
 *
 * 	@description Show a notification message (top). It will be removed after 5 seconds
 * 	@param The obj with all the information: the type of the message ("Error","Succes"), the message, the id of the object that will be focused (maybe where the error occured
 *
 */

function showDialog(data) {

	var dialogDIV = $("#dialog");

	dialogDIV.slideDown("fast");

	clearTimeout(st["dialog"]);
	st["dialog"] = setTimeout(function () {
		dialogDIV.slideUp("fast");
	}, 5000);

	if (data.type == "error") {
		dialogDIV.css({
			"background": "rgb(255, 176, 31)",
			"border": "1px solid rgb(189, 122, 0)"
		});
		dialogDIV.html("<img src='img/icons/alert.png' align='left'/> " + data.message);
		if (data.focus != undefined) $("#" + data.focus).focus();
	} else {

		if (data.type == "succes") {
			dialogDIV.css({
				"background": "rgb(161, 241, 154)",
				"border": "1px solid rgb(57, 160, 42)"
			});
			dialogDIV.html("<img src='img/icons/succes.png' align='left'/> " + data.message);
			if (data.focus != undefined) $("#" + data.focus).focus();

		}
	}

}


/**
 *
 *	@description It close the dialog.
 *
 */

function closeDialog() {
	clearTimeout(st["dialog"]);
	$("#dialog").hide();
}



/**
 *
 * @description It checks if a number has only digits
 * @returns True if the number has all the characters digits, otherwise false
 *
 */

function isNumeric(input) {
	return (input - 0) == input && (input + '').replace(/^\s+|\s+$/g, "").length > 0;
}


/**
 *
 * 	@description It checks a formular to be correct
 * 	@param form The id of the form. For fix size use or minSize or maxSize
 *	Possible
 *  The element should have the check attribute true
 *	The criteria attribute is an object with
 *	- type [numeric, string, date] . [Required]
 *	- fixSize The size of the element [Optional]
 *	- minSize The element should have at least... [Optional]
 *  - maxSize The element should not exceed ... [Optional]
 *  # dateFormat [ANUL/LN]- Required for a date element [Required for date]
 *  # alphaOnly - only for strings. It check if a string has no digits. [Optional]
 */

function checkForm(formID) {

  // close the dialog
	closeDialog();
	 

	var gresealaGasita = false;

	$("form#" + formID + " :input").each(function () {
		if (gresealaGasita) return false;
		var input = $(this);
		var valoare = input.val();
		if (input.attr("check") == "true") {
			var criteria = {};
			eval("var criteria= " + input.attr("criteria"));
			
			// regex 
			
			if(criteria.empty != undefined && valoare=="")
				return;
			
			if(criteria.reg != undefined){
			
				var re = /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
					if(! re.test(valoare)) {
					
						
						showDialog({
							type: "error",
							message: "Parola trebuie sa contina cel putin <br />&nbsp;- un caracter majuscul<br /> &nbsp;&nbsp;&nbsp;- o litera mica<br />&nbsp;&nbsp;&nbsp;- un numar sau un caracter special<br />&nbsp;&nbsp;&nbsp;- cel putin 8 caractere",
							focus: input.attr("id")
						});
						gresealaGasita = true;
						return false;
					}
					
			}
			
			
			if (criteria.minSize != undefined) {
				if (valoare.length < criteria.minSize) {
					showDialog({
						type: "error",
						message: "Campul <b>" + input.attr("name") + "</b> trebuie sa contina minim " + criteria.minSize + " caractere !",
						focus: input.attr("id")
					});
					gresealaGasita = true;
					return false;
				}
			}

			if (criteria.maxSize != undefined) {
				if (valoare.length > criteria.maxSize) {
					showDialog({
						type: "error",
						message: "Campul <b>" + input.attr("name") + "</b> trebuie sa contina maxim " + criteria.maxSize + " caractere !",
						focus: input.attr("id")

					});
					gresealaGasita = true;
					return false;
				}
			}

			if (criteria.fixSize != undefined) {
				if (valoare.length != criteria.fixSize) {
					showDialog({
						type: "error",
						message: "Campul <b>" + input.attr("name") + "</b> are exact " + criteria.fixSize + " caractere !",
						focus: input.attr("id")
					});
					gresealaGasita = true;
					return false;
				}
			}

			switch (criteria.type) {

			case "numeric":
				if (valoare != '' && !isNumeric(valoare)) {
					showDialog({
						type: "error",
						message: "Campul <b>" + input.attr("name") + "</b> trebuie sa contina numai cifre !",
						focus: input.attr("id")
					});
					gresealaGasita = true;
					return false;
				}
				break;

			case "string":
				// maybe no validation
				if (criteria.alphaOnly != undefined) {

					if (!isAlphaOrParen(valoare)) {
						showDialog({
							type: "error",
							message: "Campul <b>" + input.attr("name") + "</b> trebuie sa contina doar litere ÃÅ¸i spatii !",
							focus: input.attr("id")
						});
						gresealaGasita = true;
						return false;
					}

				}

				break;


			case "date":
				// if it is not provided
				criteria.dateFormat = input.attr("dateFormat");
				if (valoare == criteria.dateFormat) {
					showDialog({
						type: "error",
						message: "Campul <b>" + input.attr("name") + "</b> nu a fost completat !",
						focus: input.attr("id")
					});
					gresealaGasita = true;
					return false;
				}

				// check it is the right one
				switch (criteria.dateFormat) {
				case "yyyy/mm":
					var date = valoare.split("/");
					if (date[0].length != 4 || (!isNumeric(date[0])) || (!isNumeric(date[1])) || parseInt(date[0]) < 1900 || parseInt(date[0]) > 2100 || parseInt(date[1]) < 1 || parseInt(date[1]) > 12) {
						showDialog({
							type: "error",
							message: "Campul <b>" + input.attr("name") + "</b> nu are un format corect pentru o data. Formatul acceptat este <b> ANUL/LUNA</span>",
							focus: input.attr("id")
						});
						gresealaGasita = true;
						return false;
					}
					break;
				}

				break;
			}
		}
	});

	if (!gresealaGasita) 
		$("#"+formID).submit();
	else
		return false;

}