<?php
/**
 * Core of WP_CRM_Secure*
 */

/**
 * Class that implements the SRP protocol.
 *
 * @category
 * @package WP_CRM_Secure
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_SRP {
	const N_1024 = 'eeaf0ab9adb38dd69c33f80afa8fc5e86072618775ff3c0b9ea2314c9c256576d674df7496ea81d3383b4813d692c6e0e0d5d8e250b98be48e495c1d6089dad15dc7d7b46154d6b6ce8ef4ad69b15d4982559b297bcf1885c529f566660e57ec68edbc3c05726cc02fd4cbf4976eaa9afd5138fe8376435b9fc61d2fc0eb06e3';
	const g_1024 = 2;
	
	const N_2048 = 'AC6BDB41324A9A9BF166DE5E1389582FAF72B6651987EE07FC3192943DB56050A37329CBB4A099ED8193E0757767A13DD52312AB4B03310DCD7F48A9DA04FD50E8083969EDB767B0CF6095179A163AB3661A05FBD5FAAAE82918A9962F0B93B855F97993EC975EEAA80D740ADBF4FF747359D041D5C33EA71D281E446B14773BCA97B43A23FB801676BD207A436C6481F1D2B9078717461A5B9D32E688F87748544523B524B0D57D5EA77A2775D2ECFA032CFBDBF52FB3786160279004E57AE6AF874E7303CE53299CCC041C7BC308D82A5698F3A8D0C38271AE35F8E9DBFBB694B5C803D89F7AE435DE236D525F54759B65E372FCD68EF20FA7111F9E4AFF73';
	const g_2048 = 2;

	const N_4096 = 'FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AAAC42DAD33170D04507A33A85521ABDF1CBA64ECFB850458DBEF0A8AEA71575D060C7DB3970F85A6E1E4C7ABF5AE8CDB0933D71E8C94E04A25619DCEE3D2261AD2EE6BF12FFA06D98A0864D87602733EC86A64521F2B18177B200CBBE117577A615D6C770988C0BAD946E208E24FA074E5AB3143DB5BFCE0FD108E4B82D120A92108011A723C12A787E6D788719A10BDBA5B2699C327186AF4E23C1A946834B6150BDA2583E9CA2AD44CE8DBBBC2DB04DE8EF92E8EFC141FBECAA6287C59474E6BC05D99B2964FA090C3A2233BA186515BE7ED1F612970CEE2D7AFB81BDD762170481CD0069127D5B05AA993B4EA988D8FDDC186FFB7DC90A6C08F4DF435C934063199FFFFFFFFFFFFFFFF';
	const g_4096 = 5;

	const N_8192 = 'FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AAAC42DAD33170D04507A33A85521ABDF1CBA64ECFB850458DBEF0A8AEA71575D060C7DB3970F85A6E1E4C7ABF5AE8CDB0933D71E8C94E04A25619DCEE3D2261AD2EE6BF12FFA06D98A0864D87602733EC86A64521F2B18177B200CBBE117577A615D6C770988C0BAD946E208E24FA074E5AB3143DB5BFCE0FD108E4B82D120A92108011A723C12A787E6D788719A10BDBA5B2699C327186AF4E23C1A946834B6150BDA2583E9CA2AD44CE8DBBBC2DB04DE8EF92E8EFC141FBECAA6287C59474E6BC05D99B2964FA090C3A2233BA186515BE7ED1F612970CEE2D7AFB81BDD762170481CD0069127D5B05AA993B4EA988D8FDDC186FFB7DC90A6C08F4DF435C93402849236C3FAB4D27C7026C1D4DCB2602646DEC9751E763DBA37BDF8FF9406AD9E530EE5DB382F413001AEB06A53ED9027D831179727B0865A8918DA3EDBEBCF9B14ED44CE6CBACED4BB1BDB7F1447E6CC254B332051512BD7AF426FB8F401378CD2BF5983CA01C64B92ECF032EA15D1721D03F482D7CE6E74FEF6D55E702F46980C82B5A84031900B1C9E59E7C97FBEC7E8F323A97A7E36CC88BE0F1D45B7FF585AC54BD407B22B4154AACC8F6D7EBF48E1D814CC5ED20F8037E0A79715EEF29BE32806A1D58BB7C5DA76F550AA3D8A1FBFF0EB19CCB1A313D55CDA56C9EC2EF29632387FE8D76E3C0468043E8F663F4860EE12BF2D5B0B7474D6E694F91E6DBE115974A3926F12FEE5E438777CB6A932DF8CD8BEC4D073B931BA3BC832B68D9DD300741FA7BF8AFC47ED2576F6936BA424663AAB639C5AE4F5683423B4742BF1C978238F16CBE39D652DE3FDB8BEFC848AD922222E04A4037C0713EB57A81A23F0C73473FC646CEA306B4BCBC8862F8385DDFA9D4B7FA2C087E879683303ED5BDD3A062B3CF5B3A278A66D2A13F83F44F82DDF310EE074AB6A364597E899A0255DC164F31CC50846851DF9AB48195DED7EA1B1D510BD7EE74D73FAF36BC31ECFA268359046F4EB879F924009438B481C6CD7889A002ED5EE382BC9190DA6FC026E479558E4475677E9AA9E3050E2765694DFC81F56E880B96E7160C980DD98EDD3DFFFFFFFFFFFFFFFFF';
	const g_8192 = 19;

	/** @var \BigInteger Password verifier */
	protected $verifier;

	/** @var \BigInteger Password salt */
	protected $salt;

	/** @var \BigInteger|string */
	protected $N;
	protected $g;
	protected $k;
	protected $v;
	protected $A;

	/** @var \BigInteger|null Secure Random Number */
	protected $b = null;

	/** @var \BigInteger|null */
	protected $B = null;

	protected $M;
	protected $HAMK;

	protected $hash;

	public function __construct($sign, $N = null, $g = null, $hash = 'sha1') {
		if (strpos($sign, '$') !== FALSE) {
			list ($salt, $verifier) = explode ('$', $sign);
			$this->verifier = new BigInteger(bin2hex(base64_decode($verifier)), 16);
			$this->salt     = new BigInteger(bin2hex(base64_decode($salt)), 16);
			}

		$this->hash = $hash;

		$this->N = is_null ($N) ? new BigInteger(self::N_2048, 16) : new BigInteger ($N, 16);
		$this->g = is_null ($g) ? new BigInteger(self::g_2048) : new BigInteger($g);

		#$this->k = new BigInteger($this->hash($this->N->toBytes() . str_pad ($this->g->toBytes(), 256, "\x00", STR_PAD_LEFT)), 16);
		$this->k = new BigInteger($this->hash($this->N->toBytes() . $this->g->toBytes()), 16);

		while (!$this->B || bcmod((string) $this->N, 16) === 0) {
			$this->b = new BigInteger(self::random(), 16);
			$gPowed  = $this->g->powMod($this->b, $this->N);
			$this->B = $this->k->multiply($this->verifier)->add($gPowed)->powMod(new BigInteger(1), $this->N);
			}
		}

	public function challenge($A = '', $user = '') {
		$this->A    = new BigInteger(bin2hex(base64_decode($A)), 16);

		if ($this->A->powMod(new BigInteger(1), $this->N) === 0) {
			throw new \Exception('Client sent invalid key: A mod N == 0.');
			}

		$u   = new BigInteger($this->hash($this->A->toBytes() . $this->B->toBytes()), 16);
		$v   = $this->verifier;
		$avu = $this->A->multiply($v->powMod($u, $this->N));

		$this->S = $avu->modPow($this->b, $this->N);

		$K         = $this->hash($this->S->toBytes(), true);
		$HN	    = new BigInteger ($this->hash($this->N->toBytes()), 16);
		#$Hg	    = new BigInteger ($this->hash(str_pad ($this->g->toBytes(), 256, "\x00", STR_PAD_LEFT)), 16);
		$Hg	    = new BigInteger ($this->hash($this->g->toBytes()), 16);
		$this->M    = $this->hash($HN->bitwise_xor($Hg)->toBytes() . $this->hash($user, true) . $this->salt->toBytes() . $this->A->toBytes() . $this->B->toBytes() . $K, true);
		$this->HAMK = $this->hash($this->A->toBytes() . $this->M . $K, true);

		return array(
			"salt" => base64_encode($this->salt->toBytes()),
			"B" => base64_encode($this->B->toBytes()),
			#"M" => base64_encode($this->M),
			);
		}

	public function get ($key = null, $opts = null) {
		switch (strtolower((string) $key)) {
			case 'm':
				return $this->M;
				break;
			case 'm64':
				return base64_encode ($this->M);
				break;
			case 'h':
			case 'hamk':
			case 'h_amk':
				return $this->HAMK;
				break;
			case 'h64':
			case 'hamk64':
			case 'h_amk64':
				return base64_encode ($this->HAMK);
				break;
			case 'k':
			case 'key':
				return $this->hash ($this->S->toBytes(), true);
				break;
			case 'v':
			case 'verifier':
				return $this->verifier->toBytes();
				break;
			case 's':
			case 'salt':
				return $this->salt->toBytes();
				break;
			}
		}

	/**
	* Hash function to be used in SRP
	*
	* @param $bytes
	* @return string
	*/
	public function hash($bytes, $raw = false) {
		return hash($this->hash, $bytes, $raw);
		}

	private static function random ($bits = 64) {
		/**
		* https://github.com/GeorgeArgyros/Secure-random-bytes-in-PHP
		* Our primary choice for a cryptographic strong randomness function is
		* openssl_random_pseudo_bytes.
		*/

		$str = '';
		if (function_exists('openssl_random_pseudo_bytes') && (version_compare(PHP_VERSION, '5.3.4') >= 0 || substr(PHP_OS, 0, 3) !== 'WIN')) {
			$str = openssl_random_pseudo_bytes($bits, $strong);
			if ($strong) {
				return self::binary2hex($str);
				}
			}

		/*
		* If mcrypt extension is available then we use it to gather entropy from
		* the operating system's PRNG. This is better than reading /dev/urandom
		* directly since it avoids reading larger blocks of data than needed.
		* Older versions of mcrypt_create_iv may be broken or take too much time
		* to finish so we only use this function with PHP 5.3 and above.
		*/
		if (function_exists('mcrypt_create_iv') && (version_compare(PHP_VERSION, '5.3.0') >= 0 || substr(PHP_OS, 0, 3) !== 'WIN')) {
			$str = mcrypt_create_iv($bits, MCRYPT_DEV_URANDOM);
			if ($str !== false) {
				return self::binary2hex($str);
				}
			}

		/*
		* No build-in crypto randomness function found. We collect any entropy
		* available in the PHP core PRNGs along with some filesystem info and memory
		* stats. To make this data cryptographically strong we add data either from
		* /dev/urandom or if its unavailable, we gather entropy by measuring the
		* time needed to compute a number of SHA-1 hashes.
		*/

		$bitsPerRound = 2; // bits of entropy collected in each clock drift round
		$msecPerRound = 400; // expected running time of each round in microseconds
		$hashLength   = 20; // SHA-1 Hash length
		$total        = $bits; // total bytes of entropy to collect

		$handle = @fopen('/dev/urandom', 'rb');
		if ($handle && function_exists('stream_set_read_buffer')) {
			@stream_set_read_buffer($handle, 0);
			}

		do {
			$bytes = ($total > $hashLength) ? $hashLength : $total;
			$total -= $bytes;

			//collect any entropy available from the PHP system and filesystem
			$entropy = rand() . uniqid(mt_rand(), true) . $str;
			$entropy .= implode('', @fstat(@fopen(__FILE__, 'r')));
			$entropy .= memory_get_usage();
			if ($handle) {
				$entropy .= @fread($handle, $bytes);
				}
			else {
				// Measure the time that the operations will take on average
				for ($i = 0; $i < 3; $i++) {
					$c1  = microtime(true);
					$var = sha1(mt_rand());
					for ($j = 0; $j < 50; $j++) {
						$var = sha1($var);
						}
					$c2 = microtime(true);
					$entropy .= $c1 . $c2;
					}

				// Based on the above measurement determine the total rounds
				// in order to bound the total running time.
				$rounds = (int)($msecPerRound * 50 / (int)(($c2 - $c1) * 1000000));

				// Take the additional measurements. On average we can expect
				// at least $bits_per_round bits of entropy from each measurement.
				$iter = $bytes * (int)(ceil(8 / $bitsPerRound));
				for ($i = 0; $i < $iter; $i++) {
					$c1  = microtime();
					$var = sha1(mt_rand());
					for ($j = 0; $j < $rounds; $j++) {
						$var = sha1($var);
						}
					$c2 = microtime();
					$entropy .= $c1 . $c2;
					}

				}
			// We assume sha1 is a deterministic extractor for the $entropy variable.
				$str .= sha1($entropy, true);
			} while ($bits > strlen($str));

		if ($handle) {
			@fclose($handle);
			}

		return self::binary2hex($str);
		}

	private static function binary2hex($string) {
		$chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');

		$length = strlen($string);

		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$b      = ord($string[$i]);
			$result = $result . $chars[($b & 0xF0) >> 4];
			$result = $result . $chars[$b & 0x0F];
			}

		return $result;
		}
	}
