


function number_format (number, decimals, dec_point, thousands_sep) {

	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
					sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
							dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
									s = '',
									toFixedFix = function (n, prec) {
								var k = Math.pow(10, prec);
								return '' + Math.round(n * k) / k;
							};
							// Fix for IE parseFloat(0.55).toFixed(0) = 0;
							s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
							if (s[0].length > 3) {
								s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
							}
							if ((s[1] || '').length < prec) {
								s[1] = s[1] || '';

								s[1] += new Array(prec - s[1].length + 1).join('0');
							}
							return s.join(dec);
}

function cn()
{

	if($("#carnetNou").is(":checked") )
		$("#b1").removeAttr("disabled")
		else
		{

			$("#b1").val($("#inceputBilete").val());

			$("#b1").attr({
				"disabled":"disabled"
			})

		}
}


function checkValue(v1,v2,where,noShow){

	//console.log(v1+' '+v2);
	var noShow = noShow || 0;

	//console.log(v1+' cu '+v2+' la '+where);



	if(parseInt(v2) < parseInt(v1)){

		if(noShow!=1){
			$(where).css({
				"border":"1px solid red",
				"color":"white",
				"background":"red"
			});


			$(where).tooltip({ items: where, content: "Trebuie să fie mai mare sau egal decât cel anterior "});
			$(where).tooltip("open");

			$(where).tooltip({
				show: {
					effect: "shake",
					delay: 0
				},
				hide: {
					effect: "fade",
					delay: 0
				}
			});

		}
		$(".mod").attr({"disabled":'disabled'});
		$(".mod").addClass('disabled');

	}else
	{
		if(noShow!=1){
			$(where).css({
				"border":"1px solid green",
				"color":"white",
				"background":"green"
			});




		}

		$(where).tooltip({ hide: { effect: "fade", duration: 1000 } });

		$(where).tooltip("disable");
		//	$(where).remove();

		$(".mod").removeClass('disabled');
		activate();

	}
}


function checkBilet(b1,b2,where, noShow){

	if(b2 == ''){

		if(noShow != 1){

			$(where).css({
				"border":"1px solid red",
				"color":"white",
				"background":"red"
			});


			$(where).tooltip({ items: where, content: "Completați seria de bilete"});
			$(where).tooltip("open");

		}


		$(".mod").attr({"disabled":'disabled'});
		$(".mod").addClass('disabled');
		$(where).tooltip('disable');

		return;
	}

	var noShow = noShow || 0;

	b1 = b1+'';
	b2 = b2+'';

	if(b2.length != 5){

		if(noShow != 1){

			$(where).css({
				"border":"1px solid red",
				"color":"white",
				"background":"red"
			});


			$(where).tooltip({ items: where, content: "Seria trebuie să conțină cel puțin 5 caractere"});
			$(where).tooltip("open");



		}

		$(".mod").attr({"disabled":'disabled'});
		$(".mod").addClass('disabled');

		return;
	}


	if(parseInt(b1.substring(0,3)) == parseInt(b2.substring(0,3))){

		if(parseInt(b1.slice(-2)) > parseInt(b2.slice(-2))){

			if(noShow != 1){

				$(where).css({
					"border":"1px solid red",
					"color":"white",
					"background":"red"
				});


				$(where).tooltip({ items: where, content: "Seria de astăzi este mai mare decât cea de ieri "});
				$(where).tooltip("open");

				$(".mod").attr({"disabled":'disabled'});
				$(".mod").addClass('disabled');

			}


			$(".mod").attr({"disabled":'disabled'});
			$(".mod").addClass('disabled');

		}
		else
		{

			if(noShow!=1){
				$(where).css({
					"border":"1px solid green",
					"color":"white",
					"background":"green"
				});
				$(where).tooltip('disable');



			}


			$(".mod").removeClass('disabled');
			activate();

		}

	}else
	{

		if(noShow!=1){
			$(where).css({
				"border":"1px solid green",
				"color":"white",
				"background":"green"
			});




		}


		$(".mod").removeClass('disabled');
		activate();
	}

}
function getValueAt(row,c)
{

	var el = $($("#v_"+c+"_"+row)[0]);

	//	console.log("De la randul "+row+" cu index "+c+" este ")
	//	console.log(el);
	//	console.log("Are "+$(el[0]).children().length+" copii");
	if ( ($(el[0]).children().length) > 0 ) {
		//console.log(' are copil cu '+$(el.children()[0]).val());
		return ($($(el[0]).children()[0]).val());
	}
	else{

		return el.text();

	}
}

