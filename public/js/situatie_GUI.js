function number_format(a, b, c, d) {
    a = (a + "").replace(/[^0-9+\-Ee.]/g, "");
    var e = !isFinite(+a) ? 0 : +a, f = !isFinite(+b) ? 0 : Math.abs(b), g = "undefined" === typeof d ? "," : d, h = "undefined" === typeof c ? "." : c, i = "", j = function(a, b) {
        var c = Math.pow(10, b);
        return "" + Math.round(a * c) / c;
    };
    i = (f ? j(e, f) : "" + Math.round(e)).split(".");
    if (i[0].length > 3) i[0] = i[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, g);
    if ((i[1] || "").length < f) {
        i[1] = i[1] || "";
        i[1] += new Array(f - i[1].length + 1).join("0");
    }
    return i.join(h);
}

function checkValue(a, b, c, d) {
    var d = d || 0;
    if (parseInt(b) < parseInt(a)) {
        if (1 != d) {
            $(c).css({
                border: "1px solid red",
                color: "white",
                background: "red"
            });
            $(c).tooltip({
                items: c,
                content: "Trebuie să fie mai mare sau egal decât cel anterior "
            });
            $(c).tooltip("open");
            $(c).tooltip({
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
        $(".mod").attr({
            disabled: "disabled"
        });
        $(".mod").addClass("disabled");
    } else {
        if (1 != d) $(c).css({
            border: "1px solid green",
            color: "white",
            background: "green"
        });
        $(c).tooltip({
            hide: {
                effect: "fade",
                duration: 1e3
            }
        });
        $(c).tooltip("disable");
        $(".mod").removeClass("disabled");
        activate();
    }
}

function getValueAt(a, b) {
    var c = $($("#v_" + b + "_" + a)[0]);
    if ($(c[0]).children().length > 0) return $($(c[0]).children()[0]).val(); else return c.text();
}

function setValueAt(a, b, c) {
    var d = $("#v_" + b + "_" + a);
    if (d.children().length > 0) $(d.children[0]).val(c); else {
        d.html((c + "").replace(".", ","));
        if (c < 0) d.css({
            color: "red"
        }); else d.css({
            color: "black"
        });
    }
}

function calculRow(a) {
    var b = {};
    b[2] = parseInt(getValueAt(a, 2));
    b[2] = "" == b[2] ? 0 : b[2];
    b[4] = getValueAt(a, 4);
    b[5] = getValueAt(a, 5);
    b[5] = "" == b[5] ? 0 : parseInt(b[5]);
    b[7] = getValueAt(a, 7);
    b[7] = "" == b[7] ? 0 : parseInt(b[7]);
    b[8] = parseInt(getValueAt(a, 8));
    b[10] = parseInt(getValueAt(a, 10));
    b[11] = (b[5] - b[2]) * b[8] < 0 ? 0 : (b[5] - b[2]) * b[8];
    b[13] = (b[7] - b[4]) * b[10] < 0 ? 0 : (b[7] - b[4]) * b[10];
    b[14] = b[11] - b[13];
    var c = getValueAt(a, 15) + "";
    c = c.replace(",", ".");
    b[16] = parseInt(b[14]) * c;
    b[16] = b[16].toFixed(2);
    for (value in b) setValueAt(a, value, b[value]);
}

function activate() {
    $(".mod").css({
        background: "rgb(121, 121, 255)",
        color: "white"
    });
    $(".mod").removeAttr("disabled");
}

function newType(a, b) {
    calculRow(a);
    if (b) calculTotal();
}

function calculTotal() {
    if (0 != intrari) {
        var a = {};
        a[2] = 0;
        a[4] = 0;
        a[5] = 0;
        a[7] = 0;
        a[11] = 0;
        a[14] = 0;
        a[13] = 0;
        a[16] = 0;
        a[18] = 0;
        a[17] = 0;
        a[19] = 0;
        for (var b = 1; b <= intrari; b++) {
            var c = b;
            a[2] = a[2] + parseInt(getValueAt(c, 2));
            a[4] = a[4] + ("" == getValueAt(c, 4) ? 0 : parseInt(getValueAt(c, 4)));
            a[5] = a[5] + ("" == getValueAt(c, 5) ? 0 : parseInt(getValueAt(c, 5)));
            a[7] = a[7] + ("" == getValueAt(c, 7) ? 0 : parseInt(getValueAt(c, 7)));
            a[11] = a[11] + parseInt(getValueAt(c, 11));
            a[14] = a[14] + parseInt(getValueAt(c, 14));
            a[13] = a[13] + parseInt(getValueAt(c, 13));
            var d = getValueAt(c, 16) + "";
            d = d.replace(",", ".");
            a[16] = parseFloat(a[16]) + parseFloat(d);
            a[17] = a[17] + getValueAt(c, 11) * getValueAt(c, 15);
            a[19] = a[19] + getValueAt(c, 13) * getValueAt(c, 15);
        }
        a[18] = a[16];
        var d = getValueAt("total", 15) + "";
        d = d.replace(",", ".");
        for (value in a) setValueAt("total", value, a[value]);
    }
}

var problems = 0;

function _completeaza(a) {
    var b = $(a).attr("name").charAt(1);
    var c = $(a).parent().attr("id");
    var d = c.split("_");
    var e = d[1];
    newType(b, true);
    if ("" == $(a).val()) return; else v = $(a).val();
    checkValue(getValueAt(b, e - 3), v, $(a));
}

$(document).ready(function() {
    $(".complete").keydown(function(a) {
        if (46 == a.keyCode || 8 == a.keyCode || 9 == a.keyCode || 27 == a.keyCode || 13 == a.keyCode || 65 == a.keyCode && true === a.ctrlKey || a.keyCode >= 35 && a.keyCode <= 39) return; else if (a.shiftKey || (a.keyCode < 48 || a.keyCode > 57) && (a.keyCode < 96 || a.keyCode > 105)) a.preventDefault();
    });
    $(".complete").keyup(function(a) {
        if (46 == a.keyCode || 8 == a.keyCode || 9 == a.keyCode || 27 == a.keyCode || 13 == a.keyCode || 65 == a.keyCode && true === a.ctrlKey || a.keyCode >= 35 && a.keyCode <= 39) {
            _completeaza(this);
            return;
        } else {
            if (a.shiftKey || (a.keyCode < 48 || a.keyCode > 57) && (a.keyCode < 96 || a.keyCode > 105)) a.preventDefault();
            _completeaza(this);
        }
    });
    $(".complete2").keydown(function(a) {
        if (46 == a.keyCode || 8 == a.keyCode || 9 == a.keyCode || 27 == a.keyCode || 13 == a.keyCode || 65 == a.keyCode && true === a.ctrlKey || a.keyCode >= 35 && a.keyCode <= 39) return; else if (a.shiftKey || (a.keyCode < 48 || a.keyCode > 57) && (a.keyCode < 96 || a.keyCode > 105)) a.preventDefault();
    });
    $("#jump_salveaza").focus(function(a) {
        $("#salveaza_modificari").focus();
    });
});

$(".complete").tooltip({
    position: {
        my: "left-1 top+20",
        at: "right top"
    },
    open: function(a, b) {
        $(".ui-tooltip").hide();
        $(".complete").click(function() {
            $(".ui-tooltip").fadeIn();
        });
    }
});

function before() {
    var a = $(".complete");
    var b = true;
    $(a).each(function() {
        if (b && 0 == $(this).val().length) {
            alert("Completați toate câmpurile !");
            b = false;
        }
    });
    if (!b) return false;
    var c = parseInt($("#t17").html()) + parseInt($("#t18").html());

    var d = $("#b1").html() + "";
    var e, f;
    e = f = $("#b2").val() + "";
    if (parseInt(d.substring(0, 2)) == parseInt(f.substring(0, 2))) if (parseInt(d.slice(-2)) > parseInt(f.slice(-2))) {
        if (1 != noShow) {
            $(e).css({
                border: "1px solid red",
                color: "white",
                background: "red"
            });
            $(e).tooltip({
                items: e,
                content: "Seria de astăzi este mai mare decât cea de ieri "
            });
            $(e).tooltip("open");
        }
        return false;
    }
    return true;
}
