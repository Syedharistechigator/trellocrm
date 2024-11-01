<?php $include_path = '../'; ?>
<?php 
session_start();
// if(!isset($_SESSION['user_id'])){
//   header ('Location: /login.php');
//   exit;

// }

// if(isset($_SESSION['base_id']) && !empty($_SESSION['base_id']) && $_SESSION['base_id'] != 0){
//   $cxmParamId = $_SESSION['base_id'];

// } else {
//   $cxmParamId = $_SESSION['user_id'];

// }

include($include_path.'include/config.php');
include($include_path.'include/functions.php');

// if(chkUserGmailAPIOAuthApp($_SESSION['user_id'])){
//   header ('Location: '.BASE_URL.'profile.php');
//   exit;
// }

require_once $include_path.'Email_using_google_api/config.php';

$arr_token = json_decode($email->access_token, true);

if (isset($arr_token['access_token'])) {
    $token = $arr_token['access_token'];
} else {
    // Handle the case when 'access_token' is not present in the decoded JSON
    dd('Access token not found in the decoded JSON.');
}

$cxmPageToken = isset($_GET['pg'])?$_GET['pg']: '';
$cxmIsPrevPage = isset($_GET['prev'])?$_GET['prev']: '';

if(!empty($cxmPageToken)){
  $_SESSION['cxmPrevPages'][] = $cxmPageToken;
  $cxmPrevPages = array_unique($_SESSION['cxmPrevPages']);
  
  if(!empty($cxmIsPrevPage)){
    array_pop($cxmPrevPages);
    unset($_SESSION['cxmPrevPages']);
    $_SESSION['cxmPrevPages'] = $cxmPrevPages;

  }

} else {
  unset($_SESSION['cxmPrevPages']);

}

// $cxmQueryParameters = '?maxResults=10';
$cxmQueryParameters = '?maxResults=10&q=in:inbox';
use Hybridauth\Provider\Google;
use App\Models\EmailConfiguration;
$cxmBrandUsers = EmailConfiguration::where('brand_key',$email->brand_key)->get();
// $cxmTotal = $cxmTotalUsers->num_rows;
// echo $cxmTotal;
//

$cxmQs = isset($_GET['qs'])?$_GET['qs'] :'';

if(isset($cxmQs) && !empty($cxmQs) && $email->parent_id != 0){
  $cxmQueryParameters .= rawurlencode(' '.$cxmQs);

}
elseif(isset($cxmQs) && !empty($cxmQs) && $email->parent_id == 0) {
  $cxmQueryParameters .= '&q='.rawurlencode(' '.$cxmQs);

}

if(!empty($cxmPageToken)){
  $cxmQueryParameters .= '&pageToken='.$cxmPageToken;

}

try {
  // header('Content-Type: application/json'); // Specify the type of data
  $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages'.$cxmQueryParameters); // Initialise cURL
  $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization)); // Inject the token into the header
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
  $result = curl_exec($ch); // Execute the cURL statement
  curl_close($ch); // Close the cURL connection

  $resultArr = json_decode($result);

  if(isset($resultArr->error->status) && $resultArr->error->status === 'UNAUTHENTICATED'){
    throw new Exception("Token Expire");
  }  
  
} catch (Exception $e) {
  if( !$e->getCode() ) {
           $config = [
                'callback' => route('handle.google.call.back'),
                'keys'     => [
                    'id' => $email->client_id,
                    'secret' => $email->client_secret
                ],
                'scope' => 'https://mail.google.com',
                'authorize_url_parameters' => [
                    'approval_prompt' => 'force', // to pass only when you need to acquire a new refresh token.
                    'access_type' => 'offline',
                    'state' =>base64_encode('devMichael'),

                ]
            ]; 
        $adapter = new Google( $config );
        $adapter->authenticate();
        $refresh_token = $adapter->get_refersh_token();    
    try {
      $response = $adapter->refreshAccessToken([
        "grant_type" => "refresh_token",
        "refresh_token" => $refresh_token,
        'id' => $email->client_id,
        'secret' => $email->client_secret
      ]);

    } catch (Exception $e) {
      $cxm_error_message = base64_encode($e->getMessage());
      header ('Location: /404.php?cxm_access_token='.base64_encode(0).'&cxm_err_msg='.$cxm_error_message);
      exit;
    }
    
    $data = (array) json_decode($response);
    $data['refresh_token'] = $refresh_token;

    $db->update_access_token(json_encode($data), $cxmParamId);

    header ('Location: '.BASE_URL.'views/email-box/inbox.php');

  } else {
      echo $e->getMessage(); //print the error
  }
}