function setValueAt(row,c, value){

	var el = $("#v_"+c+"_"+row);

	if ( el.children().length > 0 ) {
		$(el.children[0]).val(value);
		//console.log('are');
	}else{

		el.html((value+"").replace(".", ","));

		if(value < 0){
			el.css({
				"color":"red"
			});
		}
		else
			el.css({
				"color":"black"

			});
		//console.log('a');
	}
}

function calculRow(e){

	//console.log(e);
	var c = {};

	c[2] 	= parseInt(getValueAt(e, 2));  // inceput mecanic
	c[2] = (c[2]=='')?0:(c[2]);
	c[4] 	= getValueAt(e, 4);  // inceput electronic
	c[5] 	= getValueAt(e, 5);  // sfarsit mecanic
	c[5] = (c[5]=='')?0:parseInt(c[5]);
	c[7]  = (getValueAt(e, 7));  // fact Mec inceput
	c[7] = (c[7]=='')?0:parseInt(c[7]);
	c[8]  = parseInt(getValueAt(e, 8));  // fact Mec inceput
	c[10] = parseInt(getValueAt(e, 10)); // fact EL sfarsit

	c[11] = ((c[5]-c[2])* c[8] <0)?0:((c[5]-c[2])* c[8]);
	c[13] = ((c[7]-c[4])*c[10] <0)?0:((c[7]-c[4])*c[10]);

	c[14] = c[11]-c[13];
	var v = getValueAt(e, 15)+"";

	v = v.replace(",", ".");
	//console.log(v);
	c[16] = parseInt(c[14])*(v);
	c[16] = c[16].toFixed(2);

	//	console.log(c);


	//console.log(c);
	for(value in c)
		setValueAt(e, value, c[value]);




}

function  activate(){
	$(".mod").css({
		"background": "rgb(121, 121, 255)",
		"color": "white"
	});

	$(".mod").removeAttr("disabled");


}

function newType(row, total){




	calculRow(row);

	//console.log(c);
	if(total)
		calculTotal();
}


function calculTotal(){

	if(intrari != 0){
		var t = {};

		t[2] = 0;
		t[4] = 0;
		t[5] = 0;
		t[7] = 0;
		t[11] = 0;
		t[14] = 0;
		t[13] = 0;
		t[16] = 0;
		t[18] = 0;
		t[17] = 0;
		t[19] = 0;
		for(var i=1;i<=intrari;i++){

			//get tr

			var r = i;


			//console.log(r);
			t[2]=t[2]+parseInt(getValueAt(r, 2));
			t[4]=t[4]+((getValueAt(r, 4)=='')?0:(parseInt(getValueAt(r, 4))));
			t[5]=t[5]+((getValueAt(r, 5)=='')?0:(parseInt(getValueAt(r, 5))));
			t[7]=t[7]+((getValueAt(r, 7)=='')?0:(parseInt(getValueAt(r, 7))));
			t[11]=t[11]+parseInt(getValueAt(r, 11));
			t[14]=t[14]+parseInt(getValueAt(r, 14));
			t[13]=t[13]+parseInt(getValueAt(r, 13));

			var v = getValueAt(r, 16)+"";

			v = v.replace(",", ".");


			t[16] =  parseFloat(t[16])+ parseFloat(v);
			//t[16] = t[16].toFixed(2);

			t[17] = t[17] + getValueAt(r, 11)*getValueAt(r, 15);
			t[19] = t[19] + getValueAt(r, 13)*getValueAt(r, 15);






		}


		t[18] = t[16];

		var v = getValueAt('total', 15)+"";
		v = v.replace(",", ".");


		//t[19] = t[13]*v;

		//t[17] = t[17].toFixed(2);
		//t[19] = t[19].toFixed(2);
		for(value in t)
			setValueAt("total", value, t[value]);

	}
}

var problems = 0;


