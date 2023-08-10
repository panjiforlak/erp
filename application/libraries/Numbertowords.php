<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Numbertowords
{

	function convert_number($number)
	{
		if ($number < 0) {
			$number = - ($number);
		}
		if (($number < 0) || ($number > 9999999999999)) {
			throw new Exception("Number is out of range");
		}

		$Gn = floor($number / 1000000);
		/* Millions (giga) */
		$number -= $Gn * 1000000;
		$kn = floor($number / 1000);
		/* Thousands (kilo) */
		$number -= $kn * 1000;
		$Hn = floor($number / 100);
		/* Hundreds (hecto) */
		$number -= $Hn * 100;
		$Dn = floor($number / 10);
		/* Tens (deca) */
		$n = $number % 10;
		/* Ones */

		$res = "";

		if ($Gn) {
			$res .= $this->convert_number($Gn) .  " Juta";
		}

		if ($kn) {
			if ($kn == 1) {
				$res .= (empty($res) ? "" : " ") . '' . " Seribu";
			} else {
				$res .= (empty($res) ? "" : " ") . $this->convert_number($kn) . " Ribu";
			}
		}

		if ($Hn) {
			if ($Hn == 1) {
				$res .= (empty($res) ? "" : " ") . '' . " Seratus";
			} else {
				$res .= (empty($res) ? "" : " ") . $this->convert_number($Hn) . " Ratus";
			}
		}

		$ones = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas", "Dua Belas", "Tiga Belas", "Empat Belas", "Lima Belas", "Enam Belas", "Tujuh Belas", "Delapan Belas", "Sembilan Belas");
		$tens = array("", "", "Dua Puluh", "Tiga Puluh", "Empat Puluh", "Lima Puluh", "Enam Puluh", "Tujuh Puluh", "Delapan Puluh", "Sembilan Puluh");

		if ($Dn || $n) {
			if (!empty($res)) {
				$res .= "  ";
			}

			if ($Dn < 2) {
				$res .= $ones[$Dn * 10 + $n];
			} else {
				$res .= $tens[$Dn];

				if ($n) {
					$res .= " " . $ones[$n];
				}
			}
		}

		if (empty($res)) {
			$res = "Nol";
		}

		return $res;
	}
}
