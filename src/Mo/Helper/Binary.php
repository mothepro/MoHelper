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
			$data = static::fromBase($data, $base);

		$this->data = pack('H*', $data);
	}
	
	/**
	 * HEX representation of data
	 * @return string
	 */
	public function toHex($prefix = false) {
		return unpack('H*hex', $this->data)['hex'];
	}
	
	/**
	 * HEX representation of data
	 * @return string
	 */
	public function toNiceHex() {
		return '0x' . strtoupper ($this->toHex());
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
	 * Not safe for normal calculations
	 * Uses bc functions
	 * 
	 * @return string
	 */
	public function toDecimal() {
		$ret		= '';
		
		$data	= str_split($this->toHex(), 1);
		$len	= count($data);
		$i		= 0;
		
		foreach($data as $char)
			$ret = bcadd($ret, // sum of current val and ...
					bcmul( // the product of ...
						array_search($char, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f']), // the value of a hex char [0==0 ... f==15]
						bcpow(16, $len - $i++) // the significance of that bit
					));
	
		return ltrim($ret, '0');
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
	 * Convert to any base to a given string format
	 * 
	 * @todo add quick function for base conversion with a multiple of 2 chars
	 * @link http://php.net/manual/en/function.base-convert.php#106546
	 * @param string $toBaseInput '01' is normal binary, '0123456789' is our normal decimal
	 * @return string
	 */
	public function toBase($toBaseInput) {
		// quick conversion test
		$quick = null;
		switch(strlen($toBaseInput)) {
		case 1:
		case 0:
			throw new \Exception('Base must be minimum of 2');
		case 2:
			$quick = $this->toBinary();
			break;
		case 8:
			$quick = $this->toOctal();
			break;
		case 10:
			$quick = $this->toDecimal();
			break;
		case 16:
			$quick = $this->toHex();
			break;
		}
		
		// slow conversion
		if(empty($quick)) {
			$toBase		= str_split($toBaseInput, 1);
			$len		= count($toBase);
			$base10		= $this->toDecimal();
			$ret		= '';
			
			do {
				$ret	= $toBase[ bcmod($base10, $len) ] . $ret; // prepend with modulo of base 10 and new base length
				$base10 = bcdiv($base10, $len, 0); // divide base 10 by length round down
			} while($base10 != 0);
		
			return $ret;
		} else
			$ret = strtr($quick, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $toBaseInput);
		
		return $ret;
	}
	
	/**
	 * From a base to hex
	 * @param string $data binary number/data
	 * @param int $base base to convert from
	 */
	protected static function fromBase($data, $base) {
		if($base <= 1)
			throw new \Exception('Base must be minimum of 2');
		
		if($base <= 32)
			return base_convert ($data, $base, 16);
		
		else
			throw new \Exception('Base is too large to convert from. Must be less than 32.');
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
