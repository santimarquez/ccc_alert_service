<?php

/**
 * 
 * Section to set the static data.
 * Set the variable $this->resources_required as true
 * in case that data as the toAddress will be received
 * dynamically.
 * 
 * Otherwise, it will be sent to the server admin.
 * 
 */
$this->resources_required = false;
$this->footer_layout = 'warning';
$this->header_layout = 'warning';

/**
 * 
 * Notification configurable variables
 * 
 */

//Mail subject
$this->mail->Subject = '¡Buenas noticias! Mira que hemos encontrado';

//Generate the dynamic bodies content

$this->dynamic_body = '<tr><td> ¡Hola! </td></tr>
                      <tr><td> Hemos encontrado lo que ves a continuación y estamos casi seguros de que te va a gustar :)</td></tr>
                      <tr><td> <i> Puedes hacer clic directamente en la foto o el título </i> para acceder al anuncio. </td></tr><br>';

foreach ($resources->containerable as $ad) {
    $this->dynamic_body .= '<tr class="container">
                                <td>
                                    <table class="advertisement">';
    $this->dynamic_body .= '<tr>
                                            <td>
                                                <a href="' . $ad->url . '">
                                                    <img width="100%" height="auto"
                                                        src="' . $ad->pic_url . '" 
                                                        alt="alertacoches.es">
                                                </a>
                                            </td>
                                        </tr>';
    $this->dynamic_body .= '<tr><td><h3><a href="' . $ad->url . '">' . $ad->title . '</a></h3></td></tr>';
    $this->dynamic_body .= '<tr>
                                            <td>
                                            <table class="advertisement-detail">
                                                <tr>
                                                    <td><b>Precio: </b>' . $ad->price . '€</td>
                                                    <td><b>Km: </b>' . $ad->kms . '</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Año: </b>' . $ad->year . '</td>
                                                    <td><b>Transmisión: </b>' . $ad->gear . '</td>
                                                </tr>
                                            </table>
                                            <td>
                                        </tr>';
    $this->dynamic_body .= '        </table>
                                </td>
                            </tr>
                            <tr class="separator">
                                <td>
                                <img width="100%" height="2px" 
                                     src="https://i.imgur.com/Za3ZGa4.png">
                                </td>
                            </tr>';
}

$this->dynamic_body .= '<tr><td>Te hemos enviado este email, puesto que este/s coche/s tienen un precio inferior al que has configurado en tu alarma. Si esto no es correcto, por favor, remítenoslo <a href="mailto:' . $_ENV["SUPPORT_EMAIL"] . '">aquí</a>. </td></tr>';


//Mail attachments
//$this->addAttachment(Log::getFileName());

/**
 * 
 * Set all the information.
 * THIS SECTION DOESN'T NEED TO BE MODIFIED.
 * 
 */
if (empty($resources) && $this->resources_required) {
    return false;
}

/**
 * Set the Address to whom the email
 * will be sent
 */
if (count($resources->toAddress) < 1) {
    $this->setAddress($_ENV['ADMIN_MAIL'], $_ENV['ADMIN_NAME']);
} else {
    foreach ($resources->toAddress as $data) {
        $this->setAddress($data->email, $data->email);
    }
}