function _completeaza(el){

	var rand = $(el).attr("name").charAt(1);
	var _temp = $(el).parent().attr("id");
	var split = _temp.split("_");
	var idCurent = split[1];

	//console.log(rand+' '+idCurent);

	newType(rand, true);



	if($(el).val() == '')
		return;
	else
		v = $(el).val();
	checkValue(getValueAt(rand,idCurent-3),v, $(el));

}

$(document).ready(function() {


	$(".complete").keydown(function(event) {
		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
				// Allow: Ctrl+A
				(event.keyCode == 65 && event.ctrlKey === true) ||
				// Allow: home, end, left, right
				(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything



			return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {

				event.preventDefault();
			}

		}
	});

	$(".complete").keyup(function(event) {
		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
				// Allow: Ctrl+A
				(event.keyCode == 65 && event.ctrlKey === true) ||
				// Allow: home, end, left, right
				(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything

			_completeaza(this);

			return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {

				event.preventDefault();
			}


			_completeaza(this);
		}
	});

	$(".complete2").keydown(function(event) {
		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
				// Allow: Ctrl+A
				(event.keyCode == 65 && event.ctrlKey === true) ||
				// Allow: home, end, left, right
				(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything



			return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {

				event.preventDefault();
			}

		}
	});

	$('#b2').blur(function(event)
			{
		checkBilet($('#b1').html(),$('#b2').val(), $('#b2'));


			});

	$('#b1').blur(function(event)
			{
		checkBilet($('#b1').html(),$('#b2').val(), $('#b2'));


			});





	$("#b2").keyup(function(event) {

		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
				// Allow: Ctrl+A
				(event.keyCode == 65 && event.ctrlKey === true) ||
				// Allow: home, end, left, right
				(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything

			checkBilet($('#b1').html(),$('#b2').val(), $('#b2'));

			return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {

				event.preventDefault();
			}

			checkBilet($('#b1').html(),$('#b2').val(), $('#b2'))

		}
	});




	$("#b1").keyup(function(event) {

		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
				// Allow: Ctrl+A
				(event.keyCode == 65 && event.ctrlKey === true) ||
				// Allow: home, end, left, right
				(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything

			checkBilet($('#b1').html(),$('#b2').val(), $('#b2'));

			return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {

				event.preventDefault();
			}

			checkBilet($('#b1').html(),$('#b2').val(), $('#b1'))

		}
	});



	$("#jump_salveaza").focus(function(event)
			{

		$("#salveaza_modificari").focus();
	});

});




$(".complete").tooltip({

	position: {

		my: "left-1 top+20", at: "right top"

	},

	open: function( event, ui ) {

		$('.ui-tooltip').hide();

		$('.complete').click(function() {

			$('.ui-tooltip').fadeIn();

		});
	}
});


function before(){


	var inputs = $('.complete')

	var ok = true;
	$(inputs).each(function() {

		if(ok && ($(this).val().length == 0)){
			alert('Completați toate câmpurile !');
			ok = false;
		}
	});
	if(!ok)
		return false;


	var change = parseInt($('#t17').html())+parseInt($('#t18').html());

	if((change != 0 )&& ($('#b2').val()=='') ){


		alert('Va rugăm să completați seria de bilete');
		$("#b2").focus();





		return false;
	}


	if($('#b2').val().length != 5){


		$('#b2').css({
			"border":"1px solid red",
			"color":"white",
			"background":"red"
		});

		alert('Seria de bilete trebuie să conțină minim 5 caractere');



		$(".mod").attr({"disabled":'disabled'});
		$(".mod").addClass('disabled');
		$("#b2").focus();
		return false;
	}


	var b1=$("#b1").html()+'';
	var where,b2;
	where = b2 =$("#b2").val()+'';

	//console.log(b1+' '+b2);

	if(parseInt(b1.substring(0,2)) == parseInt(b2.substring(0,2))){

		if(parseInt(b1.slice(-2)) > parseInt(b2.slice(-2))){

			if(noShow != 1){

				$(where).css({
					"border":"1px solid red",
					"color":"white",
					"background":"red"
				});


				$(where).tooltip({ items: where, content: "Seria de astăzi este mai mare decât cea de ieri "});
				$(where).tooltip("open");
			}
			return false;
		}
	}
	return true;
}
