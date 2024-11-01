<?php

namespace App\Traits;

use App\Models\Brand;
use Illuminate\Support\Facades\Artisan;

trait BrandEmailConfigurationTrait
{
    public function BrandEmailConfig($brand_key, $sandbox = true)
        /**TRUE FOR SANDBOX*/
    {
        $brand = Brand::where('brand_key', $brand_key)
            ->where('smtp_host', "!=", null)
            ->where('smtp_port', "!=", null)
            ->where('smtp_email', "!=", null)
            ->where('smtp_password', "!=", null)
            ->where('status', 1)->first();

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }
        if ($sandbox) {

            $emailConfig = [
                'transport' => 'smtp',
                'host' => 'sandbox.smtp.mailtrap.io', // Replace with your SMTP host
                'port' => 2525, // Replace with your SMTP port
                'encryption' => 'tls', // Replace with your encryption method
                'username' => '0968af34137f88', // Replace with your SMTP username
                'password' => 'b6a436d5fe8c62', // Replace with your SMTP password
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'), // Replace with your EHLO domain
            ];

//            $host = "sandbox.smtp.mailtrap.io";
//            $port = 2525;
//            $username = "0968af34137f88";
//            $address = "hello@example.com";
//            $password = "b6a436d5fe8c62";
//            $name = "Techigator-Crm";
//            $encryption = 'tls';
        } else {

            $emailConfig = [
                'transport' => 'smtp',
                'host' => $brand->smtp_host,
                'port' => $brand->smtp_port,
                'encryption' => 'ssl',
                'username' => $brand->smtp_email,
                'password' => $brand->smtp_password,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ];
//
//            $host = $brand->smtp_host;
//            $port = $brand->smtp_port;
//            $username = $brand->smtp_email;
//            $address = $brand->smtp_email;
//            $password = $brand->smtp_password;
//            $name = $brand->name;
//            $encryption = 'ssl';
        }
// Set the email configuration dynamically
        config(['mail.mailers.smtp' => $emailConfig]);
        // Set the SMTP configuration as environment variables
//        putenv("MAIL_MAILER=smtp");
//        putenv("MAIL_HOST={$host}");
//        putenv("MAIL_PORT={$port}");
//        putenv("MAIL_USERNAME={$username}");
//        putenv("MAIL_PASSWORD={$password}");
//        putenv("MAIL_ENCRYPTION={$encryption}");
//        putenv("MAIL_FROM_ADDRESS={$address}");
//        putenv("MAIL_FROM_NAME={$name}");

        // Optional: Load the new environment variables
//        if (function_exists('env')) {
//            $dotenv = \Dotenv\Dotenv::createImmutable(base_path());
//            $dotenv->load();
//        }
//        dd(
//            env('MAIL_MAILER'),
//            env('MAIL_HOST'),
//            env('MAIL_PORT'),
//            env('MAIL_USERNAME'),
//            env('MAIL_PASSWORD'),
//            env('MAIL_FROM_ADDRESS'),
//            env('MAIL_ENCRYPTION'),
//            env('MAIL_FROM_NAME')
//        );
        return response()->json(['success' => 'Email configuration set successfully'], 200);
    }

}
