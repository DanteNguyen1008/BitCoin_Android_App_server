<?php
class GamePlayComponent extends Component {
	/*
	 Big("big"), Small("small"), Triple1("triples-1"), Triple2("triples-2"), Triple3(
	 "triples-3"), Triple4("triples-4"), Triple5("triples-5"), Triple6(
	 "triples-6"), Double1("doubles-1"), Double2("doubles-2"), Double3(
	 "doubles-3"), Double4("doubles-4"), Double5("doubles-5"), Double6(
	 "doubles-6"), AllTriple("triples-any"), ThreeDice4("total-4"), ThreeDice17(
	 "total-17"), ThreeDice6("total-6"), ThreeDice15("total-15"), ThreeDice5(
	 "total-5"), ThreeDice16("total-16"), ThreeDice7("total-7"), ThreeDice14(
	 "total-14"), ThreeDice8("total-8"), ThreeDice13("total-13"), ThreeDice9(
	 "total-9"), ThreeDice12("total-12"), SingleDice1("single-1"), SingleDice2(
	 "single-2"), SingleDice3("single-3"), SingleDice4("single-4"), SingleDice5(
	 "single-5"), SingleDice6("single-6"), ThreeDice10("total-10"), ThreeDice11(
	 "total-11");
	 */

	static $arrBetRule = array('big' => 2, 'small' => 2, 'single-1' => 2, 'single-2' => 2, 'single-3' => 3, 'single-4' => 3, 'single-5' => 4, 'single-6' => 4, 'total-4' => 51, 'total-17' => 51, 'total-6' => 15, 'total-15' => 15, 'total-5' => 19, 'total-16' => 19, 'total-7' => 13, 'total-14' => 13, 'total-8' => 9, 'total-13' => 9, 'total-9' => 7, 'total-12' => 7, 'doubles-1' => 9, 'doubles-2' => 9, 'doubles-3' => 9, 'doubles-4' => 9, 'doubles-5' => 9, 'doubles-6' => 9, 'triples-1' => 151, 'triples-2' => 151, 'triples-3' => 151, 'triples-4' => 151, 'triples-5' => 151, 'triples-6' => 151, 'triples-any' => 151);

	static $arrWinByDice = array('single-1', 'single-2', 'single-3', 'single-4', 'single-5', 'single-6');
	static $arrWinByTotal = array('total-4' => 4, 'total-17' => 17, 'total-6' => 6, 'total-15' => 15, 'total-5' => 5, 'total-16' => 16, 'total-7' => 7, 'total-14' => 14, 'total-8' => 8, 'total-13' => 13, 'total-9' => 9, 'total-12' => 12);
	static $arrWinByDouble = array('doubles-1' => 1, 'doubles-2' => 2, 'doubles-3' => 3, 'doubles-4' => 4, 'doubles-5' => 5, 'doubles-6' => 6);
	static $arrWinByTriple = array('triples-1' => 1, 'triples-2' => 2, 'triples-3' => 3, 'triples-4' => 4, 'triples-5' => 5, 'triples-6' => 6);

	public function specficyBetResult($dices, $bets, $betsAmount) {
		$result = array();
		var_dump($dices);
		echo '<br/>';
		$result = $this -> winByTotal($dices, $bets, $betsAmount);
		$result = array_merge($result, $this -> winByDice($dices, $bets, $betsAmount));
		$result = array_merge($result, $this -> winByDouble($dices, $bets, $betsAmount));
		$result = array_merge($result, $this -> winByTriple($dices, $bets, $betsAmount));
		$result = array_merge($result, $this -> winByBigSmall($dices, $bets, $betsAmount));
		return $result;
	}

	private function winByBigSmall($dices, $bets, $betsAmount) {
		if (!($dices[0] == $dices[1] && $dices[1] == $dices[2])) {
			$total = $dices[0] + $dices[1] + $dices[2];
			for ($i = 0; $i < count($bets); $i++) {
				if ($bets[$i] == 'big' && $total > 10) {
					return array($bets[$i] => $betsAmount[$i]);
				} else if ($bets[$i] == 'small' && $total <= 10) {
					return array($bets[$i] => $betsAmount[$i]);
				}
			}
		}

		return array();
	}

	private function winByDice($dices, $bets, $betsAmount) {
		//From 1 -> 6
		$result = array();
		foreach ($dices as $dice) {
			for ($i = 1; $i <= count(self::$arrWinByDice); $i++) {
				for ($j = 0; $j < count($bets); $j++) {
					if ($bets[$j] == self::$arrWinByDice[$i - 1] && $dice == $i) {
						$result = array_merge($result, array(self::$arrWinByDice[$i - 1] => $betsAmount[$j]));
					}
				}
			}
		}
		return $result;
	}

	private function winByTotal($dices, $bets, $betsAmount) {
		//Big, small total from 4 - > 18

		$total = $dices[0] + $dices[1] + $dices[2];

		foreach (self::$arrWinByTotal as $betName => $value) {
			for ($i = 0; $i < count($bets); $i++) {
				if ($bets[$i] == $betName && $total == $value) {
					return array($bets[$i] => $betsAmount[$i]);
				}
			}
		}

		return array();
	}

	private function winByDouble($dices, $bets, $betsAmount) {
		//Doubles and any double
		$doubleNumber = 0;
		//Specify double number
		if ($dices[0] == $dices[1] && $dices[1] == $dices[2]) {
			$doubleNumber = $dices[0];
		} else if ($dices[0] == $dices[1] || $dices[1] == $dices[2]) {
			$doubleNumber = $dices[1];
		} else if ($dices[0] == $dices[2] && $dices[0] != $dices[1]) {
			$doubleNumber = $dices[2];
		}

		if ($doubleNumber != 0) {
			foreach (self::$arrWinByDouble as $betName => $value) {
				for ($i = 0; $i < count($bets); $i++) {
					if ($bets[$i] == $betName && $doubleNumber == $value) {
						return array($bets[$i] => $betsAmount[$i]);
					}
				}
			}
		}
		return array();
	}

	private function winByTriple($dices, $bets, $betsAmount) {
		//triple and any triple
		$result = array();
		if ($dices[0] == $dices[1] && $dices[1] == $dices[2]) {
			foreach (self::$arrWinByTriple as $betName => $value) {
				for ($i = 0; $i < count($bets); $i++) {
					if ($bets[$i] == $betName && $dices[0] == $value) {
						$result = array($bets[$i] => $betsAmount[$i]);
					}
				}
			}

			for ($i = 0; $i < count($bets); $i++) {
				if ($bets[$i] == 'triples-any') {
					$result = array_merge($result, array($bets[$i] => $betsAmount[$i]));
				}
			}
		}
		return $result;
	}

}
