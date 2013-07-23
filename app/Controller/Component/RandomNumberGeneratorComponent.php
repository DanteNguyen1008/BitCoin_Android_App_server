<?php
class RandomNumberGeneratorComponent extends Component
{
    public function getRandomNumber()
	{
		$result = array();
		$result[0] = rand(1,6);
		$result[1] = rand(1,6);
		$result[2] = rand(1,6);
		return $result;
	}
}