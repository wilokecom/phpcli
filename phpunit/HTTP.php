<?php

namespace WilokeTest;

trait HTTP
{
	protected $aAuth;

	protected $aAdminInfo
		= [
			'username' => 'admin'
		];

	protected $isEnableUserLogin;
	protected $oUser;
	protected $userId;
	protected $restBase;
	protected $ajaxUrl;
	protected $isAjax = false;

	protected function getAdminId()
	{
		$this->oUser = get_user_by('login', $this->aAdminInfo['username']);
		$this->userId = $this->oUser->ID;
		return $this;
	}

	protected function setUserLoggedIn()
	{
		wp_set_current_user($this->getAdminId()->userId);
		return $this;
	}

	protected function configureAPI()
	{
		global $aWILOKEGLOBAL;
		$this->restBase = trailingslashit($aWILOKEGLOBAL['restBaseUrl']);
		$this->ajaxUrl = $aWILOKEGLOBAL['ajaxUrl'];

		$this->aAuth = [
			'username' => $aWILOKEGLOBAL['ADMIN_USERNAME'],
			'password' => $aWILOKEGLOBAL['ADMIN_AUTH_PASS'],
		];

		$this->aAdminInfo = [
			'username' => $aWILOKEGLOBAL['ADMIN_USERNAME']
		];

		return $this;
	}

	public function ajaxPost(array $aArgs)
	{
		$this->isAjax = true;
		return $this->restAPI('', 'POST', $aArgs);
	}

	public function ajaxGet(array $aArgs)
	{
		$this->isAjax = true;
		return $this->restAPI('', 'GET', $aArgs);
	}

	protected function enableUserLogin($status = true)
	{
		$this->isEnableUserLogin = $status;
		$this->setUserLoggedIn();

		return $this;
	}

	protected function restAPI($endpoint, $method = 'POST', array $aArgs = [])
	{
		$ch = curl_init();
		$url = $this->isAjax ? $this->ajaxUrl : $this->restBase . trailingslashit($endpoint);

		if ($method !== 'POST' && !empty($aArgs)) {
			$url = add_query_args($aArgs, $url);
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		if ($this->isEnableUserLogin) {
			curl_setopt($ch, CURLOPT_USERPWD, $this->aAuth['username'] . ':' . $this->aAuth['password']);
		}

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POST, 1);

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aArgs));
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		$output = curl_exec($ch);

		if (curl_errno($ch)) {
			$errMsg = curl_error($ch);
		}
		curl_close($ch);

		$this->enableUserLogin(false);
		$this->isAjax = false;

		if (isset($errMsg)) {
			return [
				'status' => false,
				'msg'    => $errMsg
			];
		}

		$aOutput = is_array($output) ? $output : json_decode($output, true);
		if (isset($aOutput['data']) && $aOutput['data']['status'] == 200) {
			return [
				'status' => false,
				'msg'    => $aOutput['message']
			];
		}

		return $aOutput;
	}

	public function restGET($endpoint, array $aArgs = [])
	{
		return $this->restAPI($endpoint, 'GET', $aArgs);
	}

	public function restPOST($endpoint, array $aArgs = [])
	{
		return $this->restAPI($endpoint, 'POST', $aArgs);
	}

	public function restPUT($endpoint, array $aArgs = [])
	{
		return $this->restAPI($endpoint, 'PUT', $aArgs);
	}

	public function restDELETE($endpoint, array $aArgs = [])
	{
		return $this->restAPI($endpoint, 'DELETE', $aArgs);
	}

	public function restPATCH($endpoint, array $aArgs = [])
	{
		return $this->restAPI($endpoint, 'PATCH', $aArgs);
	}
}
