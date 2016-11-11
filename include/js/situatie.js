var situatie = {};
var totaluri = {};
var aparate 		= new Array();

function beforeSubmit()
{
	// verifica index-uri

	var tabel = document.getElementById('situatie_data');


	var problem			= false;

	for (row = 3; row <= situatie.nrDeAparate + 2; row++)
	{
		start_intrare = ($(tabel.rows[row].cells[2]).text());
		end_intrare = ($(tabel.rows[row].cells[5]).children()[0].value);

		start_iesire = ($(tabel.rows[row].cells[4]).text());
		end_iesire = ($(tabel.rows[row].cells[7]).children()[0].value);

		serie_aparat = ($(tabel.rows[row].cells[1]).text());
		id_aparat =  ($(tabel.rows[row].cells[9]).children()[0].value);


		if(end_iesire.length < 2)
		{
			alert("Index-ul de ieșire pentru aparatul cu seria "+serie_aparat+" trebuie să aibă cel puțin 2 cifre");
			$problem		= true;
			$(tabel.rows[row].cells[7]).children()[0].focus();
			return false;
		}


		if(end_intrare.length < 2)
		{
			alert("Index-ul de intrare pentru aparatul cu seria "+serie_aparat+" trebuie să aibă cel puțin 2 cifre");
			$problem		= true;
			$(tabel.rows[row].cells[5]).children()[0].focus();
			return false;
		}


		start_intrare 	= parseInt(start_intrare);
		end_intrare		= parseInt(end_intrare);
		start_iesire	= parseInt(start_iesire);
		end_iesire		= parseInt(end_iesire);



		if(start_intrare > end_intrare)
		{
			alert("Index-ul de intrare pentru aparatul cu seria "+serie_aparat+" trebuie să fie mai mare decât cel de început");
			$problem		= true;
			$(tabel.rows[row].cells[5]).children()[0].focus();
			return false;
		}

		if(start_iesire > end_iesire)
		{
			alert("Index-ul de ieșire pentru aparatul cu seria "+serie_aparat+" trebuie să fie mai mare decât cel de început");
			$problem		= true;
			$(tabel.rows[row].cells[7]).children()[0].focus();
			return false;
		}


		aparate.push(id_aparat);
	}

	if(!problem)
	{
		//submit

		$("#aparate_").val(aparate.join("|"));

		$("#formular_situatie").submit();

	}
}


function round(value, exp)
{
	if (typeof exp === 'undefined' || +exp === 0)
		return Math.round(value);

	value = +value;
	exp = +exp;

	if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
		return NaN;

	// Shift
	value = value.toString().split('e');
	value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));

	// Shift back
	value = value.toString().split('e');
	return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
}

$(document).ready(function ()
{



	function updateTotal(values)
	{
		totaluri.incasari += round(values.incasari * values.pret_pe_impuls, 2);
		totaluri.sertar = parseFloat(totaluri.incasari);

	}


	function recalculare_situatie()
	{
		var tabel = document.getElementById('situatie_data');


		totaluri.incasari = 0;
		totaluri.sertar = 0;

		for (row = 3; row <= situatie.nrDeAparate + 2; row++)
		{
			dif1	= 0;
			dif2	= 0;

			start_intrare = ($(tabel.rows[row].cells[2]).text());
			end_intrare = ($(tabel.rows[row].cells[5]).children()[0].value);

			start_iesire = ($(tabel.rows[row].cells[4]).text());
			end_iesire = ($(tabel.rows[row].cells[7]).children()[0].value);


			factor_mecanic = parseInt($(tabel.rows[row].cells[8]).text());
			pret_pe_impuls = parseFloat($(tabel.rows[row].cells[15]).text());

			start_intrare = parseInt(start_intrare);
			start_iesire = parseInt(start_iesire);



			if((end_intrare != "") && (start_intrare <= parseInt(end_intrare)))
			{
				end_intrare = parseInt(end_intrare);
				dif1 = parseInt(factor_mecanic * (end_intrare - start_intrare));
			}

			if((end_iesire != "") && (start_iesire <= parseInt(end_iesire)))
			{
				end_iesire = parseInt(end_iesire);
				dif2 = parseInt(factor_mecanic * (end_iesire - start_iesire));
			}





			diferenta = round(dif1 - dif2, 2);;

			total_ = 0;
			total_ = round(diferenta * pret_pe_impuls, 2);

			// console.log('intrare '+start_intrare+' -> '+end_intrare);
			// console.log('iesire '+start_iesire+' -> '+end_iesire);
			// console.log('pre_pe_impuls '+pret_pe_impuls);
			// console.log('factor_mecanic '+factor_mecanic);


			$(tabel.rows[row].cells[11]).text(dif1);
			$(tabel.rows[row].cells[13]).text(dif2);
			$(tabel.rows[row].cells[14]).text(dif1 - dif2);
			$(tabel.rows[row].cells[16]).text(total_ + ' lei');


			updateTotal(
			{
				"incasari": dif1,
				"pret_pe_impuls": pret_pe_impuls
			});

		}


		// arata totalurile
		$("#incasari").text(totaluri.incasari);
		$("#sertar").text(totaluri.sertar);

		$("#total_bani").text(totaluri.sertar);
	}


	$(".completare_index").keydown(function (event)
	{
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(event.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
			// Allow: Ctrl+A
			(event.keyCode == 65 && event.ctrlKey === true) ||
			// Allow: home, end, left, right
			(event.keyCode >= 35 && event.keyCode <= 39))
		{
			// let it happen, don't do anything
			return;
		}
		else
		{
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105))
			{
				event.preventDefault();
			}
		}

	});



	$('.completare_index').keyup(function ()
	{
		recalculare_situatie();
	});

	$("#from").datepicker(
	{


		changeMonth: true,
		numberOfMonths: 1,
		maxDate: "+0d",
		onClose: function (selectedDate)
		{
			$("#from").datepicker("option", "dateFormat", "yy-mm-dd");
			$("#to").datepicker("option", "minDate", selectedDate);

			$("#to").focus();
		}
	});


	$("#an").change(function ()
	{
		$("#luna").focus();
	});

	$("#luna").change(function ()
	{
		seeData2();
	});

	$("#to").datepicker(
	{

		changeMonth: true,
		numberOfMonths: 1,
		maxDate: "+0d",
		onClose: function (selectedDate)
		{
			$("#to").datepicker("option", "dateFormat", "yy-mm-dd");
			$("#viz_").focus();
		}
	});

	$(function ()
	{
		$(document).tooltip();
	});


});

function seeData()
{

	document.location = "situatie_mecanica.php?id_firma=" + situatie['firma'] + "&type=" + $("#_type").val() + "&from=" + $("#from").val() + "&to=" + $("#to").val();

}

function seeData2()
{

	document.location = "situatie_mecanica.php?id_firma=" + situatie['firma'] + "&month=true&type=" + $("#_type").val() + "&from=" + $("#an").val() + "-" + $("#luna").val() + "-01&to=false";

}

function seeData3()
{

	document.location = "selecteaza_situatie.php?id_firma=" + situatie['firma'] + "&type=" + $("#_type").val() + "&from=" + $("#an").val() + "-" + $("#luna").val() + "";

}

$(function ()
{
	$(document).tooltip(
	{
		position:
		{
			my: "center bottom-20",
			at: "center top",
			using: function (position, feedback)
			{
				$(this).css(position);
				$("<div>")
					.addClass("arrow")
					.addClass(feedback.vertical)
					.addClass(feedback.horizontal)
					.appendTo(this);
			}
		}
	});
});
