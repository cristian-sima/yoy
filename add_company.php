<?php

require_once 'app/Aplicatie.php';

try {
	Design::showHeader();
	$db = Aplicatie::getInstance()->Database;

	?>
	<div class="container">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="companies.php">Firme spațiu</a></li>
			<li class="breadcrumb-item active">Adaugă firmă</li>
		</ol>
		<div class="row">
			<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-xl-6 offset-xl-3">
				<form id="company_form" action="add_company_POST.php" method="POST">
					<h1>Adaugă firmă</h1>
					<hr>
					<div class="form-group text-xs-left row">
						<label class="col-md-4 form-control-label" for="nume">
							Denumire
						</label>
						<div class="col-md-8">
							<input
							class="form-control text-capitalize"
							id="nume"
							name="nume"
							placeholder="ex. Impex S.R.L."
							type="text"
							value=""
							/>
						</div>
					</div>
					<div class="form-group text-xs-left row">
						<label class="col-md-4 form-control-label" for="localitate">
							Localitate
						</label>
						<div class="col-md-8">
							<input
							class="form-control text-capitalize"
							id="localitate"
							name="localitate"
							placeholder="ex. Bolintin Vale"
							type="text"
							value=""
							/>
						</div>
					</div>
					<div class="form-group text-xs-left row">
						<label class="col-md-4 form-control-label" for="procent">
							Procent câștiguri
						</label>
						<div class="col-md-8">
							<div class="input-group">
								<input
								class="form-control form-control-warning"
								id="procent"
								name="procent"
								placeholder="ex. 50"
								type="number"
								max="100"
								min="0"
								value="" />
								<div class="input-group-addon">
									%
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="form-group text-xs-left row">
						<label class="col-md-4 form-control-label" for="comentarii">
							Comentarii
						</label>
						<div class="col-md-8">
							<textarea
							class="form-control"
							id="comentarii"
							name="comentarii"
							placeholder="ex. Această firmă este în insolvență"
							type="text"
							value=""></textarea>
						</div>
					</div>
					<div class="form-group text-xs-left row">
						<label class="col-md-4 form-control-label" for="date_contact">
							Date de contact
						</label>
						<div class="col-md-8">
							<textarea
							class="form-control form-control-warning"
							check="true"
							criteria='{type:"string",  maxSize:"30"}'
							id="date_contact"
							name="date_contact"
							placeholder="ex. Strada Republicii Nr. 22"
							type="text"
							value=""></textarea>
						</div>
					</div>
					<hr>
					<div id="wrong-data-message" class="alert alert-warning" role="alert" style="display:none">
						<strong>
							Nu am putut trimite formularul
						</strong>
						Completează corect toate câmpurile
					</div>
					<div class="text-xs-center">
						<button aria-label="Adaugă" class="btn btn-primary" type="submit">
							Adaugă
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?=	DESIGN::showFooter();	?>

	<script type="text/javascript">
	(function(){
		$("#nume").focus();

		$("#company_form").submit(function(event) {

			const hasProblems = function () {
				var isWrongName = function () {
					var element = $("#nume"),
					value = element.val(),
					isWrong = (
						value.length < 5 ||
						value.length > 30
					);

					if (isWrong) {
						element.focus();
					}

					return isWrong;
				},
				isWrongAddress = function () {
					var element = $("#localitate"),
					value = element.val(),
					isWrong = (
						value.length < 5 ||
						value.length > 30
					);

					if (isWrong) {
						element.focus();
					}

					return isWrong;
				},
				isWrongCurrentPercent = function () {
					var element = $("#procent"),
					value = element.val(),
					isWrong = (
						value === "" ||
						isNaN(value)
					);

					if (isWrong) {
						element.focus();
					}

					return isWrong;
				},
				isWrongComments = function () {
					var element = $("#comentarii"),
					value = element.val(),
					isWrong = (
						value.length > 30
					);

					if (isWrong) {
						element.focus();
					}

					return isWrong;
				},
				isWrongContactDetails = function () {
					var element = $("#date_contact"),
					value = element.val(),
					isWrong = (
						value.length > 30
					);

					if (isWrong) {
						element.focus();
					}

					return isWrong;
				}

				return (
					isWrongName() ||
					isWrongAddress() ||
					isWrongCurrentPercent() ||
					isWrongComments() ||
					isWrongContactDetails()
				)
			};

			if (hasProblems()) {
				$("#wrong-data-message").fadeIn();
				event.preventDefault();

				return false;
			}

			return true;
		})
	})()
	</script>

	<?php
} catch (Exception $e) {
	DESIGN::complain($e->getMessage());
}
