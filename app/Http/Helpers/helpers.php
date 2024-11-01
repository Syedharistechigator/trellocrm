<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// convert 1000 to K
function thousand_format($number)
{
    $number = (int)preg_replace('/[^0-9]/', '', $number);
    if ($number >= 1000) {
        $rn = round($number);
        $format_number = number_format($rn);
        $ar_nbr = explode(',', $format_number);
        $x_parts = array('K', 'M', 'B', 'T', 'Q');
        $x_count_parts = count($ar_nbr) - 1;
        $dn = $ar_nbr[0] . ((int)$ar_nbr[1][0] !== 0 ? '.' . $ar_nbr[1][0] : '');
        $dn .= $x_parts[$x_count_parts - 1];

        return $dn;
    }
    return $number;
}

function get_percentage($total, $number)
{
    if ($total > 0) {
        return round(($number * 100) / $total, 2);
    } else {
        return 0;
    }
}

// encrypet function
function cxmEncrypt($string, $key)
{
    $result = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result .= $char;
    }

    return base64_encode($result);
}

//Decrypt function
function cxmDecrypt($string, $key)
{
    $result = '';
    $string = base64_decode($string);

    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result .= $char;
    }

    return $result;
}

// email Function
function sendEmail($options)
{

    $brand = DB::table('brands')->where('brand_key', $options['brandKey'])->first();
    $marchant = DB::table('payment_methods')->where('id', $brand->merchant_id)->first();

    $merchantName = $marchant->merchant;

    $emailData = array(
        'itemPrice' => $options['amount'],
        'description' => $options['description'],
        'paidInvoiceId' => $options['paidInvoiceId'],
        'name' => $options['clientName'],
        'brandName' => $brand->name,
        'marchant' => $merchantName
    );

    // $host = $brand->smtp_host ? $brand->smtp_host : 'smtp.gmail.com';
    // $port = $brand->smtp_port ? $brand->smtp_port : 465;
    // $username = $brand->smtp_email ? $brand->smtp_email: 'laraveldeveloper26@gmail.com';
    // $password = $brand->smtp_password ? $brand->smtp_password : 'iltmcenvgqlpzdtw';

    $host = 'smtp.gmail.com';
    $port = 465;
    $username = 'mail.logoaspire@gmail.com';
    $password = 'pbqlcyqlgqhbeoag';

    require base_path("vendor/autoload.php");
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->SMTPDebug = 0;
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = $host;                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = $username;                     //SMTP username
        $mail->Password = $password;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = $port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($brand->smtp_email, $brand->name);

        $mail->addAddress($options['to']);       //Add a recipient
        // $mail->addAddress('developer.michael.09@gmail.com');       //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        $mail->addBCC('naeem4every1@gmail.com');
        $mail->addBCC('faraz.hussain@techigator.com');
        // $mail->addBCC('developer.michael.09@gmail.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $options['subject'];
        $emailBody = view('mail.admin.payment_confirmation', ['data' => $emailData])->render();
        $mail->Body = $emailBody;

        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        Log::driver('email')->debug('payment_confirmation , details = ' . json_encode($options));

        //echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        Log::driver('email')->debug('payment_confirmation , details = ' . json_encode($options) . ' , error = ' . $e . ' /' . $mail->ErrorInfo);
    }
}


if (!function_exists('moneyFormat')) {
    function moneyFormat($amount)
    {
        return '$' . number_format($amount, 2);
    }
}




if (! function_exists('redirectBackConditional')) {
    /**
     * Custom helper for conditional back redirection.
     *
     * @param  string|null  $route
     * @param  array  $errors
     * @return \Illuminate\Http\RedirectResponse
     */
    function redirectBackConditional($route = null, $errors = [])
    {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        if ($previousUrl === $currentUrl) {
            $defaultRoute = auth()->guard('admin')->check() ? 'admin.dashboard' : 'dashboard';
            return redirect()->route($route ?? $defaultRoute)->withErrors($errors ?: ['error' => 'Access denied: This route not accessible from here.']);
        }

        return redirect()->back()->withErrors($errors ?: ['error' => 'Access denied: This route not accessible from here.']);
    }
}
