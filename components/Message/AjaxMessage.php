<?php

#namespace WilokeTest;

/**
 * Class AjaxMessage
 * @package HSBlogCore\Helpers
 */
class AjaxMessage extends AbstractMessage
{
	/**
	 * @param       $msg
	 * @param       $code
	 * @param array $aAdditional
	 *
	 * @return void
	 */
	public function retrieve( $msg, $code, $aAdditional = [] ) {
		if ( $code == 200 ) {
			$this->success( $msg, $aAdditional );
		} else {
			$this->error( $msg, $code, $aAdditional );
		}
	}

	private function sendJson( array $aMessage, $statusCode ) {
		if ( ! headers_sent() ) {
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
			if ( null !== $statusCode ) {
				status_header( $statusCode );
			}
		}

		echo wp_json_encode( $aMessage );

		die;
	}

	/**
	 * @param       $msg
	 * @param array $aAdditional
	 *
	 * @return void
	 */
	public function success( $msg, $aAdditional = [] ) {
		$aData = [
			'msg' => $msg
		];

		$aData = array_merge( $aData, $aAdditional );

		$this->sendJson( $aData, 200 );
	}

	/**
	 * @param       $msg
	 * @param array $aAdditional
	 * @param       $code
	 *
	 * @return void
	 */
	public function error( $msg, $code, array $aAdditional = [] ) {
		$aData = [
			'msg' => $msg
		];

		$aData = array_merge( $aData, $aAdditional );

		$this->sendJson( $aData, $code );
	}
}
