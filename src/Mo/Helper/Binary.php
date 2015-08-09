<?php

/*
 * The MIT License
 *
 * Copyright 2015 Maurice Prosper <maurice.prosper@ttu.edu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Mo\Helper;

/**
 * Layer of abstraction for binary data
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
	public function toHex($prefix = false) {
		return ($prefix ? '0x' : null) . unpack('H*hex', $this->data)['hex'];
	}
	
	/**
	 * Binary data to char list
	 * @return string
	 */
	public function toAscii() {
		return hex2bin($this->toHex());
	}
	
	/**
	 * Binary representation
	 * @return string
	 */
	public function toBinary() {
		$ret = '';
		foreach(str_split($this->toHex(), 1) as $char)
			$ret .= str_pad(base_convert ($char, 16, 2), 4, '0', STR_PAD_LEFT);
		
		return $ret;
	}
	
	/**
	 * Octal format
	 * @return string
	 */
	public function toOctal() {
		$ret = '';
		foreach(str_split($this->toHex(), 3) as $char)
			$ret .= str_pad(base_convert ($char, 16, 2), 4, '0', STR_PAD_LEFT);
		
		return $ret;
	}
	
	/**
	 * Binary data to Base 10
	 * Not safe for calculations
	 * 
	 * @return float|int
	 */
	public function toDecimal() {
		return hexdec($this->toHex());
	}
	
	/**
	 * Unpacks the data
	 * 
	 * @param string $format
	 * @return array
	 */
	public function unpack($format) {
		return unpack($format, $this->data);
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

		return new static(bin2hex($data), 16);
	}
}
