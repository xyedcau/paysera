<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;

Route::get('/', function (Request $request) {
	return view('welcome');
});

Route::post('/upload', function (Request $request) {
	if ($request->hasFile('commi')) {
		$z = explode(".", $request->file("commi")->getClientOriginalName());
		if (end($z) === "csv") {
			$client = new Client();
			$res = $client->request('GET', 'https://developers.paysera.com/tasks/api/currency-exchange-rates');
			$exch = json_decode($res->getBody(), true);
			$rate = $exch['rates'];

			$path = $request->commi->storeAs('', 'commission.csv');
			$pather = storage_path() . "/app/$path";
			$datas = explode("\n", file_get_contents($pather));

			$commissions = [];
			$freeCharge = 0;
			$prev = 0;
			$getMon = 0;

			foreach ($datas as $key => $data) {
				$qwe = explode(",", $data);
				$asd = (float)$qwe[4];
				$curr = trim(strtoupper($qwe[5]));

				if (strtolower($qwe[3]) === 'deposit') {
					$depo = 0.0003;
					if ($curr === 'EUR') {
						$qwe[4] = $asd * $depo;
					} else {
						$qwe[4] = ($asd / $rate[$curr]) * $depo * $rate[$curr];
					}
				}

				if (strtolower($qwe[3]) === 'withdraw' && strtolower($qwe[2]) === 'business') {
					$busi = 0.005;
					if ($curr === 'EUR') {
						$qwe[4] = $asd * $busi;
					} else {
						$qwe[4] = ($asd / $rate[$curr]) * $busi * $rate[$curr];
					}
				}

				if (strtolower($qwe[3]) === 'withdraw' && strtolower($qwe[2]) === 'private') {
					$day = date('D', strtotime($qwe[0]));
					$priv = 0.003;
					$free = 1000;
					$conv = $asd / $rate[$curr];
					$prev = strtotime($qwe[0]);
					$rang = 0;

					$rang = $prev - $getMon;
					if ($rang >= 604800) {
						$freeCharge = 0;
						switch ($day) {
							case 'Mon':
								$getMon = strtotime($qwe[0]);
								break;
							case 'Tue':
								$r = explode("-", $qwe[0]);
								$t = (int)$r[2] - 1;
								$getMon = strtotime("$r[0]-$r[1]-" . (string)$t);
								break;
							case 'Wed':
								$r = explode("-", $qwe[0]);
								$t = (int)$r[2] - 2;
								$getMon = strtotime("$r[0]-$r[1]-" . (string)$t);
								break;
							case 'Thu':
								$r = explode("-", $qwe[0]);
								$t = (int)$r[2] - 3;
								$getMon = strtotime("$r[0]-$r[1]-" . (string)$t);
								break;
							case 'Fri':
								$r = explode("-", $qwe[0]);
								$t = (int)$r[2] - 4;
								$getMon = strtotime("$r[0]-$r[1]-" . (string)$t);
								break;
							case 'Sat':
								$r = explode("-", $qwe[0]);
								$t = (int)$r[2] - 5;
								$getMon = strtotime("$r[0]-$r[1]-" . (string)$t);
								break;
							case 'Sun':
								$r = explode("-", $qwe[0]);
								$t = (int)$r[2] - 6;
								$getMon = strtotime("$r[0]-$r[1]-" . (string)$t);
								break;
						}
					}

					if ($curr === 'EUR') {
						if ($freeCharge < 3 && $asd >= $free) {
							$asd -= $free;
							++$freeCharge;
						}
						$qwe[4] = $asd * $priv;
					} else {
						if ($freeCharge < 3 && $conv >= $free) {
							$conv -= $free;
							++$freeCharge;
						}
						$qwe[4] = $conv * $priv * $rate[$curr];
					}
				}

				$commissions[$key] = [round($qwe[4], 2), $curr];
			}
			return view('csv', compact('datas', 'commissions'));
		}
	}
	return redirect('/');
});
