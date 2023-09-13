<?php

/**
 * Plugin Name: Cybersource Payment Gateway for WooCommerce
 * Description: Integración de Cybersource como pasarela de pago para WooCommerce.
 * Version: 1.0
 * Author: DE-BLACK-KEN
 */

function agregar_pasarela_cybersource($gateways)
{
    $gateways[] = 'WC_Gateway_Cybersource';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'agregar_pasarela_cybersource');

class WC_Gateway_Cybersource extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'cybersource';
        $this->method_title = 'Cybersource Payment Gateway';
        $this->title = 'Pagar con Cybersource';
        $this->has_fields = true;

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->get_option('enabled');
        $this->merchant_id = $this->get_option('merchant_id');
        // Agrega más configuraciones según tus necesidades.

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Habilitar/Deshabilitar',
                'type' => 'checkbox',
                'label' => 'Habilitar Cybersource Payment Gateway',
                'default' => 'no',
            ),
            'merchant_id' => array(
                'title' => 'ID de Comerciante',
                'type' => 'text',
                'description' => 'Ingrese su ID de Comerciante de Cybersource.',
                'default' => '',
            ),
            // Agrega más campos según tus necesidades.
        );
    }

    public function process_payment($order_id)
    {
        // Aquí deberías incluir la lógica para enviar la información de pago a Cybersource y procesar el pago.

        // Después de procesar el pago, puedes actualizar el estado del pedido y redirigir al usuario a una página de éxito o fracaso.
    }
}

add_action('woocommerce_receipt_cybersource', 'process_payment');

?>

<!-- Cybersource Implementation #1-->

<!-- Configuración de las Credenciales -->
$merchant_id = 'tu_id_de_comerciante';
$api_key = 'tu_clave_de_api';

<!-- Recopilación de Datos de Pago -->
$card_number = $_POST['numero_tarjeta'];
$card_expiry = $_POST['fecha_vencimiento'];
$card_cvv = $_POST['codigo_cvv'];
$amount = $_POST['cantidad'];

<!-- Construcción de la Solicitud a Cybersource -->
$cybersource_url = 'https://api.cybersource.com/';
$request_data = [
'card_number' => $card_number,
'card_expiry' => $card_expiry,
'card_cvv' => $card_cvv,
'amount' => $amount,
// Otros datos necesarios para la transacción.
];

$ch = curl_init($cybersource_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Content-Type: application/json',
'Authorization: Basic ' . base64_encode("$merchant_id:$api_key"),
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
// Manejar errores de cURL.
// Registra o notifica del error y toma medidas adecuadas.
}

curl_close($ch);

// Analizar la respuesta de Cybersource y tomar acciones según sea necesario.


<!-- Cybersource Implementation #2-->

<!-- Obtén los Datos del Pedido -->
$order = wc_get_order($order_id);
$order_total = $order->get_total();
// Otros datos del pedido que puedas necesitar.

<!-- Envía la Información de Pago a Cybersource -->
// ... Código de solicitud a Cybersource ...

// Analizar la respuesta de Cybersource
if ($response) {
$response_data = json_decode($response, true);

// Verificar si la transacción fue exitosa
if (isset($response_data['status']) && $response_data['status'] === 'success') {
// Marcar el pedido como completado
$order->payment_complete();

// Redirigir al usuario a una página de éxito
return [
'result' => 'success',
'redirect' => $this->get_return_url($order),
];
} else {
// La transacción falló, maneja el error adecuadamente
wc_add_notice('Hubo un problema con la transacción. Por favor, inténtelo de nuevo.', 'error');
return;
}
} else {
// Error en la solicitud a Cybersource
wc_add_notice('Hubo un error al procesar el pago. Por favor, inténtelo de nuevo más tarde.', 'error');
return;
}