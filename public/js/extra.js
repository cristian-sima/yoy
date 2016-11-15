var fixProblems = function () {
	/*!
	DataTables Bootstrap 3 integration
	ÂŠ2011-2015 SpryMedia Ltd - datatables.net/license
	*/
	(function(b) {
		"function" === typeof define && define.amd ? define(["jquery", "datatables.net"], function(a) {
			return b(a, window, document)
		}) : "object" === typeof exports ? module.exports = function(a, d) {
			a || (a = window);
			if (!d || !d.fn.dataTable) d = require("datatables.net")(a, d).$;
			return b(d, a, a.document)
		} : b(jQuery, window, document)
	})(function(b, a, d) {
		var f = b.fn.dataTable;
		b.extend(!0, f.defaults, {
			dom: "<'container'<'row'<'col-xs-4 col-sm-6'l><'col-xs-8 col-sm-6 text-xs-center'f>><'table-responsive'tr>><'row'<'col-md-5'i><'col-md-7'p>>",
			renderer: "bootstrap"
		});
		b.extend(f.ext.classes, {
			sWrapper: "dataTables_wrapper form-inline dt-bootstrap4",
			sFilterInput: "form-control form-control-sm",
			sLengthSelect: "custom-select",
			sProcessing: "dataTables_processing panel panel-default",
			sPageButton: " page-item"
		});
		f.ext.renderer.pageButton.bootstrap = function(a, h, r, m, j, n) {
			var o = new f.Api(a),
			s = a.oClasses,
			k = a.oLanguage.oPaginate,
			t = a.oLanguage.oAria.paginate || {}, e, g, p = 0,
			q = function(d, f) {
				var l, h, i, c, m = function(a) {
					a.preventDefault();
					!b(a.currentTarget).hasClass("disabled") && o.page() != a.data.action &&
					o.page(a.data.action).draw("page")
				};
				l = 0;
				for (h = f.length; l < h; l++)
				if (c = f[l], b.isArray(c)) q(d, c);
				else {
					g = e = "";
					switch (c) {
						case "ellipsis":
						e = "&#x2026;";
						g = "disabled";
						break;
						case "first":
						e = k.sFirst;
						g = c + (0 < j ? "" : " disabled");
						break;
						case "previous":
						e = k.sPrevious;
						g = c + (0 < j ? "" : " disabled");
						break;
						case "next":
						e = k.sNext;
						g = c + (j < n - 1 ? "" : " disabled");
						break;
						case "last":
						e = k.sLast;
						g = c + (j < n - 1 ? "" : " disabled");
						break;
						default:
						e = c + 1, g = j === c ? "active" : ""
					}
					e && (i = b("<li>", {
						"class": s.sPageButton + " " + g,
						id: 0 === r && "string" === typeof c ? a.sTableId + "_" + c : null
					}).append(b("<a>", {
						href: "#",
						"aria-controls": a.sTableId,
						"aria-label": t[c],
						"data-dt-idx": p,
						tabindex: a.iTabIndex,
						"class": "page-link"
					}).html(e)).appendTo(d), a.oApi._fnBindAction(i, {
						action: c
					}, m), p++)
				}
			}, i;
			try {
				i = b(h).find(d.activeElement).data("dt-idx")
			} catch (u) {}
			q(b(h).empty().html('<ul class="pagination pagination-sm"/>').children("ul"), m);
			i && b(h).find("[data-dt-idx=" + i + "]").focus()
		};
		return f
	});
	$.extend(
		true,
		$.fn.dataTable.defaults, {
			oLanguage : {
				"sEmptyTable": "Nu există date disponibile în tabel",
				"sProcessing":   "Procesează...",
				"sLengthMenu":   "Afișez _MENU_ <span class='hidden-sm-down'> rânduri pe pagină</span>",
				"sZeroRecords":  "Nu am găsit nimic - ne pare rău",
				"sInfo":         "Afișez de la _START_ la _END_ din _TOTAL_ rânduri",
				"sInfoEmpty":    "Nu sunt rânduri",
				"sInfoFiltered": "(filtrate dintr-un total de _MAX_ rânduri)",
				"sInfoPostFix":  "",
				"sSearch":       "Caută:",
				"sUrl":          "",
				"oPaginate": {
					"sFirst":    "&laquo;",
					"sPrevious": "&lsaquo;",
					"sNext":     "&rsaquo;",
					"sLast":     "&raquo;"
				}
			}
		}
	);
};

const enableGeneralFunctionallity = () => {
	$("#disconnectButton").click(function(event) {
		event.preventDefault();
		if(confirm("Vrei să te deconectezi ?")) {
			document.location = "paraseste_aplicatia.php";
		}
	})
}

(function(){
	fixProblems();
	enableGeneralFunctionallity();
})();
