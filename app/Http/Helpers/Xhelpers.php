<?php 
    use Illuminate\Support\Facades\DB;
    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

 // convert 1000 to K
    function thousand_format($number) {
        $number = (int) preg_replace('/[^0-9]/', '', $number);
        if ($number >= 1000) {
            $rn = round($number);
            $format_number = number_format($rn);
            $ar_nbr = explode(',', $format_number);
            $x_parts = array('K', 'M', 'B', 'T', 'Q');
            $x_count_parts = count($ar_nbr) - 1;
            $dn = $ar_nbr[0] . ((int) $ar_nbr[1][0] !== 0 ? '.' . $ar_nbr[1][0] : '');
            $dn .= $x_parts[$x_count_parts - 1];
    
            return $dn;
        }
        return $number;
    }

    function get_percentage($total, $number)
    {
        if ( $total > 0 ) {
            return round(($number * 100) / $total, 2);
        } else {
            return 0;
        }
    }

    // encrypet function
    function cxmEncrypt($string, $key) {
        $result = '';
        for($i=0; $i<strlen($string); $i++) {
          $char = substr($string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char)+ord($keychar));
          $result.=$char;
        }
       
        return base64_encode($result);
    }

    //Decrypt function
    function cxmDecrypt($string, $key) {
        $result = '';
        $string = base64_decode($string);
       
        for($i=0; $i<strlen($string); $i++) {
          $char = substr($string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char)-ord($keychar));
          $result.=$char;
        }
       
        return $result;
    }

    // email Function 
    function sendEmail($options){
        
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
        
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        $host = $brand->smtp_host ? $brand->smtp_host : 'smtp.gmail.com';
        $port = $brand->smtp_port ? $brand->smtp_port : 465;
        
        //$username = $brand->smtp_email ? $brand->smtp_email: 'mail.logoaspire@gmail.com';
        //$password = $brand->smtp_password ? $brand->smtp_password : 'pbqlcyqlgqhbeoag';
        $username = $brand->smtp_email ? $brand->smtp_email: 'laraveldeveloper26@gmail.com';
        $password = $brand->smtp_password ? $brand->smtp_password : 'iltmcenvgqlpzdtw';

        // SMTP configurations
        $mail->SMTPDebug = 2;
        $mail->isSMTP(); 
        $mail->Host = $host; 
        $mail->SMTPAuth = true; 
        //$mail->SMTPAuth = false;
        $mail->Username = $username; 
        $mail->Password = $password; 
        $mail->SMTPSecure = 'ssl'; 
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port        = $port; 
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        
        
        // Sender info  
        $mail->setFrom($username, $brand->name); 
        
        // Add a recipient  
        $mail->addAddress($options['to']);  
        
        // Add cc or bcc   
        //$mail->addCC('cc@example.com');  
        $mail->addBCC('mursaleen.techigator@gmail.com');  

        // Email subject  
        $mail->Subject = $options['subject'];  
        
        // Set email format to HTML  
        $mail->isHTML(true);  
        
        
        $emailBody =  view('mail.admin.payment_confirmation', ['data' => $emailData])->render();
        $mail->Body = $emailBody;  

        
        // Send email
        $mail->send();

        // if(!$mail->send()){  
        //     echo 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;  
        // }else{  
        //     echo 'Message has been sent.';  
        // }


        
    }


    if (! function_exists('moneyFormat')) {
        function moneyFormat($amount)
        {
            return '$' . number_format($amount, 2);
        }
    }




