<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mo\Helper;

/**
 * Description of Binary
 *
 * @author Maurice Prosper <maurice.prosper@ttu.edu>
 */
class Binary {
	/**
	 * pure binary string
	 * @var string
	 */
	private $data;
	
	/**
	 * Data to save as a binary
	 * 
	 * @param string $data
	 * @param int $base
	 */
	public function __construct($data = null, $base = 16) {
		if($base !== 16)
			$data = base_convert ($data, $base, 16);
		
		$this->data = pack('H*', $data);
	}
	
	/**
	 * HEX representation of data
	 * @return string
	 */
	public function toHex() {
		$a = unpack('H*', $this->data);
		return $a[0];
	}
	
	/**
	 * Creates a random binary patter
	 * @param int $bits number of bits, rounds to lowest nibble
	 * @return Binary
	 */
	public static function random($bits = 128) {
		$data = '';
		$bits_ = $bits;
		while($bits_ > 0) {
			$data .= hex2bin(md5(microtime()));
			$bits_ -= 128;
		}
		$data = substr($data, 0, $bits);

		return new static($data, 2);
	}
}
