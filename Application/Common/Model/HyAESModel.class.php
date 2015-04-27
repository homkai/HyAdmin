<?php
namespace Common\Model;
class HyAESModel{

	/**
	 * @var string Encryption key
	 */
	private $key;
	
	/**
	 * @var int Mcrypt initialization vector size
	 */
	private $ivSize;
	
	/**
	 * @var resource mcrypt module resource
	 */
	private $mcryptModule;
	
	/**
	 * @var int encryption block size
	 */
	private $blockSize;
	
	/**
	 * @param string $key encryption key should be 16, 24 or 32 characters long form 128, 192, 256 bit encryption
	 */
	public function __construct($key)
	{
		$this->mcryptModule = mcrypt_module_open('rijndael-256', '', 'cbc', '');
		$this->key = $key;
	}
	
	/**
	 * @param $data
	 * @return string
	 */
	public function encrypt($data)
	{
		$this->ivSize = mcrypt_enc_get_iv_size($this->mcryptModule);
		$iv = mcrypt_create_iv($this->ivSize, MCRYPT_DEV_URANDOM);
		mcrypt_generic_init($this->mcryptModule, $this->key, $iv);
		$encrypted = mcrypt_generic($this->mcryptModule, $this->pad($data));
		return base64url_encode($iv. $encrypted);
	}
	
	/**
	 * @param $encryptedData
	 * @return string
	 */
	public function decrypt($data)
	{
		$this->ivSize = mcrypt_enc_get_iv_size($this->mcryptModule);
		$data=base64url_decode($data);
		$iv = substr($data, 0, $this->ivSize);
		mcrypt_generic_init($this->mcryptModule, $this->key, $iv);
		$decrypted = mdecrypt_generic($this->mcryptModule, substr($data, $this->ivSize));
		return $this->unpad($decrypted);
	}
	
	private function pad($data)
	{
		$this->blockSize = mcrypt_enc_get_block_size($this->mcryptModule);
		$pad = $this->blockSize - (strlen($data) % $this->blockSize);
		return $data . str_repeat(chr($pad), $pad);
	}
	
	private function unpad($data)
	{
		$pad = ord($data[strlen($data) - 1]);
		return substr($data, 0, -$pad);
	}
	
	public function __destruct()
	{
		mcrypt_generic_deinit($this->mcryptModule);
		mcrypt_module_close($this->mcryptModule);
	}
	
	public function encrypt_fixed($data) {
		mcrypt_generic_init($this->mcryptModule, $this->key, strrev(preg_replace('/\d/', 'h', $this->key)));
		$encrypted = mcrypt_generic($this->mcryptModule, $this->pad($data));
		return base64url_encode($encrypted);
	}
	
	public function decrypt_fixed($data,$key) {
		mcrypt_generic_init($this->mcryptModule, $this->key, strrev(preg_replace('/\d/', 'h', $this->key)));
		$decrypted = mdecrypt_generic($this->mcryptModule, base64url_decode($data));
		return $this->unpad($decrypted);
	}
}