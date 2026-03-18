<?php
namespace yamarket_fusion\Helpers;

class MathHelper {

	public static function comparison($sign, $param1, $param2) {
		switch ($sign) {
			case '>':
				$output = $param1 > $param2; break;
			case '<':
				$output = $param1 < $param2; break;
			case '>=':
				$output = $param1 >= $param2; break;
			case '<=':
				$output = $param1 <= $param2; break;
		}

		return $output;
	}

	public static function inRange($value, $from, $to) {
		return ($value >= $from) && ($value <= $to);
	}

	public static function operation() {
		$operands = func_get_args();
		$sign = array_shift($operands);
		$operation = array(__CLASS__);

		switch ($sign) {
			case '+':
				$operation[1] = 'add';
				break;
			case '-':
				$operation[1] = 'substract';
				break;
			case '*':
				$operation[1] = 'multiple';
				break;
			case '/':
				$operation[1] = 'divide';
				break;
			default:
				$operation = null;
				break;
		}

		$output = null;

		if (count($operands) > 1 && $operation !== null) {
			$output = call_user_func_array($operation, $operands);;
		}

		return $output;
	}

	public static function add() {
		$output = NAN;

		if (func_num_args() > 1) {
			$output = func_get_arg(0);
			for ($i = 1; $i < func_num_args(); $i++) {
				$output += func_get_arg($i);
			}
		}

		return $output;
	}
	
	public static function substract() {
		$output = NAN;

		if (func_num_args() > 1) {
			$output = func_get_arg(0);
			for ($i = 1; $i < func_num_args(); $i++) {
				$output -= func_get_arg($i);
			}
		}

		return $output;
	}
	
	public static function muliple() {
		$output = NAN;

		if (func_num_args() > 1) {
			$output = array_product(func_get_args());
		}

		return $output;
	}
	
	public static function divide() {
		$output = NAN;

		if (func_num_args() > 1) {
			$output = func_get_arg(0);
			for ($i = 1; $i < func_num_args(); $i++) {
				$output /= func_get_arg($i);
			}
		}

		return $output;
	}

	public static function percent($value, $percent) {
		return $value * $percent / 100;
	}
}