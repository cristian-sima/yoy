const enableGeneralFunctionallity = () => {
	$("#disconnectButton").click(function(event) {
		event.preventDefault();
		if(confirm("Vrei să te deconectezi ?")) {
			document.location = "paraseste_aplicatia.php";
		}
	})
}

(function(){
	enableGeneralFunctionallity();
})();
