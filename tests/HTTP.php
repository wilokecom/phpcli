<?php

namespace WilokeTest;

trait HTTP {
	public function ajaxPost( array $aArgs ) {
		global $aWILOKEGLOBAL;
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $aWILOKEGLOBAL['ajaxUrl'] );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $aArgs ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
		$output = curl_exec( $ch );

		if ( curl_errno( $ch ) ) {
			$errMsg = curl_error( $ch );
		}
		curl_close( $ch );

		if ( isset( $errMsg ) ) {
			return [
				'success' => false,
				'msg'     => $errMsg
			];
		}

		return json_decode( $output, true );
	}

	public function restGET( $endpoint, array $aArgs ) {
		global $aWILOKEGLOBAL;
		$url = trailingslashit( $aWILOKEGLOBAL['restBaseUrl'] ) . untrailingslashit( $endpoint );
		$url = add_query_arg( [ 'query' => http_build_query( $aArgs ) ], $url );

		$response = wp_remote_get( $url, [ 'sslverify' => false ] );

		if ( is_wp_error( $response ) ) {
			return [
				'status' => false,
				'msg'    => $response->get_error_message()
			];
		}

		$body = wp_remote_retrieve_body( $response );
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return [
				'status' => false,
				'msg'    => sprintf( 'The code status is not 200. %s', $body )
			];
		}

		$aBody = json_decode( $body, true );

		if ( isset( $aBody['code'] ) && $aBody['code'] == 'rest_no_route' ) {
			return [
				'status' => false,
				'msg'    => $aBody['message']
			];
		}

		return $aBody;
	}

	protected function restAPI( $endpoint, $method = 'POST', array $aArgs = [] ) {
		global $aWILOKEGLOBAL;
		$ch  = curl_init();
		$url = trailingslashit( $aWILOKEGLOBAL['restBaseUrl'] ) . trailingslashit( $endpoint );
		if ( $method !== 'POST' && ! empty( $aArgs ) ) {
			$url = add_query_args( $aArgs, $url );
		}

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt( $ch, CURLOPT_POST, 1 );

		if ( $method == 'POST' ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $aArgs ) );
		}
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
		$output = curl_exec( $ch );

		if ( curl_errno( $ch ) ) {
			$errMsg = curl_error( $ch );
		}
		curl_close( $ch );

		if ( isset( $errMsg ) ) {
			return [
				'status' => false,
				'msg'    => $errMsg
			];
		}

		$aOutput = is_array( $output ) ? $output : json_decode( $output, true );
		if ( isset( $aOutput['code'] ) && $aOutput['code'] == 'rest_no_route' ) {
			return [
				'status' => false,
				'msg'    => $aOutput['message']
			];
		}

		return $aOutput;
	}

	public function restPOST( $endpoint, array $aArgs = [] ) {
		return $this->restAPI( $endpoint, 'POST', $aArgs );
	}

	public function restPUT( $endpoint, array $aArgs = [] ) {
		return $this->restAPI( $endpoint, 'PUT', $aArgs );
	}

	public function restDELETE( $endpoint, array $aArgs = [] ) {
		return $this->restAPI( $endpoint, 'DELETE', $aArgs );
	}

	public function restPATCH( $endpoint, array $aArgs = [] ) {
		return $this->restAPI( $endpoint, 'PATCH', $aArgs );
	}
}
