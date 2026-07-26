<?php

namespace App\Auth\Infrastructure\EventListener;

use App\Shared\Application\Port\EventListenerInterface;
use App\Shared\Application\Port\MailerInterface;
use App\Shared\Domain\Event\DomainEventInterface;
use App\Auth\Domain\Events\PasswordRecoveryRequested;
use App\Shared\Application\DTO\MailDTO;



final class SendPasswordRecoveryEmail implements EventListenerInterface {
    public function __construct(
        private readonly MailerInterface $mailer
    ) {}

    public function handle(DomainEventInterface $event): void {
        if(!$event instanceof PasswordRecoveryRequested) return;
        $host = getenv('ORIGIN_ONE');
        $token = $event->tokenRecoveryValue()->value();
        $body =<<<HTML
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Recuperación de la cuenta</title>
            </head>

            <body
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #13011d; width: 100%;">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#13011d" style="table-layout: fixed;">
                    <tr>
                        <td align="center" style="padding: 20px 10px;">

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
                                <tr>
                                    <td align="left" style="padding: 10px 24px;">
                                        <h1
                                            style="font-size: 32px; color: #ffffff; font-weight: 400; margin: 0; font-family: 'Segoe UI', sans-serif;">
                                            JK<strong style="font-weight: 900;">App</strong>
                                        </h1>
                                    </td>
                                </tr>

                                <tr>
                                    <td height="10"></td>
                                </tr>

                                <tr>
                                    <td align="center" bgcolor="#0d0613" style="padding: 40px 30px; border-radius: 8px;">

                                        <h2
                                            style="font-size: 24px; color: #ffffff; margin: 0 0 20px 0; text-align: center; font-family: 'Segoe UI', sans-serif;">
                                            Recupera tu cuenta
                                        </h2>

                                        <p
                                            style="font-size: 16px; color: #ffffff; margin: 0 0 10px 0; text-align: center; line-height: 1.5;">
                                            Gracias por usar <strong>JKApp</strong>
                                        </p>

                                        <p
                                            style="font-size: 16px; color: #ffffff; margin: 0 0 30px 0; text-align: center; line-height: 1.5; padding: 0 10%;">
                                            Si solicitaste cambiar tu contraseña, da click en el boton de abajo para confirmar que eres tú.
                                        </p>

                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" bgcolor="#0d166b" style="border-radius: 8px;">
                                                    <a href="{$host}?/Auth/ResetPassword/reset-password.html?confirmation={$token}"
                                                        target="_blank"
                                                        style="text-transform: uppercase; font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 15px 30px; display: inline-block; font-family: 'Segoe UI', sans-serif;">
                                                        Recuperar Cuenta
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <p 
                                            style="font-size: 14px; color: #ffffff; margin: 30px 0 0 0; text-align: center; line-height: 1.5; padding: 0 10%;"
                                        >
                                            Si tu no solicitaste nada, puedes ignorar este mensaje.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        HTML;
    
        $mailDTO = new MailDTO(
            [$event->email()->value()],
            "Solicitud para cambio de contraseña",
            $body,
            "Confirmación para el cambio de contraseña"
        );

        $this->mailer->send($mailDTO);
    }   
}