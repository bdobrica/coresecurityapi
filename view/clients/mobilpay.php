<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-blog-header.php');
include (dirname(dirname(dirname(dirname(__FILE__)))) . '/plugins/wp-crm/class/Mobilpay/Payment/Request/Abstract.php');
include (dirname(dirname(dirname(dirname(__FILE__)))) . '/plugins/wp-crm/class/Mobilpay/Payment/Request/Card.php');
include (dirname(dirname(dirname(dirname(__FILE__)))) . '/plugins/wp-crm/class/Mobilpay/Payment/Request/Notify.php');
include (dirname(dirname(dirname(dirname(__FILE__)))) . '/plugins/wp-crm/class/Mobilpay/Payment/Invoice.php');
include (dirname(dirname(dirname(dirname(__FILE__)))) . '/plugins/wp-crm/class/Mobilpay/Payment/Address.php');
?>

<div class="wp-crm-view-payment-wrap">
	<div class="wp-crm-view-payment">
		<div class="wp-crm-view-mobilpay">

<?php if (!isset($_GET['inv'])) { ?>

			<h1>Parametrii tranzactiei dumneavoastra nu sunt valizi!</h1>
			<p>Tranzactia dumneavoastra a esuat.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

<?php } else {
	$invoice = new WP_CRM_Invoice ((int) $_GET['inv']);
	$payload = unserialize ($invoice->get ('payload'));

	if (!isset($payload['mobilpay'])) { ?>

			<h1>Parametrii tranzactiei dumneavoastra nu sunt valizi!</h1>
			<p>Tranzactia dumneavoastra a esuat.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

<?php		}
	else {
		switch ($payload['mobilpay']) {
			case 'confirmed': ?>

			<h1>Plata dumneavoastra a fost inregistrata cu succes!</h1>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'confirmed_pending': ?>

			<h1>Plata dumneavoastra este in curs de verificare anti-frauda.</h1>
			<p>In cel mai scurt timp vom primi rezultatul acestei verificari si vom actualiza starea comenzii dumneavoastra.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'paid_pending': ?>

			<h1>Plata dumneavoastra este in curs de verificare.</h1>
			<p>In cel mai scurt timp vom primi rezultatul acestei verificari si vom actualiza starea comenzii dumneavoastra.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'paid': ?>

			<h1>Plata dumneavoastra este in curs de procesare.</h1>
			<p>In cel mai scurt timp vom actualiza starea comenzii dumneavoastra.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'canceled': ?>

			<h1>Plata dumneavoastra a fost anulata.</h1>
			<p>Va multumim!</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'credit': ?>

			<h1>Suma platita de dumneavoastra v-a fost returnata cu succes.</h1>
			<p>Va multumim!</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'invalid': ?>

			<h1>Parametrii tranzactiei dumneavoastra nu sunt valizi!</h1>
			<p>Tranzactia dumneavoastra a esuat.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'rejected':
				switch ($payload['errcode']) {
					case 0x10: ?>

			<h1>Plata dumneavoastra a fost respinsa. Cardul folosit de dumneavoastra prezinta un grad mare de risc.</h1>
			<p>In cel mai scurt timp vom primi rezultatul verificarii anti-frauda si vom actualiza starea comenzii dumneavoastra.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x11: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: numarul cardului nu este corect.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x12: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: cardul dumneavoastra este blocat.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x13: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: cardul dumneavoastra a expirat.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x14: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: fonduri insuficiente.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x15: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: numar CVV2 incorect.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x16: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: banca emitenta nu a putut fi contactata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					
					case 0x20: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: suma este incorecta.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x21: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: moneda platii este incorecta.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x22: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: cardul nu poate fi folosit online.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x23: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: tranzactia a fost respinsa.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x24: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: tranzactia a fost respinsa de filtrele antifrauda.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x25: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: tranzactia a fost respinsa (incalcarea legii).</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x26: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: tranzactia a fost respinsa.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;

					case 0x30: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: cerea trimisa nu a fost validata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x31: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: preautorizarea este posibila numai pentru tranzactii noi.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x32: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: nu se poate autoriza decat o tranzactie noua.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x33: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: nu se poate anula decat o tranzactie preautorizata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x34: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: nu se poate postautoriza decat o tranzactie preautorizata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x35: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: nu se poate credita decat o tranzactie finalizata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x36: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: suma de creditare este mai mica decat suma tranzactiei autorizate sau postautorizate.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x37: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: suma tranzactiei de postautorizare este mai mare decat suma preautorizata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;
					case 0x38: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: cerere duplicata.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php	break;

					default: ?>

			<h1>Plata dumneavoastra a fost respinsa de catre procesator!</h1>
			<p>Tranzactia dumneavoastra a fost respinsa de catre procesator cu mesajul: eroare generala.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

					<?php }
?>
			<?php break;
			case 'exception': ?>

			<h1>Conexiunea cu serverul procesatorului a esuat!</h1>
			<p>Conexiunea cu serverul procesatorului s-a intrerupt neasteptat. Nu a fost efectuata nicio tranzactie.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			case 'missing': ?>

			<h1>Conexiunea cu serverul procesatorului a esuat!</h1>
			<p>Conexiunea cu serverul procesatorului a esuat. Nu a fost efectuata nicio tranzactie.</p>
			<p>Apasati <a href="/">aici</a> pentru a continua navigarea pe site-ul www.traininguri.ro.</p>

			<?php break;
			}
		}
	} ?>

			<p>Pentru orice intrebari sau mentiuni va rugam sa va adresati prin email la secretariat@traininguri.ro sau prin telefon la <strong>+40 (0)31 425 25 34</strong>.</p>
		</div>
	</div>
</div>
