<?php

#namespace WilokeTest;

/**
 * Class AjaxMessage
 * @package HSBlogCore\Helpers
 */
class RestMessage extends AbstractMessage
{
	/**
	 * @param       $msg
	 * @param       $code
	 * @param array $aAdditional
	 *
	 * @return \WP_REST_Response|array
	 */
	public function retrieve($msg, $code, $aAdditional = [])
	{
		if ($code == 200) {
			return $this->success($msg, $aAdditional);
		} else {
			return $this->error($msg, $code);
		}
	}

	/**
	 * @param       $msg
	 * @param array $aAdditional
	 *
	 * @return \WP_REST_Response
	 */
	public function success($msg, $aAdditional = [])
	{
		$aData = [
			'msg'    => $msg
		];
		$aData = array_merge($aAdditional, $aData);

		if (empty($aAdditional)) {
			throw new \Exception('You must set items/item as empty');
		}

		return (new \WP_REST_Response($aData, 200));
	}

	/**
	 * @param $msg
	 * @param $code
	 *
	 * @return \WP_REST_Response
	 */
	public function error($msg, $code)
	{
		return new \WP_REST_Response([
			'msg' => $msg
		], $code);
	}
}