$cxmMessagesArr = [];
if (is_array($resultArr) || is_object($resultArr)){
  foreach($resultArr as $msgData){
    if (is_array($msgData) || is_object($msgData)){
      foreach ($msgData as $key => $cxmMsgList) {
        $ch1 = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages/'.$cxmMsgList->id); // Initialise cURL
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
        curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization)); // Inject the token into the header
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $cxmMessages = curl_exec($ch1); // Execute the cURL statement
        curl_close($ch1); // Close the cURL connection

        $cxmMessagesArr[] = json_decode($cxmMessages);
      }
    }
  }
}

// echo '<pre>';
// print_r($resultArr);
// print_r($cxmMessagesArr);
// echo '</pre>';

// exit;

?>
<!DOCTYPE html>
<html lang="en">

<?php include($include_path.'include/head.php'); ?>
</head>

<body class="<?php echo BODY_CLASSES; ?>">
<div class="wrapper">
  
  <?php include($include_path.'include/header.php'); ?>
  
  <?php include($include_path.'include/left-bar.php'); ?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Inbox</h1>            
          </div>
          <div class="col-sm-6">
            <?php echo cxmBreadCrumbs('', 'Dashboard'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="<?php echo BASE_URL; ?>compose.php" class="btn btn-navy btn-block cxm-btn-1 mb-3">Compose</a>
          <?php include($include_path.'include/mail-folders.php'); ?>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <?php if(FALSE && !empty($cxmPageToken)){ ?>
            <div class="card">
              <div class="card-header">Token: <?php echo $cxmPageToken; ?></div>
              <div class="card-body">              
              <?php 
              if(isset($cxmPrevPages) && is_array($cxmPrevPages) && count($cxmPrevPages) > 1){
                echo 'Count: '. count($cxmPrevPages).'<hr>';
                print_r($cxmPrevPages);
                echo '<hr>';

                end($cxmPrevPages);
                echo 'Previous: '. prev($cxmPrevPages);
                echo '<hr>';
                echo array_search (prev($cxmPrevPages), $cxmPrevPages);
              }
              ?>
              </div>
            </div>
          <?php } ?>
          <div class="card card-navy card-outline">
            <div class="card-header">
              <div class="row">
                <div class="col-md-4">
                  <h3 class="card-title">Inbox</h3>
                </div>
                <div class="col-md-8">
                  <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm justify-content-end">
                      <?php 
                        if(isset($cxmPrevPages) && is_array($cxmPrevPages) && count($cxmPrevPages) > 1){
                          end($cxmPrevPages);
                      ?>
                        <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>views/email-box/inbox.php?pg=<?php echo prev($cxmPrevPages); ?>&prev=1"><span class="fas fa-chevron-left"></span></a></li>
                      <?php } else {
                        if(!empty($cxmPageToken)){ ?>
                        <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>views/email-box/inbox.php"><span class="fas fa-chevron-left"></span></a></li>
                      <?php }
                      } ?>
                      
                      <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>views/email-box/inbox.php">1</a></li>

                      <?php if(isset($cxmPrevPages) && is_array($cxmPrevPages) && count($cxmPrevPages) > 0){ ?>
                        <?php 
                          $cxmPg = 2;
                          foreach ($cxmPrevPages as $cxmPrevPage) { ?>
                        <li class="page-item <?php echo (strcmp($cxmPrevPage, $cxmPageToken) === 0)?'active' :'cxm'; ?>"><a class="page-link" href="<?php echo BASE_URL; ?>views/email-box/inbox.php?pg=<?php echo $cxmPrevPage; ?>"><?php echo $cxmPg++; ?></a></li>
                        <?php } ?>
                      <?php } ?>

                      <?php if(isset($resultArr->nextPageToken)){ ?>
                        <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>views/email-box/inbox.php?pg=<?php echo $resultArr->nextPageToken; ?>"><span class="fas fa-chevron-right"></span></a></li>
                      <?php } ?>                      
                    </ul>
                  </nav>
                </div>
                <div class="col-md-4 d-none">
                  <div class="card-tools text-right">
                    <?php 
                      if(isset($cxmPrevPages) && is_array($cxmPrevPages) && count($cxmPrevPages) > 1){
                        end($cxmPrevPages);
                    ?>
                      <a href="<?php echo BASE_URL; ?>views/email-box/inbox.php?pg=<?php echo prev($cxmPrevPages); ?>&prev=1" class="btn btn-tool"><i class="fas fa-chevron-left"></i></a>
                    <?php } else {
                      if(!empty($cxmPageToken)){ ?>
                      <a href="<?php echo BASE_URL; ?>views/email-box/inbox.php" class="btn btn-tool"><i class="fas fa-chevron-left"></i></a>
                    <?php }
                    } ?>  
                    <?php if(isset($resultArr->nextPageToken)){ ?>
                    <a href="<?php echo BASE_URL; ?>views/email-box/inbox.php?pg=<?php echo $resultArr->nextPageToken; ?>" class="btn btn-tool"><i class="fas fa-chevron-right"></i></a>
                    <?php } ?>
                  </div> 
                </div>
              </div>

              <?php if($cxmLoginUserEmail['role'] == 'brand'){ ?>
              <form action="<?php echo $curl; ?>" method="GET">
                <div class="row">
                  <div class="col-md-6">                  
                    <div class="input-group input-group-sm">
                      <select name="cxmBrandUser" id="cxm-brand-user" class="form-control">
                        <option value="">Brand Users</option>
                        <?php foreach ($cxmBrandUsers as $cxmBrandUser) { ?>
                          <option value="<?php echo $cxmBrandUser['email']; ?>"<?php echo (isset($_GET['cxmBrandUser']) && $_GET['cxmBrandUser'] == $cxmBrandUser['email'])?' selected="selected"' :''; ?>><?php echo $cxmBrandUser['email']; ?></option>
                        <?php } ?>
                      </select>
                    </div>                    
                  </div>
                  <div class="col-md-6">
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control" placeholder="Search Mail" name="qs" value="<?php echo isset($_GET['qs'])?$_GET['qs'] :''; ?>">
                      <div class="input-group-append">
                        <button type="submit" class="btn btn-navy cxm-btn-1">
                          <i class="fas fa-search"></i>
                        </button>                      
                        <?php if(isset($_GET['qs']) && !empty($_GET['qs']) || !empty($cxmBrandUserActive)){ ?>
                        <span class="input-group-text"><a href="<?php echo BASE_URL; ?>views/email-box/inbox.php"><span class="fas fa-times"></span></a></span>
                        <?php } ?>
                      </div>
                    </div>                        
                  </div>
                </div>
              </form>
              <?php } else { ?>
              <div class="card-tools">
                <form action="<?php echo $curl; ?>" method="GET">
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Search Mail" name="qs" value="<?php echo isset($_GET['qs'])?$_GET['qs'] :''; ?>">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-navy cxm-btn-1">
                        <i class="fas fa-search"></i>
                      </button>                      
                      <?php if(isset($_GET['qs']) && !empty($_GET['qs'])){ ?>
                      <span class="input-group-text"><a href="<?php echo BASE_URL; ?>views/email-box/inbox.php"><span class="fas fa-times"></span></a></span>
                      <?php } ?>
                    </div>
                  </div>
                </form>
              </div>
              <?php } ?>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-controls d-none">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle d-none"><i class="far fa-square"></i></button>
                <div class="btn-group d-none">
                  <button type="button" class="btn btn-default btn-sm"><i class="far fa-trash-alt"></i></button>
                  <button type="button" class="btn btn-default btn-sm"><i class="fas fa-reply"></i></button>
                  <button type="button" class="btn btn-default btn-sm"><i class="fas fa-share"></i></button>
                </div>
                <!-- /.btn-group -->
                <a href="<?php echo BASE_URL; ?>views/email-box/inbox.php" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i></a>
                <div class="float-right">
                  1-50/<?php echo count($cxmMessagesArr); ?>
                    <div class="btn-group">
                      <button type="submit" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i></button>
                      <button type="submit" class="btn btn-default btn-sm"><i class="fas fa-chevron-right"></i></button>
                    </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>
                    <?php
                    $indx = 0;
                    if (!empty($cxmMessagesArr) && is_array($cxmMessagesArr) || is_object($cxmMessagesArr)){
                      foreach($cxmMessagesArr as $Message){
                        
                        $cxmIsUnread = false;
                        if(isset($Message->labelIds)){
                          $cxmLabelIds = $Message->labelIds;
                          foreach($cxmLabelIds as $cxmLabelId){
                            if($cxmLabelId == 'UNREAD'){
                              $cxmIsUnread = true;

                            }
                          }
                        }
                        
                        // $cxmLabels = [];
                        // if(isset($Message->labelIds)){
                        //   $cxmLabelIds = $Message->labelIds;
                        //   foreach($cxmLabelIds as $cxmLabelId){
                        //     $ch3 = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/labels/'.$cxmLabelId); // Initialise cURL
                        //     $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
                        //     curl_setopt($ch3, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
                        //     curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
                        //     curl_setopt($ch3, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
                        //     $cxmLabel = curl_exec($ch3); // Execute the cURL statement
                        //     curl_close($ch3); // Close the cURL connection

                        //     $cxmLabels[] = json_decode($cxmLabel);
                        //   }
                        // }
                        // if(in_array('INBOX', $Message->labelIds)){
                        if(TRUE){
                          $cxmEmailHeaders = [];
                          if (!empty($Message->payload->headers) && is_array($Message->payload->headers) || is_object($Message->payload->headers)){
                            foreach ($Message->payload->headers as $header) {
                              if($header->name === 'To'){
                                $cxmEmailHeaders[$header->name] = $header->value;
                              }
                              if($header->name === 'Cc'){
                                $cxmEmailHeaders[$header->name] = $header->value;
                              }
                              if($header->name === 'From'){
                                $cxmEmailHeaders[$header->name] = $header->value;
                              }
                              if($header->name === 'Subject'){
                                $cxmEmailHeaders[$header->name] = $header->value;
                              }  
                              if($header->name === 'Date'){
                                $cxmEmailHeaders[$header->name] = $header->value;
                              }  
                            }
                          }  

                          // GET FROM EMAIL ADDRESS:
                          preg_match('/<(.*?)>/', $cxmEmailHeaders['From'], $cxmFromEmailMatch);
                          if(isset($cxmFromEmailMatch[1])){
                            $cxmEmailHeaders['FromEmail'] = $cxmFromEmailMatch[1];

                          } else {
                            $cxmEmailHeaders['FromEmail'] = 'Not Found';

                          }

                          if(FALSE){
                            $cxmEmailBodyPure = '<div class="alert alert-danger text-center">Empty body</div>';
                            if($Message->payload->body->size > 0){
                              // echo $Message->payload->body->size;

                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            }

                            if(isset($Message->payload->parts) && $Message->payload->parts[1]->body->size > 0 && isset($Message->payload->parts[1]->body->data)){
                              // echo $Message->payload->parts[1]->body->size;

                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->parts[1]->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            } else if(isset($Message->payload->parts) && $Message->payload->parts[0]->body->size > 0) {
                              // echo $Message->payload->parts[0]->body->size;

                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->parts[0]->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            }

                            if(isset($Message->payload->parts[0]->parts) && $Message->payload->parts[0]->parts[1]->body->size > 0 && isset($Message->payload->parts[0]->parts[1]->body->data)){
                              // echo $Message->payload->parts[0]->parts[1]->body->size;

                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->parts[0]->parts[1]->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            } else if(isset($Message->payload->parts[0]->parts) && $Message->payload->parts[0]->parts[0]->body->size > 0) {
                              // echo $Message->payload->parts[0]->parts[0]->body->size;

                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->parts[0]->parts[0]->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            }

                            if(isset($Message->payload->parts[0]->parts[0]->parts) && $Message->payload->parts[0]->parts[0]->parts[1]->body->size > 0 && isset($Message->payload->parts[0]->parts[0]->parts[1]->body->data)){
                              // echo $Message->payload->parts[0]->parts[0]->parts[1]->body->size;
                              
                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->parts[0]->parts[0]->parts[1]->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            } else if(isset($Message->payload->parts[0]->parts[0]->parts) && $Message->payload->parts[0]->parts[0]->parts[0]->body->size > 0) {
                              // echo $Message->payload->parts[0]->parts[0]->parts[0]->body->size;
                              
                              $cxmEmailBodyUpdate = str_replace('-', '+', $Message->payload->parts[0]->parts[0]->parts[0]->body->data);
                              $cxmEmailBodyDecode = str_replace('_', '/', $cxmEmailBodyUpdate);
                              $cxmEmailBodyPure = base64_decode($cxmEmailBodyDecode);

                            }
                          }  

                          $cxmAttachments = [];
                          $cxmAttachment = false;
                          if(isset($Message->payload->parts)){
                            $cxmParts = $Message->payload->parts;
                            foreach($cxmParts as $cxmPart){
                              if(isset($cxmPart->body->attachmentId)){
                                // $ch2 = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages/'.$Message->id.'/attachments/'.$cxmPart->body->attachmentId); // Initialise cURL
                                // $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
                                // curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
                                // curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                                // curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
                                // $cxmAttachment = curl_exec($ch2); // Execute the cURL statement
                                // curl_close($ch2); // Close the cURL connection

                                // $cxmAttachments[] = json_decode($cxmAttachment);
                                $cxmAttachment = true;
                              }
                            }
                          }

                          ?>
                          <tr class="position-relative<?php echo ($cxmIsUnread == true)?' table-info' :''; ?>">
                            <td class="d-none">
                              <div class="icheck-navy">
                                <input type="checkbox" value="<?php echo $Message->id; ?>" id="check1">
                                <label for="check1"></label>
                              </div>
                            </td>
                            <td class="mailbox-star d-none"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                            <td class="d-none"><span class="badge badge-dark rounded-pill"><?php echo ++$indx; ?></span></td>
                            <?php if(FALSE){ ?>
                            <td class="xd-none"><?php print_r($Message->labelIds); ?>
                              <?php foreach($cxmLabels as $label){ ?>
                                <?php 
                                // echo '<pre>'; 
                                // print_r($label);
                                // echo '</pre>';
                                ?>
                                <span class="badge badge-warning rounded-pill"><?php echo $label->name; ?></span>
                              <?php } ?>
                            </td>
                            <?php } ?>

                            <td class="mailbox-name w-25">
                              <div class="bg-gradient-navy rounded">
                              <?php if(isset($cxmEmailHeaders['To'])){ ?>
                                <span class="badge xbg-gradient-orange text-wrap text-left">To: <?php echo $cxmEmailHeaders['To']; ?></span>
                              <?php } ?>

                              <?php if(isset($cxmEmailHeaders['Cc'])){ ?>
                                <span class="badge xbg-gradient-orange text-wrap text-left">Cc: <?php echo $cxmEmailHeaders['Cc']; ?></span>
                              <?php } ?>

                              <?php if(isset($cxmEmailHeaders['From'])){ ?>
                                <span class="badge xbg-gradient-orange text-wrap text-left">From: <?php echo $cxmEmailHeaders['From']; ?> &lt;<?php echo isset($cxmEmailHeaders['FromEmail'])?$cxmEmailHeaders['FromEmail'] :'From not found'; ?>&gt;</span>
                              <?php } ?>

                              <?php if(isset($cxmEmailHeaders['Date'])){ ?>
                                <span class="badge xbg-gradient-orange text-wrap text-left">Date: <?php echo $cxmEmailHeaders['Date']; ?></span>
                              <?php } ?>

                              <?php if(isset($cxmEmailHeaders['Subject'])){ ?>
                                <span class="badge xbg-gradient-orange text-wrap text-left">Subject: <?php echo $cxmEmailHeaders['Subject']; ?></span>
                              <?php } ?>
                              </div>
                            </td>
                            <td class="mailbox-subject"><?php echo html_entity_decode(substr($Message->snippet, 0, 200).((strlen($Message->snippet) > 200)?'...' :'')); ?>
                            </td>
                            <td class="mailbox-attachment align-middle">
                              <?php 
                                // if(count($cxmAttachments) > 0){
                                if($cxmAttachment == true){
                                  // foreach ($cxmAttachments as $cxmAttachmentKey => $cxmAttachment) {
                                  //   $cxmAttachmentFile = str_replace('-', '+', $cxmAttachment->data);
                                  //   $cxmAttachmentFile = str_replace('_', '/', $cxmAttachmentFile);
                                  ?>
                                    <!-- <a href="#" class="cxm-attach-file d-none" data-toggle="modal" data-target=".cxmAttachmentModal">
                                      <img width="50" src="data:image/png;base64, <?php // echo $cxmAttachmentFile; ?>" alt="Attachment Image" class="img-thumbnail mb-1">
                                    </a> -->
                                    <span class="badge badge-success rounded-pill">Attachment</span>
                                  <?php 
                                  // }                                
                                } else { ?>
                                  <span class="badge badge-warning rounded-pill">No Attachment</span>                                

                                <?php } ?>
                            </td>
                            <td class="align-middle">
                              <div class="text-nowrap">
                                <?php if($cxmIsUnread == true){ ?>
                                <a href="<?php echo BASE_URL; ?>views/email-box/read-mail.php?msgid=<?php echo $Message->id; ?>" class="btn btn-navy btn-sm cxm-btn-1 stretched-link"><span class="fas fa-envelope"></span></a>
                                <?php }else{ ?>
                                  <a href="<?php echo BASE_URL; ?>views/email-box/read-mail.php?msgid=<?php echo $Message->id; ?>" class="btn btn-navy btn-sm cxm-btn-1 stretched-link"><span class="fas fa-envelope-open"></span></a>
                                <?php } ?>
                                <?php if(FALSE){ ?>
                                <a href="#" class="btn btn-navy btn-sm cxm-btn-1" data-toggle="modal" data-target="#cxmEmailModal<?php echo $Message->id; ?>"><span class="fas fa-eye"></span></a>
                                <?php } ?>
                              </div>
                              <?php if(FALSE){ ?>
                              <!-- cxmEmailModal -->
                              <div class="modal fade" id="cxmEmailModal<?php echo $Message->id; ?>" tabindex="-1" aria-labelledby="cxmEmailModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                  <div class="modal-content">
                                    <div class="modal-header rounded-0">
                                      <h5 class="modal-title" id="cxmEmailModalLabel">Email Body</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                                      <?php echo preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si', ' ', $cxmEmailBodyPure); ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <?php } ?>
                            </td>
                          </tr>
                          <?php 
                        } 
                      }
                    } else { ?>
                      <div class="alert alert-danger text-center m-3">No Record Found</div>
                    <?php } ?>        
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0 d-none">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                  <i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
    </div>
  </div>

  <!-- cxm Attachment Modal -->
  <div class="modal fade cxmAttachmentModal" id="cxmAttachmentModal" tabindex="-1" aria-labelledby="cxmAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header rounded-0">
          <h5 class="modal-title" id="cxmAttachmentModalLabel">Attachment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <img src="<?php echo BASE_URL; ?>media/bg.jpg" alt="Attachment" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
  
    <?php include($include_path.'include/footer.php'); ?>
    </div>

    <?php include($include_path.'include/footer-scripts.php'); ?>

    <script>      
      $('[data-toggle="tooltip"]').tooltip();

      $('.cxm-attach-file').on('click', function() {
        let cxmAttachFile = $(this).find('img').attr('src');
        $('#cxmAttachmentModal').find('.modal-body img').attr('src', cxmAttachFile);
      });
    </script>

    </body>
</html>