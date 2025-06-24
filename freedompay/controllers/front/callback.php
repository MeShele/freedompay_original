<?php

class FreedomPayCallbackModuleFrontController extends ModuleFrontController
{
    public function postProcess(): void
    {
        $content = file_get_contents( 'php://input' );

        if ( ! $content ) {
            die(  'There was no data passed with the request.' );
        }

        if ( $content[0] === '{' ) {
            $data = json_decode( $content, true );
        } else {
            parse_str( $content, $data );
        }

        if ( $data == null || ( is_array( $data ) && count( $data ) == 0 ) ) {
            die( 'The input data was incorrect.' );
        }

        if ( ! $this->validate_signature( $data ) ) {
            die( 'Data signature was invalid.' );
        }

        if (! empty($data['pg_order_id'])  && ! empty($data['pg_result'])) {
            if ($data['pg_result'] != 1) {
                return;
            }

            $order = new Order($data['pg_order_id']);
            $order->current_state = (int)Configuration::get('PS_OS_WS_PAYMENT');
            $order->update();
        }

        die('OK');

    }

    private function validate_signature(array $data): bool
    {
        if ( ! key_exists( 'pg_sig', $data ) ) {
            return false;
        }
        $signature = $data['pg_sig'];
        unset( $data['pg_sig'] );
        $actual_sig = $this->generate_signature( $data, 'index.php' );

        return $signature === $actual_sig;
    }

    public function generate_signature( array $data, string $route_url ): string
    {
        ksort( $data );
        array_unshift( $data, $route_url );
        $data[] = Configuration::get('merchant_secret');
        return md5( join( ';', $data ) );
    }
}